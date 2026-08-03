// resources/js/alpine-loader.js
const modules = {
    'user-management': () => import('./pages/user-management.js'),
    'role-management': () => import('./pages/role-management.js'),
    'permission-management': () => import('./pages/permission-management.js'),
    'icon-management': () => import('./pages/icon-management.js'),
    'menu-management': () => import('./pages/menu-management.js'),
    'activity-log-management': () => import('./pages/activity-log-management.js'),
    // tambah module lain di sini
};

export async function bootAlpine(Alpine) {
    const elements = document.querySelectorAll('[data-module]');

    const imports = Array.from(elements)
        .map(el => el.dataset.module)
        .filter(name => modules[name])
        .map(name => modules[name]());

    await Promise.all(imports);

    Alpine.start();
}
