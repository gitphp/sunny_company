<?php

/**
 * 前台首页服务类
 *
 * @package     App\Services
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

namespace App\Services;

class HomeService
{
    public function __construct(
        private readonly SiteConfigService $configs,
        private readonly AdMaterialService $ads,
        private readonly ArticleService $articles,
        private readonly ProductService $products,
        private readonly FriendLinkService $links,
        private readonly ProductCategoryService $categories,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function site(): array
    {
        $values = $this->configs->publicValues();

        return [
            'site' => $values,
            'nav' => $this->nav(),
            ...$this->links->publicList(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function index(): array
    {
        $site = $this->site();
        $products = $this->products->publicPaginate(['per_page' => 8]);

        return [
            ...$site,
            'banners' => $this->ads->activeByCode('home_top_banner')['materials'],
            'about' => [
                'title' => '关于我们',
                'intro' => (string) ($site['site']['about_intro'] ?? ''),
            ],
            'news' => $this->articles->latestByCategoryUrl('company-news', 4),
            'industry' => $this->articles->latestByCategoryUrl('industry', 4),
            'products' => $products['data'],
            'categories' => $this->categories->publicTree()['categories'],
        ];
    }

    /**
     * @return list<array{label: string, path: string}>
     */
    private function nav(): array
    {
        return [
            ['label' => '首页', 'path' => '/'],
            ['label' => '关于我们', 'path' => '/about'],
            ['label' => '产品中心', 'path' => '/products'],
            ['label' => '公司新闻', 'path' => '/news'],
            ['label' => '行业动态', 'path' => '/industry'],
            ['label' => '联系我们', 'path' => '/contact'],
        ];
    }
}
