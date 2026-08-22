const modules = import.meta.glob('../pages/**/*.vue');

export function loadView(component) {
    const path = `../pages/${component}.vue`;

    if (modules[path]) {
        return modules[path];
    }

    return modules['../pages/placeholder/Index.vue'];
}

export function generateRoutes(menus, routes = []) {
    menus.forEach((menu) => {
        if (menu.children?.length) {
            generateRoutes(menu.children, routes);
        }

        if (menu.component && menu.menu_path) {
            routes.push({
                path: menu.menu_path,
                name: menu.menu_path.replaceAll('/', '_'),
                component: loadView(menu.component),
                meta: {
                    title: menu.menu_name,
                    icon: menu.menu_icon,
                    affix: menu.menu_path === '/index',
                },
            });
        }
    });

    return routes;
}
