<?php

namespace App\Http\Middleware;

use App\Services\OperationLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RecordOperationLog
{
    public function __construct(private readonly OperationLogService $logs) {}

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (! $this->shouldRecord($request)) {
            return;
        }

        try {
            $this->logs->write($this->payload($request, $response));
        } catch (Throwable) {
            // 操作日志失败不影响主流程
        }
    }

    private function shouldRecord(Request $request): bool
    {
        if (! in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        $path = $request->path();

        if (str_contains($path, 'operation-logs') || str_ends_with($path, '/logout') || str_contains($path, '/options/')) {
            return false;
        }

        return str_starts_with($path, 'api/admin');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, Response $response): array
    {
        $user = $request->user();
        $success = $response->getStatusCode() < 400;
        $resource = $this->resource($request);
        $action = $this->action($request);
        $body = $this->decodeResponse($response);

        return [
            'operator_id' => $user?->id ?? 0,
            'operator_name' => (string) ($user?->real_name ?: $user?->user_name ?: ''),
            'biz_type' => $resource,
            'activity_type' => $resource.'_'.strtolower($action === 'INSERT' ? 'created' : ($action === 'DELETE' ? 'deleted' : 'updated')),
            'action' => $action,
            'biz_id' => $this->bizId($request),
            'biz_label' => $this->label($request, $body),
            'new_value' => $this->redact($request->except(['password', 'password_hash', 'password_confirmation'])),
            'operator_status' => $success ? 1 : 0,
            'error_msg' => $success ? '' : (string) ($body['message'] ?? ''),
            'client_ip' => (string) $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'request_url' => mb_substr($request->fullUrl(), 0, 255),
            'method_fun' => (string) ($request->route()?->getActionName() ?? ''),
        ];
    }

    private function resource(Request $request): string
    {
        $path = trim(str_replace('api/admin', '', $request->path()), '/');
        $segment = explode('/', $path)[0] ?? '';

        return match ($segment) {
            'users' => 'user',
            'roles' => 'role',
            'departments' => 'dept',
            'posts' => 'post',
            'articles' => 'article',
            'article-categories' => 'category',
            'friend-links' => 'link',
            'feedbacks' => 'feedback',
            'site-configs' => 'config',
            'ad-positions' => 'ad_position',
            'ad-materials' => 'ad_material',
            default => mb_substr($segment, 0, 16),
        };
    }

    private function action(Request $request): string
    {
        $path = $request->path();

        if (str_contains($path, 'batch-delete') || strtoupper($request->method()) === 'DELETE') {
            return 'DELETE';
        }

        return match (strtoupper($request->method())) {
            'POST' => 'INSERT',
            'PUT', 'PATCH' => 'UPDATE',
            default => strtoupper($request->method()),
        };
    }

    private function bizId(Request $request): string
    {
        $parameters = array_reverse($request->route()?->parameters() ?? []);

        foreach ($parameters as $value) {
            if (is_object($value) && method_exists($value, 'getKey')) {
                return (string) $value->getKey();
            }

            if (is_scalar($value) && preg_match('/^\d+$/', (string) $value)) {
                return (string) $value;
            }
        }

        $ids = $request->input('ids');

        return is_array($ids) && isset($ids[0]) ? (string) $ids[0] : '0';
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function label(Request $request, array $body): string
    {
        foreach (['title', 'user_name', 'role_name', 'dept_name', 'post_name', 'link_name', 'cat_name', 'fb_title', 'menu_name', 'pos_name'] as $field) {
            $value = $request->input($field);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        $message = $body['message'] ?? '';

        return is_string($message) ? $message : '';
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(Response $response): array
    {
        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return [];
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function redact(array $data): array
    {
        foreach (['password', 'password_hash', 'password_confirmation'] as $key) {
            unset($data[$key]);
        }

        return $data;
    }
}
