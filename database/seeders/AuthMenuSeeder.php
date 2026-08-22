<?php

namespace Database\Seeders;

use App\Models\AuthMenu;
use App\Support\Snowflake;
use Illuminate\Database\Seeder;

class AuthMenuSeeder extends Seeder
{
    public function run(): void
    {
        if (AuthMenu::query()->exists()) {
            return;
        }

        $home = $this->createMenu([
            'menu_name' => '首页',
            'menu_icon' => 'HomeFilled',
            'menu_path' => '/index',
            'component' => 'dashboard/Index',
            'menu_sort' => 990,
        ]);

        $ai = $this->createMenu([
            'menu_name' => 'AI对话',
            'menu_icon' => 'ChatDotRound',
            'menu_path' => '/ai',
            'component' => 'ai/Index',
            'permission_code' => 'ai:chat',
            'menu_sort' => 980,
        ]);

        $this->createMenu([
            'menu_name' => '模型配置',
            'permission_code' => 'ai:config',
            'menu_sort' => 40,
        ], $ai->id);

        $system = $this->createMenu([
            'menu_name' => '系统管理',
            'menu_icon' => 'Setting',
            'menu_path' => '/system',
            'component' => '',
            'menu_sort' => 900,
        ]);

        $user = $this->createMenu([
            'menu_name' => '用户管理',
            'menu_icon' => 'User',
            'menu_path' => '/system/user',
            'component' => 'system/user/Index',
            'permission_code' => 'system:user:list',
            'menu_sort' => 90,
        ], $system->id);

        foreach ([
            ['新增', 'system:user:add', 50],
            ['修改', 'system:user:edit', 40],
            ['删除', 'system:user:remove', 30],
            ['导入', 'system:user:import', 20],
            ['导出', 'system:user:export', 10],
            ['重置密码', 'system:user:resetPwd', 5],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $user->id);
        }

        $this->createMenu([
            'menu_name' => '角色管理',
            'menu_icon' => 'UserFilled',
            'menu_path' => '/system/role',
            'component' => 'placeholder/Index',
            'menu_sort' => 80,
        ], $system->id);

        $this->createMenu([
            'menu_name' => '菜单管理',
            'menu_icon' => 'Menu',
            'menu_path' => '/system/menu',
            'component' => 'system/menu/Index',
            'menu_sort' => 70,
        ], $system->id);

        $this->createMenu([
            'menu_name' => '部门管理',
            'menu_icon' => 'OfficeBuilding',
            'menu_path' => '/system/dept',
            'component' => 'placeholder/Index',
            'menu_sort' => 60,
        ], $system->id);

        $post = $this->createMenu([
            'menu_name' => '岗位管理',
            'menu_icon' => 'Postcard',
            'menu_path' => '/system/post',
            'component' => 'system/post/Index',
            'permission_code' => 'system:post:list',
            'menu_sort' => 50,
        ], $system->id);

        foreach ([
            ['新增', 'system:post:add', 50],
            ['修改', 'system:post:edit', 40],
            ['删除', 'system:post:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $post->id);
        }

        foreach ([
            ['字典管理', 'Collection', '/system/dict', 40],
            ['参数设置', 'EditPen', '/system/config', 30],
            ['通知公告', 'Bell', '/system/notice', 20],
        ] as [$name, $icon, $path, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'menu_icon' => $icon,
                'menu_path' => $path,
                'component' => 'placeholder/Index',
                'menu_sort' => $sort,
            ], $system->id);
        }

        $log = $this->createMenu([
            'menu_name' => '日志管理',
            'menu_icon' => 'Document',
            'menu_path' => '/system/log',
            'component' => '',
            'menu_sort' => 10,
        ], $system->id);

        $this->createMenu([
            'menu_name' => '操作日志',
            'menu_path' => '/system/log/operlog',
            'component' => 'system/log/operlog/Index',
            'permission_code' => 'system:operlog:list',
            'menu_sort' => 20,
        ], $log->id);

        $this->createMenu([
            'menu_name' => '登录日志',
            'menu_path' => '/system/log/logininfor',
            'component' => 'placeholder/Index',
            'menu_sort' => 10,
        ], $log->id);

        $monitor = $this->createMenu([
            'menu_name' => '系统监控',
            'menu_icon' => 'Monitor',
            'menu_path' => '/monitor',
            'component' => '',
            'menu_sort' => 800,
        ]);

        foreach ([
            ['在线用户', '/monitor/online', 40],
            ['定时任务', '/monitor/job', 30],
            ['数据监控', '/monitor/druid', 20],
            ['服务监控', '/monitor/server', 10],
        ] as [$name, $path, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'menu_path' => $path,
                'component' => 'placeholder/Index',
                'menu_sort' => $sort,
            ], $monitor->id);
        }

        $tool = $this->createMenu([
            'menu_name' => '系统工具',
            'menu_icon' => 'Tools',
            'menu_path' => '/tool',
            'component' => '',
            'menu_sort' => 700,
        ]);

        foreach ([
            ['表单构建', '/tool/build', 30],
            ['代码生成', '/tool/gen', 20],
            ['系统接口', '/tool/swagger', 10],
        ] as [$name, $path, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'menu_path' => $path,
                'component' => 'placeholder/Index',
                'menu_sort' => $sort,
            ], $tool->id);
        }

