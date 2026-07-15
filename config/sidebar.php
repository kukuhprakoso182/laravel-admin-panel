<?php

// Data menu sidebar dipisah dari Blade supaya gampang di-maintain / ditambah tanpa
// nyentuh view. "active" sengaja tidak di-hardcode di sini, dicek langsung di
// sidebar-menu.blade.php pakai request()->routeIs() supaya selalu sinkron dengan
// route yang sedang aktif.

return [
    [
        'title' => 'Store',
        'items' => [
            [
                'label' => 'Dashboard',
                'icon' => 'dashboard-line',
                'route' => 'dashboard',
            ],
            [
                'label' => 'Orders',
                'icon' => 'shopping-bag-3-line',
                'route' => 'orders.index',
                'badge' => 8,
            ],
            [
                'label' => 'Products',
                'icon' => 'price-tag-3-line',
                'route' => 'products.index',
            ],
            [
                'label' => 'Customers',
                'icon' => 'group-line',
                'route' => 'customers.index',
            ],
        ],
    ],
    [
        'title' => 'Insights',
        'items' => [
            [
                'label' => 'Reports',
                'icon' => 'bar-chart-2-line',
                'route' => 'reports.index',
                'submenu' => [
                    ['label' => 'Sales', 'route' => 'reports.index'],
                    ['label' => 'Traffic', 'route' => 'reports.index'],
                    ['label' => 'Inventory', 'route' => 'reports.index'],
                ],
            ],
        ],
    ],
    [
        'title' => 'Pages',
        'items' => [
            [
                'label' => 'Authentication',
                'icon' => 'shield-user-line',
                'route' => 'login',
                'submenu' => [
                    ['label' => 'Login', 'route' => 'login'],
                    ['label' => 'Register', 'route' => 'register'],
                    ['label' => 'Forgot password', 'route' => 'password.request'],
                ],
            ],
            [
                'label' => 'Profile',
                'icon' => 'user-line',
                'route' => 'profile.edit',
            ],
            [
                'label' => 'Blank',
                'icon' => 'file-line',
                'route' => 'blank',
            ],
        ],
    ],
];
