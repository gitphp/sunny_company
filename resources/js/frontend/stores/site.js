/**
 * 站点信息仓库
 *
 * @package     Resources\Js\Frontend\Stores
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import { defineStore } from 'pinia';
import { fetchSite } from '../api/site';

export const useSiteStore = defineStore('site', {
    state: () => ({
        site: {},
        nav: [],
        links: [],
        loaded: false,
    }),
    actions: {
        async bootstrap() {
            if (this.loaded) {
                return;
            }
            const { data } = await fetchSite();
            this.site = data.site ?? {};
            this.nav = data.nav ?? [];
            this.links = data.links ?? [];
            this.loaded = true;
        },
    },
});
