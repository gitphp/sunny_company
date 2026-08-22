{{--
/**
 * 业务异常页面
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
        <title>提示 · 名杨科技</title>
        <style>
            body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #fffcf7; color: #1c2420; font-family: "Noto Sans SC", sans-serif; }
            main { width: min(520px, calc(100% - 40px)); padding: 40px; border-radius: 24px; background: #fff; border: 1px solid rgba(28, 36, 32, .08); }
            p { color: #5b675f; line-height: 1.7; }
            a { color: #d97816; }
        </style>
    </head>
    <body>
        <main>
            <h1>操作无法完成</h1>
            <p>{{ $message }}</p>
            <p><a href="javascript:history.back()">返回上一页</a></p>
        </main>
    </body>
</html>
