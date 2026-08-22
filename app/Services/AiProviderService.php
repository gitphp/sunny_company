<?php

namespace App\Services;

use App\Models\AiProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AiProviderService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function paginate(array $filters): array
    {
        $paginator = AiProvider::query()
            ->when(($filters['provider_name'] ?? '') !== '', fn (Builder $query) => $query->where('provider_name', 'like', '%'.$filters['provider_name'].'%'))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->orderByDesc('is_default')
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->paginate((int) ($filters['per_page'] ?? 20));

        return [
            'data' => collect($paginator->items())->map(fn (AiProvider $provider) => $this->transform($provider))->values(),
            'presets' => $this->presets(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function options(): array
    {
        $providers = AiProvider::query()
            ->where('status', 1)
            ->orderByDesc('is_default')
            ->orderByDesc('sort_order')
            ->get();

        return [
            'providers' => $providers->map(fn (AiProvider $provider) => $this->transform($provider))->values(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $provider = DB::transaction(function () use ($data): AiProvider {
            $provider = AiProvider::query()->create($this->payload($data, true));
            $this->syncDefault($provider);

            return $provider->fresh();
        });

        return [
            'message' => '新增成功',
            'provider' => $this->transform($provider),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $provider = DB::transaction(function () use ($id, $data): AiProvider {
            $provider = AiProvider::query()->findOrFail($id);
            $provider->fill($this->payload($data, false, $provider))->save();
            $this->syncDefault($provider->fresh());

            return $provider->fresh();
        });

        return [
            'message' => '修改成功',
            'provider' => $this->transform($provider),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function delete(string $id): array
    {
        $provider = AiProvider::query()->findOrFail($id);

        if ($provider->is_default && AiProvider::query()->where('status', 1)->where('id', '!=', $id)->doesntExist()) {
            throw ValidationException::withMessages([
                'id' => ['请至少保留一个可用模型'],
            ]);
        }

        $provider->delete();

        if ($provider->is_default) {
            $next = AiProvider::query()->where('status', 1)->orderByDesc('sort_order')->first();
            $next?->forceFill(['is_default' => 1])->save();
        }

        return ['message' => '删除成功'];
    }

    /**
     * @return array<string, mixed>
     */
    public function changeStatus(string $id, int $status): array
    {
        $provider = AiProvider::query()->findOrFail($id);
        $provider->forceFill(['status' => $status])->save();

        if ($status === 0 && $provider->is_default) {
            $provider->forceFill(['is_default' => 0])->save();
            $next = AiProvider::query()->where('status', 1)->where('id', '!=', $id)->orderByDesc('sort_order')->first();
            $next?->forceFill(['is_default' => 1])->save();
        }

        return [
            'message' => '状态已更新',
            'provider' => $this->transform($provider->fresh()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function setDefault(string $id): array
    {
        $provider = AiProvider::query()->findOrFail($id);

        if ((int) $provider->status !== 1) {
            throw ValidationException::withMessages([
                'id' => ['请先启用该模型再设为默认'],
            ]);
        }

        DB::transaction(function () use ($provider): void {
            AiProvider::query()->where('is_default', 1)->update(['is_default' => 0]);
            $provider->forceFill(['is_default' => 1])->save();
        });

        return [
            'message' => '已设为默认模型',
            'provider' => $this->transform($provider->fresh()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data, bool $creating, ?AiProvider $current = null): array
    {
        $apiKey = trim((string) ($data['api_key'] ?? ''));

        if ($creating && $apiKey === '' && ! $this->isLocal((string) ($data['base_url'] ?? ''))) {
            throw ValidationException::withMessages([
                'api_key' => ['请填写接口密钥'],
            ]);
        }

        if (! $creating && ($apiKey === '' || str_contains($apiKey, '*'))) {
            $apiKey = (string) ($current?->getRawOriginal('api_key') ?? '');
        }

        return [
            'provider_name' => (string) ($data['provider_name'] ?? ''),
            'driver' => (string) ($data['driver'] ?? 'openai'),
            'base_url' => rtrim((string) ($data['base_url'] ?? ''), '/'),
            'api_key' => $apiKey,
            'model' => (string) ($data['model'] ?? ''),
            'temperature' => round((float) ($data['temperature'] ?? 0.7), 2),
            'max_tokens' => (int) ($data['max_tokens'] ?? 2048),
            'system_prompt' => (string) ($data['system_prompt'] ?? ''),
            'is_default' => (int) ($data['is_default'] ?? 0),
            'status' => (int) ($data['status'] ?? 1),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }

    private function syncDefault(AiProvider $provider): void
    {
        if ((int) $provider->is_default !== 1) {
            if (! AiProvider::query()->where('is_default', 1)->where('status', 1)->exists()) {
                $provider->forceFill(['is_default' => 1])->save();
            }

            return;
        }

        AiProvider::query()->where('id', '!=', $provider->id)->update(['is_default' => 0]);
    }

    private function isLocal(string $url): bool
    {
        return str_contains($url, '127.0.0.1') || str_contains($url, 'localhost');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function presets(): array
    {
        return [
            ['provider_name' => 'DeepSeek', 'base_url' => 'https://api.deepseek.com', 'model' => 'deepseek-chat', 'system_prompt' => '你是名杨科技管理系统的智能助手，回答简洁、准确。'],
            ['provider_name' => 'DeepSeek Reasoner', 'base_url' => 'https://api.deepseek.com', 'model' => 'deepseek-reasoner', 'system_prompt' => '你是名杨科技管理系统的智能助手，回答需条理清晰。'],
            ['provider_name' => 'OpenAI', 'base_url' => 'https://api.openai.com/v1', 'model' => 'gpt-4o-mini', 'system_prompt' => ''],
            ['provider_name' => '通义千问', 'base_url' => 'https://dashscope.aliyuncs.com/compatible-mode/v1', 'model' => 'qwen-plus', 'system_prompt' => ''],
            ['provider_name' => 'Kimi', 'base_url' => 'https://api.moonshot.cn/v1', 'model' => 'moonshot-v1-8k', 'system_prompt' => ''],
            ['provider_name' => 'Ollama 本地', 'base_url' => 'http://127.0.0.1:11434/v1', 'model' => 'llama3.2', 'system_prompt' => ''],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function transform(AiProvider $provider): array
    {
        $key = (string) $provider->getRawOriginal('api_key');

        return [
            'id' => (string) $provider->id,
            'provider_name' => $provider->provider_name,
            'driver' => $provider->driver,
            'base_url' => $provider->base_url,
            'api_key' => $this->maskKey($key),
            'has_key' => $key !== '',
            'model' => $provider->model,
            'temperature' => (float) $provider->temperature,
            'max_tokens' => (int) $provider->max_tokens,
            'system_prompt' => $provider->system_prompt,
            'is_default' => (int) $provider->is_default,
            'status' => (int) $provider->status,
            'sort_order' => (int) $provider->sort_order,
        ];
    }

    private function maskKey(string $key): string
    {
        if ($key === '') {
            return '';
        }

        $length = strlen($key);

        if ($length <= 8) {
            return str_repeat('*', $length);
        }

        return substr($key, 0, 3).str_repeat('*', max(4, $length - 7)).substr($key, -4);
    }
}
