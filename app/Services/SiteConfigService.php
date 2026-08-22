<?php

/**
 * 站点配置服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Enums\ConfigGroup;
use App\Models\SiteConfig;
use Illuminate\Support\Facades\DB;

class SiteConfigService
{
    /**
     * @return array<string, mixed>
     */
    public function grouped(): array
    {
        $items = SiteConfig::query()
            ->orderBy('conf_group')
            ->orderByDesc('conf_sort')
            ->orderBy('id')
            ->get();

        $groups = [];

        foreach (ConfigGroup::cases() as $group) {
            $groups[] = [
                'group' => $group->value,
                'group_label' => $group->label(),
                'items' => $items
                    ->filter(fn (SiteConfig $config): bool => $config->conf_group === $group)
                    ->map(fn (SiteConfig $config) => $this->transform($config))
                    ->values(),
            ];
        }

        return ['groups' => $groups];
    }

    /**
     * @return array<string, string>
     */
    public function publicValues(): array
    {
        return SiteConfig::query()
            ->get(['conf_key', 'conf_value'])
            ->mapWithKeys(fn (SiteConfig $config): array => [
                $config->conf_key => (string) ($config->conf_value ?? ''),
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public function save(array $values): array
    {
        DB::transaction(function () use ($values): void {
            foreach ($values as $key => $value) {
                SiteConfig::query()
                    ->where('conf_key', (string) $key)
                    ->update(['conf_value' => is_scalar($value) || $value === null ? (string) ($value ?? '') : json_encode($value, JSON_UNESCAPED_UNICODE)]);
            }
        });

        return [
            'message' => '保存成功',
            ...$this->grouped(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function transform(SiteConfig $config): array
    {
        return [
            'id' => (string) $config->id,
            'conf_group' => $config->conf_group?->value,
            'conf_key' => $config->conf_key,
            'conf_value' => (string) ($config->conf_value ?? ''),
            'conf_desc' => $config->conf_desc,
            'input_type' => $config->input_type?->value,
            'conf_sort' => $config->conf_sort,
        ];
    }
}
