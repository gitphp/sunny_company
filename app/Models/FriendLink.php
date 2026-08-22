<?php

/**
 * 友情链接模型
 *
 * @package     App\Models
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Models;

use App\Support\Snowflake;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'link_name',
    'link_url',
    'link_logo',
    'link_desc',
    'link_sort',
    'link_status',
])]
class FriendLink extends Model
{
    protected $table = 'friend_links';

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (FriendLink $link): void {
            if (empty($link->id)) {
                $link->id = Snowflake::id();
            }
        });
    }
}
