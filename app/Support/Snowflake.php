<?php

/**
 * 雪花ID工具类
 *
 * @package     App\Support
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Support;

final class Snowflake
{
    private const EPOCH = 1_704_067_200_000;

    private static int $sequence = 0;

    private static int $lastTimestamp = -1;

    public static function id(): string
    {
        $timestamp = self::timestamp();
        $datacenterId = ((int) config('app.snowflake_datacenter_id', 1)) & 0x1F;
        $workerId = ((int) config('app.snowflake_worker_id', 1)) & 0x1F;

        if ($timestamp === self::$lastTimestamp) {
            self::$sequence = (self::$sequence + 1) & 0xFFF;

            if (self::$sequence === 0) {
                while ($timestamp <= self::$lastTimestamp) {
                    $timestamp = self::timestamp();
                }
            }
        } else {
            self::$sequence = 0;
        }

        self::$lastTimestamp = $timestamp;

        $id = (($timestamp - self::EPOCH) << 22)
            | ($datacenterId << 17)
            | ($workerId << 12)
            | self::$sequence;

        return (string) $id;
    }

    private static function timestamp(): int
    {
        return (int) floor(microtime(true) * 1000);
    }
}
