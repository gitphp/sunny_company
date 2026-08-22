<?php

/**
 * 文件上传服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

use App\Support\Snowflake;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class UploadService
{
    /**
     * @return array<string, mixed>
     */
    public function store(UploadedFile $file, string $scene = 'products'): array
    {
        $folder = $scene === 'ads' ? 'ads' : 'products';
        $this->assertAllowed($file, $folder === 'ads');
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $directory = $folder.'/'.date('Y/m/d');
        $filename = Snowflake::id().($extension !== '' ? '.'.$extension : '');
        $path = $file->storeAs($directory, $filename, 'public');

        if ($path === false) {
            throw ValidationException::withMessages([
                'file' => ['文件保存失败'],
            ]);
        }

        return [
            'message' => '上传成功',
            'file' => [
                'file_url' => '/storage/'.$path,
                'file_name' => mb_substr((string) $file->getClientOriginalName(), 0, 255),
                'file_key' => $path,
                'storage_provider' => 'local',
                'extension' => mb_substr($extension, 0, 16),
                'file_size' => (int) $file->getSize(),
                'file_type' => mb_substr((string) ($file->getMimeType() ?: ''), 0, 32),
            ],
        ];
    }

    private function assertAllowed(UploadedFile $file, bool $imageOnly = false): void
    {
        $mime = (string) ($file->getMimeType() ?: '');
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $images = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $allowed = [
            ...$images,
            'mp4', 'webm', 'mov',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar',
        ];

        if ($imageOnly) {
            if (! in_array($extension, $images, true) && ! str_starts_with($mime, 'image/')) {
                throw ValidationException::withMessages([
                    'file' => ['请上传 jpg / png / gif / webp 图片'],
                ]);
            }

            return;
        }

        if (! in_array($extension, $allowed, true) && ! str_starts_with($mime, 'image/')) {
            throw ValidationException::withMessages([
                'file' => ['不支持的文件类型'],
            ]);
        }
    }
}