        $productRoot = $this->createMenu([
            'menu_name' => '产品管理',
            'menu_icon' => 'Goods',
            'menu_path' => '/product',
            'component' => '',
            'menu_sort' => 200,
        ]);

        $productList = $this->createMenu([
            'menu_name' => '商品管理',
            'menu_icon' => 'ShoppingCart',
            'menu_path' => '/product/list',
            'component' => 'product/Index',
            'permission_code' => 'product:list',
            'menu_sort' => 40,
        ], $productRoot->id);

        foreach ([
            ['新增', 'product:add', 50],
            ['修改', 'product:edit', 40],
            ['删除', 'product:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $productList->id);
        }

        $productCategory = $this->createMenu([
            'menu_name' => '商品分类',
            'menu_icon' => 'Menu',
            'menu_path' => '/product/category',
            'component' => 'product/category/Index',
            'permission_code' => 'product:category:list',
            'menu_sort' => 30,
        ], $productRoot->id);

        foreach ([
            ['新增', 'product:category:add', 50],
            ['修改', 'product:category:edit', 40],
            ['删除', 'product:category:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $productCategory->id);
        }

        $productBrand = $this->createMenu([
            'menu_name' => '品牌管理',
            'menu_icon' => 'Medal',
            'menu_path' => '/product/brand',
            'component' => 'product/brand/Index',
            'permission_code' => 'product:brand:list',
            'menu_sort' => 20,
        ], $productRoot->id);

        foreach ([
            ['新增', 'product:brand:add', 50],
            ['修改', 'product:brand:edit', 40],
            ['删除', 'product:brand:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $productBrand->id);
        }

        $productSpec = $this->createMenu([
            'menu_name' => '规格管理',
            'menu_icon' => 'SetUp',
            'menu_path' => '/product/spec',
            'component' => 'product/spec/Index',
            'permission_code' => 'product:spec:list',
            'menu_sort' => 10,
        ], $productRoot->id);

        foreach ([
            ['新增', 'product:spec:add', 50],
            ['修改', 'product:spec:edit', 40],
            ['删除', 'product:spec:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $productSpec->id);
        }

        $site = $this->createMenu([
            'menu_name' => '名杨科技官网',
            'menu_icon' => 'Link',
            'menu_path' => '/site',
            'component' => '',
            'menu_sort' => 100,
        ]);

        $category = $this->createMenu([
            'menu_name' => '文章分类',
            'menu_icon' => 'FolderOpened',
            'menu_path' => '/site/category',
            'component' => 'site/category/Index',
            'permission_code' => 'cms:category:list',
            'menu_sort' => 20,
        ], $site->id);

        foreach ([
            ['新增', 'cms:category:add', 50],
            ['修改', 'cms:category:edit', 40],
            ['删除', 'cms:category:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $category->id);
        }

        $article = $this->createMenu([
            'menu_name' => '文章管理',
            'menu_icon' => 'Document',
            'menu_path' => '/site/article',
            'component' => 'site/article/Index',
            'permission_code' => 'cms:article:list',
            'menu_sort' => 10,
        ], $site->id);

        foreach ([
            ['新增', 'cms:article:add', 50],
            ['修改', 'cms:article:edit', 40],
            ['删除', 'cms:article:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $article->id);
        }

        $config = $this->createMenu([
            'menu_name' => '网站配置',
            'menu_icon' => 'Setting',
            'menu_path' => '/site/config',
            'component' => 'site/config/Index',
            'permission_code' => 'cms:config:list',
            'menu_sort' => 40,
        ], $site->id);

        $this->createMenu([
            'menu_name' => '修改',
            'permission_code' => 'cms:config:edit',
            'menu_sort' => 40,
        ], $config->id);

        $adPosition = $this->createMenu([
            'menu_name' => '广告位',
            'menu_icon' => 'PictureFilled',
            'menu_path' => '/site/ad-position',
            'component' => 'site/ad/position/Index',
            'permission_code' => 'cms:ad-position:list',
            'menu_sort' => 18,
        ], $site->id);

        foreach ([
            ['新增', 'cms:ad-position:add', 50],
            ['修改', 'cms:ad-position:edit', 40],
            ['删除', 'cms:ad-position:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $adPosition->id);
        }

        $adMaterial = $this->createMenu([
            'menu_name' => '广告素材',
            'menu_icon' => 'Picture',
            'menu_path' => '/site/ad-material',
            'component' => 'site/ad/material/Index',
            'permission_code' => 'cms:ad-material:list',
            'menu_sort' => 16,
        ], $site->id);

        foreach ([
            ['新增', 'cms:ad-material:add', 50],
            ['修改', 'cms:ad-material:edit', 40],
            ['删除', 'cms:ad-material:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $adMaterial->id);
        }

        $job = $this->createMenu([
            'menu_name' => '招聘职位',
            'menu_icon' => 'Suitcase',
            'menu_path' => '/site/job',
            'component' => 'site/job/Index',
            'permission_code' => 'cms:job:list',
            'menu_sort' => 12,
        ], $site->id);

        foreach ([
            ['新增', 'cms:job:add', 50],
            ['修改', 'cms:job:edit', 40],
            ['删除', 'cms:job:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $job->id);
        }

        $link = $this->createMenu([
            'menu_name' => '友情链接',
            'menu_icon' => 'Connection',
            'menu_path' => '/site/link',
            'component' => 'site/link/Index',
            'permission_code' => 'cms:link:list',
            'menu_sort' => 8,
        ], $site->id);

        foreach ([
            ['新增', 'cms:link:add', 50],
            ['修改', 'cms:link:edit', 40],
            ['删除', 'cms:link:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $link->id);
        }

        $feedback = $this->createMenu([
            'menu_name' => '留言反馈',
            'menu_icon' => 'ChatLineSquare',
            'menu_path' => '/site/feedback',
            'component' => 'site/feedback/Index',
            'permission_code' => 'cms:feedback:list',
            'menu_sort' => 5,
        ], $site->id);

        foreach ([
            ['回复', 'cms:feedback:reply', 40],
            ['删除', 'cms:feedback:remove', 30],
        ] as [$name, $code, $sort]) {
            $this->createMenu([
                'menu_name' => $name,
                'permission_code' => $code,
                'menu_sort' => $sort,
            ], $feedback->id);
        }

        unset($home);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createMenu(array $data, int|string $parentId = 0): AuthMenu
    {
        return AuthMenu::query()->create([
            'id' => Snowflake::id(),
            'parent_id' => $parentId,
            'menu_name' => $data['menu_name'],
            'menu_icon' => $data['menu_icon'] ?? '',
            'menu_path' => $data['menu_path'] ?? '',
            'component' => $data['component'] ?? '',
            'permission_code' => $data['permission_code'] ?? '',
            'menu_sort' => $data['menu_sort'] ?? 0,
            'menu_status' => 1,
        ]);
    }
}
