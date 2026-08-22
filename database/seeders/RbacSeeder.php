<?php

namespace Database\Seeders;

use App\Enums\DataScope;
use App\Enums\JobStatus;
use App\Enums\PermissionType;
use App\Enums\RoleType;
use App\Models\AdMaterial;
use App\Models\AdPosition;
use App\Models\AiProvider;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\AuthMenu;
use App\Models\AuthPermission;
use App\Models\AuthRole;
use App\Models\BossJob;
use App\Models\FriendLink;
use App\Models\HrDepartment;
use App\Models\HrPost;
use App\Models\SiteConfig;
use App\Models\User;
use App\Support\Snowflake;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureMenuMeta();
        $root = $this->seedDepartments();
        $this->seedPosts();
        $this->seedArticleCategories();
        $this->seedSiteConfigs();
        $this->seedFriendLinks();
        $this->seedAdPositions();
        $this->seedBossJobs();
        $this->seedAiProviders();
        $this->syncPermissionsFromMenus();
        $super = $this->seedSuperAdminRole();
        $this->assignAdmin($root, $super);
        $this->seedSampleArticle();
    }

    private function ensureMenuMeta(): void
    {
        $this->updateMenu('/system/role', 'system/role/Index', 'system:role:list', [
            ['新增', 'system:role:add', 50],
            ['修改', 'system:role:edit', 40],
            ['删除', 'system:role:remove', 30],
        ]);

        $this->updateMenu('/system/dept', 'system/dept/Index', 'system:dept:list', [
            ['新增', 'system:dept:add', 50],
            ['修改', 'system:dept:edit', 40],
            ['删除', 'system:dept:remove', 30],
        ]);

        $this->updateMenu('/system/menu', 'system/menu/Index', 'system:menu:list', []);

        $this->updateMenu('/system/post', 'system/post/Index', 'system:post:list', [
            ['新增', 'system:post:add', 50],
            ['修改', 'system:post:edit', 40],
            ['删除', 'system:post:remove', 30],
        ]);

        $this->updateMenu('/system/log/operlog', 'system/log/operlog/Index', 'system:operlog:list', [
            ['删除', 'system:operlog:remove', 30],
        ]);

        $this->updateMenu('/ai', 'ai/Index', 'ai:chat', [
            ['模型配置', 'ai:config', 40],
        ]);

        $this->ensureCmsMenus();
    }

    /**
     * @param  list<array{0: string, 1: string, 2: int}>  $buttons
     */
    private function updateMenu(string $path, string $component, string $code, array $buttons): void
    {
        $menu = AuthMenu::query()->where('menu_path', $path)->first();

        if (! $menu) {
            return;
        }

        $menu->forceFill([
            'component' => $component,
            'permission_code' => $code,
        ])->save();

        foreach ($buttons as [$name, $buttonCode, $sort]) {
            AuthMenu::query()->firstOrCreate(
                ['permission_code' => $buttonCode],
                [
                    'id' => Snowflake::id(),
                    'parent_id' => $menu->id,
                    'menu_name' => $name,
                    'menu_icon' => '',
                    'menu_path' => '',
                    'component' => '',
                    'menu_sort' => $sort,
                    'menu_status' => 1,
                ]
            );
        }
    }

    private function ensureCmsMenus(): void
    {
        $site = AuthMenu::query()->where('menu_path', '/site')->first();

        if (! $site) {
            $site = AuthMenu::query()->create([
                'id' => Snowflake::id(),
                'parent_id' => 0,
                'menu_name' => '名杨科技官网',
                'menu_icon' => 'Link',
                'menu_path' => '/site',
                'component' => '',
                'permission_code' => '',
                'menu_sort' => 100,
                'menu_status' => 1,
            ]);
        } else {
            $site->forceFill([
                'component' => '',
                'menu_icon' => $site->menu_icon ?: 'Link',
            ])->save();
        }

        $this->ensureChildMenu($site, [
            'menu_name' => '网站配置',
            'menu_icon' => 'Setting',
            'menu_path' => '/site/config',
            'component' => 'site/config/Index',
            'permission_code' => 'cms:config:list',
            'menu_sort' => 40,
        ], [
            ['修改', 'cms:config:edit', 40],
        ]);

        $this->ensureChildMenu($site, [
            'menu_name' => '文章分类',
            'menu_icon' => 'FolderOpened',
            'menu_path' => '/site/category',
            'component' => 'site/category/Index',
            'permission_code' => 'cms:category:list',
            'menu_sort' => 20,
        ], [
            ['新增', 'cms:category:add', 50],
            ['修改', 'cms:category:edit', 40],
            ['删除', 'cms:category:remove', 30],
        ]);

        $this->ensureChildMenu($site, [
            'menu_name' => '文章管理',
            'menu_icon' => 'Document',
            'menu_path' => '/site/article',
            'component' => 'site/article/Index',
            'permission_code' => 'cms:article:list',
            'menu_sort' => 10,
        ], [
            ['新增', 'cms:article:add', 50],
            ['修改', 'cms:article:edit', 40],
            ['删除', 'cms:article:remove', 30],
        ]);

        $this->ensureChildMenu($site, [
            'menu_name' => '广告位',
            'menu_icon' => 'PictureFilled',
            'menu_path' => '/site/ad-position',
            'component' => 'site/ad/position/Index',
            'permission_code' => 'cms:ad-position:list',
            'menu_sort' => 18,
        ], [
            ['新增', 'cms:ad-position:add', 50],
            ['修改', 'cms:ad-position:edit', 40],
            ['删除', 'cms:ad-position:remove', 30],
        ]);

        $this->ensureChildMenu($site, [
            'menu_name' => '广告素材',
            'menu_icon' => 'Picture',
            'menu_path' => '/site/ad-material',
            'component' => 'site/ad/material/Index',
            'permission_code' => 'cms:ad-material:list',
            'menu_sort' => 16,
        ], [
            ['新增', 'cms:ad-material:add', 50],
            ['修改', 'cms:ad-material:edit', 40],
            ['删除', 'cms:ad-material:remove', 30],
        ]);

        $this->ensureChildMenu($site, [
            'menu_name' => '招聘职位',
            'menu_icon' => 'Suitcase',
            'menu_path' => '/site/job',
            'component' => 'site/job/Index',
            'permission_code' => 'cms:job:list',
            'menu_sort' => 12,
        ], [
            ['新增', 'cms:job:add', 50],
            ['修改', 'cms:job:edit', 40],
            ['删除', 'cms:job:remove', 30],
        ]);

        $this->ensureChildMenu($site, [
            'menu_name' => '友情链接',
            'menu_icon' => 'Connection',
            'menu_path' => '/site/link',
            'component' => 'site/link/Index',
            'permission_code' => 'cms:link:list',
            'menu_sort' => 8,
        ], [
            ['新增', 'cms:link:add', 50],
            ['修改', 'cms:link:edit', 40],
            ['删除', 'cms:link:remove', 30],
        ]);

        $this->ensureChildMenu($site, [
            'menu_name' => '留言反馈',
            'menu_icon' => 'ChatLineSquare',
            'menu_path' => '/site/feedback',
            'component' => 'site/feedback/Index',
            'permission_code' => 'cms:feedback:list',
            'menu_sort' => 5,
        ], [
            ['回复', 'cms:feedback:reply', 40],
            ['删除', 'cms:feedback:remove', 30],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<array{0: string, 1: string, 2: int}>  $buttons
     */
    private function ensureChildMenu(AuthMenu $parent, array $data, array $buttons): void
    {
        $menu = AuthMenu::query()->where('menu_path', $data['menu_path'])->first();

        if (! $menu) {
            $menu = AuthMenu::query()->create([
                'id' => Snowflake::id(),
                'parent_id' => $parent->id,
                'menu_name' => $data['menu_name'],
                'menu_icon' => $data['menu_icon'] ?? '',
                'menu_path' => $data['menu_path'],
                'component' => $data['component'],
                'permission_code' => $data['permission_code'],
                'menu_sort' => $data['menu_sort'] ?? 0,
                'menu_status' => 1,
            ]);
        } else {
            $menu->forceFill([
                'parent_id' => $parent->id,
                'component' => $data['component'],
                'permission_code' => $data['permission_code'],
                'menu_icon' => $data['menu_icon'] ?? $menu->menu_icon,
            ])->save();
        }

        foreach ($buttons as [$name, $buttonCode, $sort]) {
            AuthMenu::query()->firstOrCreate(
                ['permission_code' => $buttonCode],
                [
                    'id' => Snowflake::id(),
                    'parent_id' => $menu->id,
                    'menu_name' => $name,
                    'menu_icon' => '',
                    'menu_path' => '',
                    'component' => '',
                    'menu_sort' => $sort,
                    'menu_status' => 1,
                ]
            );
        }
    }

    private function seedDepartments(): HrDepartment
    {
        $existing = HrDepartment::query()->where('dept_code', 'ROOT')->first();

        if ($existing) {
            return $existing;
        }

        $root = $this->createDept('名杨科技科技', 'ROOT', 0, '0', 1, 90);
        $sz = $this->createDept('深圳总公司', 'SZ', $root->id, '0,'.$root->id, 2, 80);
        $cs = $this->createDept('长沙分公司', 'CS', $root->id, '0,'.$root->id, 2, 70);

        foreach ([['研发部门', 'SZ_RD', 50], ['市场部门', 'SZ_MK', 40], ['测试部门', 'SZ_QA', 30], ['财务部门', 'SZ_FN', 20], ['运维部门', 'SZ_OPS', 10]] as [$name, $code, $sort]) {
            $this->createDept($name, $code, $sz->id, $sz->ancestors.','.$sz->id, 3, $sort);
        }

        foreach ([['市场部门', 'CS_MK', 20], ['财务部门', 'CS_FN', 10]] as [$name, $code, $sort]) {
            $this->createDept($name, $code, $cs->id, $cs->ancestors.','.$cs->id, 3, $sort);
        }

        return $root;
    }

    private function createDept(string $name, string $code, int|string $parentId, string $ancestors, int $level, int $sort): HrDepartment
    {
        return HrDepartment::query()->create([
            'id' => Snowflake::id(),
            'parent_id' => $parentId,
            'dept_name' => $name,
            'dept_code' => $code,
            'ancestors' => $ancestors,
            'dept_level' => $level,
            'leader_user_id' => 0,
            'dept_phone' => '',
            'dept_sort' => $sort,
            'dept_status' => 1,
            'created_by' => 0,
        ]);
    }

    private function seedPosts(): void
    {
        if (HrPost::query()->exists()) {
            return;
        }

        $ceo = $this->createPost('总经理', 'CEO', 0, 90);
        $manager = $this->createPost('部门经理', 'DEPT_MANAGER', $ceo->id, 80);
        $this->createPost('技术主管', 'TECH_LEAD', $manager->id, 70);
        $this->createPost('前端开发', 'FE_DEV', $manager->id, 60);
        $this->createPost('财务专员', 'FINANCE', $manager->id, 50);
    }

    private function createPost(string $name, string $code, int|string $parentId, int $sort): HrPost
    {
        return HrPost::query()->create([
            'id' => Snowflake::id(),
            'parent_id' => $parentId,
            'post_name' => $name,
            'post_code' => $code,
            'post_sort' => $sort,
            'post_status' => 1,
            'remark' => '',
            'created_by' => 0,
        ]);
    }

    private function seedArticleCategories(): void
    {
        if (ArticleCategory::query()->exists()) {
            return;
        }

        foreach ([
            ['公司新闻', 'company-news', 90],
            ['产品动态', 'product', 80],
            ['行业资讯', 'industry', 70],
        ] as [$name, $url, $sort]) {
            $this->createCategory(0, $name, $url, $sort);
        }

        $this->createCategory(1, '关于我们', 'about', 60);
    }

    private function createCategory(int $type, string $name, string $url, int $sort): ArticleCategory
    {
        return ArticleCategory::query()->create([
            'id' => Snowflake::id(),
            'cat_type' => $type,
            'parent_id' => 0,
            'cat_name' => $name,
            'cat_url' => $url,
            'description' => '',
            'cat_sort' => $sort,
            'status' => 1,
        ]);
    }

    private function seedSampleArticle(): void
    {
        if (Article::query()->exists()) {
            return;
        }

        $admin = User::query()->where('user_name', 'admin')->first();
        $category = ArticleCategory::query()->where('cat_url', 'company-news')->first();

        if (! $admin || ! $category) {
            return;
        }

        Article::query()->create([
            'id' => Snowflake::id(),
            'title' => '名杨科技科技正式上线名杨科技管理系统',
            'subtitle' => '统一后台，提升协同效率',
            'art_cover' => '',
            'art_content' => '<p>名杨科技管理系统已正式上线，覆盖用户、角色、部门、岗位与官网内容管理。</p>',
            'content_type' => 1,
            'summary' => '名杨科技管理系统正式上线，支持组织架构与官网内容一体化管理。',
            'category_id' => $category->id,
            'tag_ids' => [],
            'author_id' => $admin->id,
            'author_name' => mb_substr((string) $admin->real_name, 0, 16),
            'source' => '原创',
            'source_url' => '',
            'art_status' => 4,
            'is_top' => 1,
            'is_original' => 1,
            'is_commentable' => 1,
            'seo_title' => '名杨科技科技正式上线名杨科技管理系统',
            'seo_keywords' => '名杨科技科技,管理系统',
            'seo_description' => '名杨科技管理系统正式上线。',
            'published_at' => now(),
        ]);
    }

    private function seedSiteConfigs(): void
    {
        foreach ($this->defaultSiteConfigs() as $item) {
            SiteConfig::query()->firstOrCreate(
                ['conf_key' => $item['conf_key']],
                [
                    'id' => Snowflake::id(),
                    ...$item,
                ]
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function defaultSiteConfigs(): array
    {
        return [
            ['conf_group' => 'basic', 'conf_key' => 'site_name', 'conf_value' => '名杨科技', 'conf_desc' => '网站名称', 'input_type' => 'text', 'conf_sort' => 90],
            ['conf_group' => 'basic', 'conf_key' => 'site_logo', 'conf_value' => '', 'conf_desc' => '网站Logo', 'input_type' => 'image', 'conf_sort' => 80],
            ['conf_group' => 'basic', 'conf_key' => 'site_icp', 'conf_value' => '', 'conf_desc' => '备案号', 'input_type' => 'text', 'conf_sort' => 70],
            ['conf_group' => 'basic', 'conf_key' => 'site_copyright', 'conf_value' => 'Copyright © 名杨科技', 'conf_desc' => '版权信息', 'input_type' => 'textarea', 'conf_sort' => 60],
            ['conf_group' => 'seo', 'conf_key' => 'seo_title', 'conf_value' => '名杨科技', 'conf_desc' => 'SEO标题', 'input_type' => 'text', 'conf_sort' => 90],
            ['conf_group' => 'seo', 'conf_key' => 'seo_keywords', 'conf_value' => '名杨科技,企业管理', 'conf_desc' => 'SEO关键词', 'input_type' => 'text', 'conf_sort' => 80],
            ['conf_group' => 'seo', 'conf_key' => 'seo_description', 'conf_value' => '名杨科技官方网站', 'conf_desc' => 'SEO描述', 'input_type' => 'textarea', 'conf_sort' => 70],
            ['conf_group' => 'contact', 'conf_key' => 'contact_name', 'conf_value' => '', 'conf_desc' => '联系人', 'input_type' => 'text', 'conf_sort' => 90],
            ['conf_group' => 'contact', 'conf_key' => 'contact_phone', 'conf_value' => '', 'conf_desc' => '联系电话', 'input_type' => 'text', 'conf_sort' => 80],
            ['conf_group' => 'contact', 'conf_key' => 'contact_email', 'conf_value' => '', 'conf_desc' => '联系邮箱', 'input_type' => 'text', 'conf_sort' => 70],
            ['conf_group' => 'contact', 'conf_key' => 'contact_address', 'conf_value' => '', 'conf_desc' => '公司地址', 'input_type' => 'textarea', 'conf_sort' => 60],
            ['conf_group' => 'social', 'conf_key' => 'social_wechat', 'conf_value' => '', 'conf_desc' => '微信', 'input_type' => 'text', 'conf_sort' => 90],
            ['conf_group' => 'social', 'conf_key' => 'social_weibo', 'conf_value' => '', 'conf_desc' => '微博', 'input_type' => 'text', 'conf_sort' => 80],
            ['conf_group' => 'social', 'conf_key' => 'social_douyin', 'conf_value' => '', 'conf_desc' => '抖音', 'input_type' => 'text', 'conf_sort' => 70],
        ];
    }

    private function seedFriendLinks(): void
    {
        if (FriendLink::query()->exists()) {
            return;
        }

        foreach ([
            ['Laravel', 'https://laravel.com', 'PHP Web 框架', 20],
            ['Element Plus', 'https://element-plus.org', 'Vue 组件库', 10],
        ] as [$name, $url, $desc, $sort]) {
            FriendLink::query()->create([
                'id' => Snowflake::id(),
                'link_name' => $name,
                'link_url' => $url,
                'link_logo' => '',
                'link_desc' => $desc,
                'link_sort' => $sort,
                'link_status' => 1,
            ]);
        }
    }

    private function seedAdPositions(): void
    {
        if (AdPosition::query()->exists()) {
            return;
        }

        $banner = AdPosition::query()->create([
            'id' => Snowflake::id(),
            'pos_name' => '首页顶部轮播图',
            'pos_code' => 'home_top_banner',
            'pos_desc' => '官网首页顶部轮播',
            'ad_width' => 1920,
            'ad_height' => 600,
            'status' => 1,
        ]);

        AdPosition::query()->create([
            'id' => Snowflake::id(),
            'pos_name' => '首页侧边广告',
            'pos_code' => 'home_side_ad',
            'pos_desc' => '官网首页侧栏',
            'ad_width' => 300,
            'ad_height' => 250,
            'status' => 1,
        ]);

        AdMaterial::query()->create([
            'id' => Snowflake::id(),
            'position_id' => $banner->id,
            'title' => '名杨科技管理系统上线',
            'image_url' => '',
            'link_url' => '/admin',
            'target' => '_self',
            'sort_order' => 10,
            'status' => 1,
        ]);
    }

    private function seedBossJobs(): void
    {
        if (BossJob::query()->exists()) {
            return;
        }

        foreach ([
            [
                'job_title' => 'PHP 开发工程师',
                'department' => '研发部',
                'workplace' => '深圳',
                'experience' => '3-5年',
                'education' => '本科',
                'salary_range' => '15-25K',
                'description' => '负责公司官网与管理系统的后端开发与维护。',
                'requirements' => "熟悉 PHP / Laravel\n具备 MySQL 与 Redis 使用经验",
                'benefits' => '五险一金、弹性工作、年度体检',
                'is_hot' => 1,
                'job_status' => JobStatus::Published->value,
                'job_sort' => 20,
            ],
            [
                'job_title' => '前端开发工程师',
                'department' => '研发部',
                'workplace' => '深圳',
                'experience' => '1-3年',
                'education' => '本科',
                'salary_range' => '12-20K',
                'description' => '负责官网与后台管理系统的前端开发。',
                'requirements' => "熟悉 Vue 3 / Element Plus\n了解前端工程化",
                'benefits' => '五险一金、餐补、带薪年假',
                'is_hot' => 0,
                'job_status' => JobStatus::Published->value,
                'job_sort' => 10,
            ],
            [
                'job_title' => '市场专员',
                'department' => '市场部',
                'workplace' => '长沙',
                'experience' => '不限',
                'education' => '大专',
                'salary_range' => '8-12K',
                'description' => '负责市场活动策划与客户沟通。',
                'requirements' => '沟通能力强，有相关经验优先',
                'benefits' => '五险一金、绩效奖金',
                'is_hot' => 0,
                'job_status' => JobStatus::Pending->value,
                'job_sort' => 0,
            ],
        ] as $item) {
            BossJob::query()->create([
                'id' => Snowflake::id(),
                ...$item,
            ]);
        }
    }

    private function seedAiProviders(): void
    {
        if (AiProvider::query()->exists()) {
            return;
        }

        foreach ([
            [
                'provider_name' => 'DeepSeek',
                'base_url' => 'https://api.deepseek.com',
                'model' => 'deepseek-chat',
                'system_prompt' => '你是名杨科技管理系统的智能助手，回答简洁、准确。',
                'is_default' => 1,
                'sort_order' => 30,
            ],
            [
                'provider_name' => 'OpenAI',
                'base_url' => 'https://api.openai.com/v1',
                'model' => 'gpt-4o-mini',
                'system_prompt' => '',
                'is_default' => 0,
                'sort_order' => 20,
            ],
            [
                'provider_name' => '通义千问',
                'base_url' => 'https://dashscope.aliyuncs.com/compatible-mode/v1',
                'model' => 'qwen-plus',
                'system_prompt' => '',
                'is_default' => 0,
                'sort_order' => 10,
            ],
        ] as $item) {
            AiProvider::query()->create([
                'id' => Snowflake::id(),
                'driver' => 'openai',
                'api_key' => '',
                'temperature' => 0.7,
                'max_tokens' => 2048,
                'status' => 1,
                ...$item,
            ]);
        }
    }

    private function syncPermissionsFromMenus(): void
    {
        $this->syncMenuPermissions('0', '0');
    }

    private function syncMenuPermissions(string $parentMenuId, string $parentPermId): void
    {
        $menus = AuthMenu::query()
            ->where('parent_id', $parentMenuId)
            ->orderByDesc('menu_sort')
            ->get();

        foreach ($menus as $menu) {
            $code = $this->permissionCode($menu);

            $permission = AuthPermission::query()->firstOrCreate(
                ['per_code' => $code],
                [
                    'id' => Snowflake::id(),
                    'parent_id' => $parentPermId,
                    'per_name' => $menu->menu_name,
                    'per_type' => $menu->isButton() ? PermissionType::Button : PermissionType::Menu,
                    'per_path' => $menu->menu_path,
                    'per_method' => '',
                    'per_icon' => $menu->menu_icon,
                    'per_sort' => $menu->menu_sort,
                    'per_status' => 1,
                ]
            );

            $this->syncMenuPermissions((string) $menu->id, (string) $permission->id);
        }
    }

    private function permissionCode(AuthMenu $menu): string
    {
        if ($menu->permission_code !== '') {
            return $menu->permission_code;
        }

        if ($menu->menu_path !== '') {
            return trim(str_replace('/', ':', $menu->menu_path), ':');
        }

        return 'menu_'.$menu->id;
    }

    private function seedSuperAdminRole(): AuthRole
    {
        $role = AuthRole::query()->firstOrCreate(
            ['role_code' => AuthRole::SUPER_ADMIN_CODE],
            [
                'id' => Snowflake::id(),
                'role_name' => '超级管理员',
                'role_type' => RoleType::System,
                'role_sort' => 999,
                'data_scope' => DataScope::All,
                'scope_departments' => [],
                'role_status' => 1,
                'role_remark' => '拥有全部菜单和权限',
            ]
        );

        $now = now();
        $role->menus()->sync(
            AuthMenu::query()->pluck('id')->mapWithKeys(fn ($id) => [(string) $id => ['created_at' => $now]])->all()
        );
        $role->permissions()->sync(
            AuthPermission::query()->pluck('id')->mapWithKeys(fn ($id) => [(string) $id => ['created_at' => $now]])->all()
        );

        return $role;
    }

    private function assignAdmin(HrDepartment $root, AuthRole $super): void
    {
        $admin = User::query()->where('user_name', 'admin')->first();

        if (! $admin) {
            return;
        }

        $admin->forceFill([
            'dept_id' => $root->id,
            'post_id' => HrPost::query()->where('post_code', 'CEO')->value('id') ?: 0,
        ])->save();

        $root->forceFill([
            'leader_user_id' => $admin->id,
        ])->save();

        $admin->roles()->sync([
            (string) $super->id => ['created_at' => now()],
        ]);
    }
}
