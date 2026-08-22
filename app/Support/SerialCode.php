<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

final class SerialCode
{
    /**
     * @param  class-string<Model>  $model
     */
    public static function next(string $model, string $column, string $prefix, int $width = 6): string
    {
        $max = $model::withTrashed()
            ->where($column, 'like', $prefix.'%')
            ->pluck($column)
            ->reduce(function (int $carry, mixed $code) use ($prefix): int {
                $number = (int) substr((string) $code, strlen($prefix));

                return max($carry, $number);
            }, 0);

        return $prefix.str_pad((string) ($max + 1), $width, '0', STR_PAD_LEFT);
    }
}
