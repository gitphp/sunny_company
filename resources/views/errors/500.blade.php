{{--
/**
 * 系统异常页面
 *
 * @package     Resources\Views\Errors
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */
--}}
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>系统繁忙 · 名杨科技</title>
        <style>
            body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #161c19; color: #d7ddd9; font-family: "Noto Sans SC", sans-serif; }
            main { width: min(520px, calc(100% - 40px)); padding: 40px; border-radius: 24px; background: #1c2420; }
            p { color: #9aa59e; line-height: 1.7; }
            a { color: #e08a1e; }
        </style>
    </head>
    <body>
        <main>
            <h1>系统繁忙</h1>
            <p>{{ $message ?? '系统繁忙，请稍后重试' }}</p>
            <p><a href="/">返回首页</a></p>
        </main>
    </body>
</html>
