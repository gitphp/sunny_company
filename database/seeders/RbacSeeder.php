<?php

/**
 * RBAC与业务数据填充
 *
 * @package     Database\Seeders
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

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
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductSku;
use App\Models\ProductSkuSpecValue;
use App\Models\ProductSpecification;
use App\Models\ProductSpecificationValue;
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
        $this->seedProducts();
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
        $this->ensureProductMenus();
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

    private function ensureProductMenus(): void
    {
        $product = AuthMenu::query()->where('menu_path', '/product')->first();

        if (! $product) {
            $product = AuthMenu::query()->create([
                'id' => Snowflake::id(),
                'parent_id' => 0,
                'menu_name' => '产品管理',
                'menu_icon' => 'Goods',
                'menu_path' => '/product',
                'component' => '',
                'permission_code' => '',
                'menu_sort' => 200,
                'menu_status' => 1,
            ]);
        } else {
            $product->forceFill([
                'component' => '',
                'menu_icon' => $product->menu_icon ?: 'Goods',
            ])->save();
        }

        $this->ensureChildMenu($product, [
            'menu_name' => '商品管理',
            'menu_icon' => 'ShoppingCart',
            'menu_path' => '/product/list',
            'component' => 'product/Index',
            'permission_code' => 'product:list',
            'menu_sort' => 40,
        ], [
            ['新增', 'product:add', 50],
            ['修改', 'product:edit', 40],
            ['删除', 'product:remove', 30],
        ]);

        $this->ensureChildMenu($product, [
            'menu_name' => '商品分类',
            'menu_icon' => 'Menu',
            'menu_path' => '/product/category',
            'component' => 'product/category/Index',
            'permission_code' => 'product:category:list',
            'menu_sort' => 30,
        ], [
            ['新增', 'product:category:add', 50],
            ['修改', 'product:category:edit', 40],
            ['删除', 'product:category:remove', 30],
        ]);

        $this->ensureChildMenu($product, [
            'menu_name' => '品牌管理',
            'menu_icon' => 'Medal',
            'menu_path' => '/product/brand',
            'component' => 'product/brand/Index',
            'permission_code' => 'product:brand:list',
            'menu_sort' => 20,
        ], [
            ['新增', 'product:brand:add', 50],
            ['修改', 'product:brand:edit', 40],
            ['删除', 'product:brand:remove', 30],
        ]);

        $this->ensureChildMenu($product, [
            'menu_name' => '规格管理',
            'menu_icon' => 'SetUp',
            'menu_path' => '/product/spec',
            'component' => 'product/spec/Index',
            'permission_code' => 'product:spec:list',
            'menu_sort' => 10,
        ], [
            ['新增', 'product:spec:add', 50],
            ['修改', 'product:spec:edit', 40],
            ['删除', 'product:spec:remove', 30],
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
        $admin = User::query()->where('user_name', 'admin')->first();

        if (! $admin) {
            return;
        }

        $this->ensureSampleArticle(
            $admin,
            'company-news',
            '名杨科技管理系统正式上线',
            '统一后台，提升协同效率',
            '名杨科技管理系统正式上线，支持组织架构与官网内容一体化管理。',
            '<p>名杨科技管理系统已正式上线，覆盖用户、角色、部门、岗位与官网内容管理，帮助团队把日常协作收拢到同一套流程里。</p>',
            1,
        );

        $this->ensureSampleArticle(
            $admin,
            'industry',
            '家居行业向绿色材料与智能制造加速迈进',
            '材料、工艺与体验正在被重新定义',
            '绿色材料、柔性生产和更完整的交付体验，正在成为家居制造的新主线。',
            '<p>行业正从规模扩张转向品质与可持续。企业更关注材料来源、结构寿命，以及产品进入空间后的真实使用体验。</p><p>名杨科技持续跟进这一趋势，把工艺沉淀落实到每一件产品上。</p>',
            0,
        );
    }

    private function ensureSampleArticle(
        User $admin,
        string $categoryUrl,
        string $title,
        string $subtitle,
        string $summary,
        string $content,
        int $isTop,
    ): void {
        $category = ArticleCategory::query()->where('cat_url', $categoryUrl)->first();

        if (! $category || Article::query()->where('title', $title)->exists()) {
            return;
        }

        Article::query()->create([
            'id' => Snowflake::id(),
            'title' => $title,
            'subtitle' => $subtitle,
            'art_cover' => '',
            'art_content' => $content,
            'content_type' => 1,
            'summary' => $summary,
            'category_id' => $category->id,
            'tag_ids' => [],
            'author_id' => $admin->id,
            'author_name' => mb_substr((string) $admin->real_name, 0, 16),
            'source' => '原创',
            'source_url' => '',
            'art_status' => 4,
            'is_top' => $isTop,
            'is_original' => 1,
            'is_commentable' => 1,
            'seo_title' => $title,
            'seo_keywords' => '名杨科技',
            'seo_description' => $summary,
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
            ['conf_group' => 'basic', 'conf_key' => 'about_intro', 'conf_value' => '名杨科技专注家居与智能办公产品的设计与制造。我们以材料、结构与工艺为根基，持续打磨每一件产品的耐用度与使用体验，服务企业空间与家庭生活。', 'conf_desc' => '关于我们简介', 'input_type' => 'textarea', 'conf_sort' => 50],
            ['conf_group' => 'seo', 'conf_key' => 'seo_title', 'conf_value' => '名杨科技', 'conf_desc' => 'SEO标题', 'input_type' => 'text', 'conf_sort' => 90],
            ['conf_group' => 'seo', 'conf_key' => 'seo_keywords', 'conf_value' => '名杨科技,企业管理', 'conf_desc' => 'SEO关键词', 'input_type' => 'text', 'conf_sort' => 80],
            ['conf_group' => 'seo', 'conf_key' => 'seo_description', 'conf_value' => '名杨科技官方网站', 'conf_desc' => 'SEO描述', 'input_type' => 'textarea', 'conf_sort' => 70],
            ['conf_group' => 'contact', 'conf_key' => 'contact_name', 'conf_value' => '名杨科技', 'conf_desc' => '联系人', 'input_type' => 'text', 'conf_sort' => 90],
            ['conf_group' => 'contact', 'conf_key' => 'contact_phone', 'conf_value' => '400-800-1668', 'conf_desc' => '联系电话', 'input_type' => 'text', 'conf_sort' => 80],
            ['conf_group' => 'contact', 'conf_key' => 'contact_email', 'conf_value' => 'hello@mingyang.cn', 'conf_desc' => '联系邮箱', 'input_type' => 'text', 'conf_sort' => 70],
            ['conf_group' => 'contact', 'conf_key' => 'contact_address', 'conf_value' => '广东省深圳市南山区科技园', 'conf_desc' => '公司地址', 'input_type' => 'textarea', 'conf_sort' => 60],
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

    private function seedProducts(): void
    {
        if (ProductBrand::query()->exists()) {
            return;
        }

        $brand = ProductBrand::query()->create([
            'id' => Snowflake::id(),
            'brand_code' => 'BR000001',
            'brand_name' => '名杨',
            'alias' => 'MingYang',
            'is_system' => 1,
            'is_show' => 1,
            'sort_order' => 20,
        ]);

        ProductBrand::query()->create([
            'id' => Snowflake::id(),
            'brand_code' => 'BR000002',
            'brand_name' => '无品牌',
            'alias' => '',
            'is_system' => 1,
            'is_show' => 1,
            'sort_order' => 0,
        ]);

        $furniture = ProductCategory::query()->create([
            'id' => Snowflake::id(),
            'category_code' => 'FL000001',
            'category_name' => '家具',
            'parent_id' => 0,
            'level' => 1,
            'unit' => '件',
            'cat_status' => 1,
            'sort_order' => 20,
        ]);

        $sofa = ProductCategory::query()->create([
            'id' => Snowflake::id(),
            'category_code' => 'FL000002',
            'category_name' => '沙发',
            'parent_id' => $furniture->id,
            'level' => 2,
            'unit' => '套',
            'cat_status' => 1,
            'sort_order' => 10,
        ]);

        $color = ProductSpecification::query()->create([
            'id' => Snowflake::id(),
            'spec_code' => 'GL000001',
            'spec_name' => '颜色',
            'spec_status' => 1,
            'sort_order' => 20,
        ]);

        $size = ProductSpecification::query()->create([
            'id' => Snowflake::id(),
            'spec_code' => 'GL000002',
            'spec_name' => '尺寸',
            'spec_status' => 1,
            'sort_order' => 10,
        ]);

        $white = ProductSpecificationValue::query()->create([
            'id' => Snowflake::id(),
            'spec_id' => $color->id,
            'value_code' => 'GV000001',
            'value' => '米白',
            'sort_order' => 20,
            'value_status' => 1,
        ]);

        $grey = ProductSpecificationValue::query()->create([
            'id' => Snowflake::id(),
            'spec_id' => $color->id,
            'value_code' => 'GV000002',
            'value' => '灰色',
            'sort_order' => 10,
            'value_status' => 1,
        ]);

        $sizeA = ProductSpecificationValue::query()->create([
            'id' => Snowflake::id(),
            'spec_id' => $size->id,
            'value_code' => 'GV000003',
            'value' => '2.2m',
            'sort_order' => 10,
            'value_status' => 1,
        ]);

        $product = Product::query()->create([
            'id' => Snowflake::id(),
            'auto_code' => 'SP000001',
            'product_name' => '名杨布艺沙发',
            'product_model' => 'MY-SF-01',
            'category_id' => $sofa->id,
            'brand_id' => $brand->id,
            'material_quality' => '棉麻',
            'filling' => '高弹海绵',
            'short_desc' => '示例商品，可在后台继续完善 SKU 与图片。',
            'product_status' => 1,
            'sort_order' => 10,
        ]);

        $sku = ProductSku::query()->create([
            'id' => Snowflake::id(),
            'product_id' => $product->id,
            'sku_code' => 'SK000001',
            'price' => 2999,
            'market_price' => 3999,
            'cost_price' => 1800,
            'stock_num' => 20,
            'sale_status' => 1,
            'sort_order' => 10,
        ]);

        ProductSkuSpecValue::query()->create([
            'id' => Snowflake::id(),
            'sku_id' => $sku->id,
            'spec_id' => $color->id,
            'spec_value_id' => $white->id,
        ]);

        ProductSkuSpecValue::query()->create([
            'id' => Snowflake::id(),
            'sku_id' => $sku->id,
            'spec_id' => $size->id,
            'spec_value_id' => $sizeA->id,
        ]);

        $skuGrey = ProductSku::query()->create([
            'id' => Snowflake::id(),
            'product_id' => $product->id,
            'sku_code' => 'SK000002',
            'price' => 3099,
            'market_price' => 4099,
            'cost_price' => 1850,
            'stock_num' => 12,
            'sale_status' => 1,
            'sort_order' => 9,
        ]);

        ProductSkuSpecValue::query()->create([
            'id' => Snowflake::id(),
            'sku_id' => $skuGrey->id,
            'spec_id' => $color->id,
            'spec_value_id' => $grey->id,
        ]);

        ProductSkuSpecValue::query()->create([
            'id' => Snowflake::id(),
            'sku_id' => $skuGrey->id,
            'spec_id' => $size->id,
            'spec_value_id' => $sizeA->id,
        ]);

        $sofa->forceFill(['product_count' => 1])->save();
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
