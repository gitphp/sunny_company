{{--
/**
 * 后台入口视图
 *
 * @package     Resources\Views
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
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>名杨科技管理系统</title>
        @vite(['resources/css/backend.css', 'resources/js/backend/app.js'])
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
