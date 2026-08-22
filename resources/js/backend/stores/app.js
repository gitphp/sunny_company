/**
 * 应用状态仓库
 *
 * @package     Resources\Backend\Stores
 * @license     MIT License (MIT)
 * @author      yang song <githup@163.com> QQ4873473
 * @copyright   Copyright (c) 2024 YourCompany. All rights reserved.
 * @link        http://www.budff.com
 */

import { defineStore } from 'pinia';

export const useAppStore = defineStore('app', {
    state: () => ({
        collapsed: false,
    }),
    actions: {
        toggleSidebar() {
            this.collapsed = !this.collapsed;
        },
    },
});
