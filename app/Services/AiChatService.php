<?php

/**
 * AI对话服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Exceptions\SystemException;
use App\Models\AiProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AiChatService
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function complete(array $data): array
    {
        $provider = $this->resolve($data);
        $response = $this->request($provider, $this->messages($provider, $data['messages'] ?? []), false);

        if (! $response->successful()) {
            $this->throwRemoteError($response);
        }

        $content = (string) data_get($response->json(), 'choices.0.message.content', '');

        if ($content === '') {
            BusinessException::fail('模型未返回内容，请检查配置或稍后重试', 'messages');
        }

        return [
            'content' => $content,
            'model' => (string) data_get($response->json(), 'model', $provider->model),
            'provider_id' => (string) $provider->id,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function stream(array $data): StreamedResponse
    {
        $provider = $this->resolve($data);
        $messages = $this->messages($provider, $data['messages'] ?? []);

        return response()->stream(function () use ($provider, $messages): void {
            try {
                $response = $this->request($provider, $messages, true);

                if (! $response->successful()) {
                    if ($response->serverError()) {
                        Log::error('AI 模型接口失败', [
                            'status' => $response->status(),
                            'body' => $this->errorMessage($response),
                        ]);
                        $this->emit(['error' => '系统繁忙，请稍后重试']);

                        return;
                    }

                    $this->emit(['error' => $this->errorMessage($response)]);

                    return;
                }

                $body = $response->toPsrResponse()->getBody();

                while (! $body->eof()) {
                    echo $body->read(1024);

                    if (ob_get_level() > 0) {
                        ob_flush();
                    }

                    flush();
                }
            } catch (BusinessException $exception) {
                $this->emit(['error' => $exception->getMessage()]);
            } catch (Throwable $exception) {
                Log::error('AI 流式对话失败', ['exception' => $exception]);
                $this->emit(['error' => '系统繁忙，请稍后重试']);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=utf-8',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function test(string $id): array
    {
        $result = $this->complete([
            'provider_id' => $id,
            'messages' => [
                ['role' => 'user', 'content' => '只回复两个字：成功'],
            ],
        ]);

        return [
            'message' => '连接成功',
            'content' => $result['content'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolve(array $data): AiProvider
    {
        $query = AiProvider::query()->where('status', 1);

        if (($data['provider_id'] ?? '') !== '') {
            $provider = $query->find($data['provider_id']);
        } else {
            $provider = $query->where('is_default', 1)->first()
                ?? $query->orderByDesc('sort_order')->first();
        }

        if (! $provider) {
            BusinessException::fail('请先配置并启用一个模型', 'provider_id');
        }

        $key = (string) $provider->getRawOriginal('api_key');
        $local = str_contains($provider->base_url, '127.0.0.1') || str_contains($provider->base_url, 'localhost');

        if ($key === '' && ! $local) {
            BusinessException::fail('请先为「'.$provider->provider_name.'」填写 API Key', 'provider_id');
        }

        if (trim((string) $provider->base_url) === '' || trim((string) $provider->model) === '') {
            BusinessException::fail('请完善接口地址和模型名称', 'provider_id');
        }

        return $provider;
    }

    /**
     * @param  list<array<string, mixed>>  $messages
     * @return list<array{role: string, content: string}>
     */
    private function messages(AiProvider $provider, array $messages): array
    {
        $normalized = [];

        foreach (array_slice($messages, -20) as $item) {
            $role = (string) ($item['role'] ?? '');
            $content = trim((string) ($item['content'] ?? ''));

            if (! in_array($role, ['user', 'assistant', 'system'], true) || $content === '') {
                continue;
            }

            $normalized[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        if ($normalized === []) {
            BusinessException::fail('请输入对话内容', 'messages');
        }

        if (trim((string) $provider->system_prompt) !== '' && ($normalized[0]['role'] ?? '') !== 'system') {
            array_unshift($normalized, [
                'role' => 'system',
                'content' => (string) $provider->system_prompt,
            ]);
        }

        return $normalized;
    }

    /**
     * @param  list<array{role: string, content: string}>  $messages
     */
    private function request(AiProvider $provider, array $messages, bool $stream): Response
    {
        try {
            $request = Http::acceptJson()
                ->timeout(120)
                ->connectTimeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer '.(string) $provider->getRawOriginal('api_key'),
                ]);

            if ($stream) {
                $request = $request->withOptions(['stream' => true]);
            }

            return $request->post($this->chatUrl((string) $provider->base_url), [
                'model' => $provider->model,
                'messages' => $messages,
                'temperature' => (float) $provider->temperature,
                'max_tokens' => (int) $provider->max_tokens,
                'stream' => $stream,
            ]);
        } catch (ConnectionException $exception) {
            SystemException::fail('模型服务暂时不可用', $exception);
        }
    }

    private function chatUrl(string $baseUrl): string
    {
        $url = rtrim($baseUrl, '/');

        if (str_ends_with($url, '/chat/completions')) {
            return $url;
        }

        if (str_ends_with($url, '/v1')) {
            return $url.'/chat/completions';
        }

        return $url.'/v1/chat/completions';
    }

    private function throwRemoteError(Response $response): never
    {
        if ($response->serverError()) {
            SystemException::fail($this->errorMessage($response));
        }

        BusinessException::fail($this->errorMessage($response), 'messages');
    }

    private function errorMessage(Response $response): string
    {
        $message = data_get($response->json(), 'error.message')
            ?? data_get($response->json(), 'message')
            ?? $response->body();

        $text = is_string($message) && $message !== '' ? $message : '模型接口请求失败';

        return mb_substr($text, 0, 300);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function emit(array $payload): void
    {
        echo 'data: '.json_encode($payload, JSON_UNESCAPED_UNICODE)."\n\n";

        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }
}
