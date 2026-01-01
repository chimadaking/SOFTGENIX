<?php

return [
    '' => ['HomeController', 'index'],
    'home' => ['HomeController', 'index'],

    'auth' => ['AuthController', 'login'],
    'auth/login' => ['AuthController', 'login'],
    'auth/register' => ['AuthController', 'register'],
    'auth/logout' => ['AuthController', 'logout'],
    'auth/forgot-password' => ['AuthController', 'forgotPassword'],

    'ref/{code}' => ['AuthController', 'referral'],

    'dashboard' => ['DashboardController', 'index'],
    'dashboard/index' => ['DashboardController', 'index'],
    'dashboard/profile' => ['DashboardController', 'profile'],
    'dashboard/password' => ['DashboardController', 'password'],

    'product' => ['ProductController', 'index'],
    'products' => ['ProductController', 'index'],
    'product/details/{id}' => ['ProductController', 'details'],
    'products/details/{id}' => ['ProductController', 'details'],
    'product/create' => ['ProductController', 'create'],
    'products/create' => ['ProductController', 'create'],
    'product/edit/{id}' => ['ProductController', 'edit'],
    'products/edit/{id}' => ['ProductController', 'edit'],
    'product/delete/{id}' => ['ProductController', 'delete'],
    'products/delete/{id}' => ['ProductController', 'delete'],

    'order' => ['OrderController', 'index'],
    'orders' => ['OrderController', 'index'],
    'order/details/{id}' => ['OrderController', 'details'],
    'orders/details/{id}' => ['OrderController', 'details'],
    'order/create/{id}' => ['OrderController', 'create'],
    'orders/create/{id}' => ['OrderController', 'create'],
    'order/cancel/{id}' => ['OrderController', 'cancel'],
    'orders/cancel/{id}' => ['OrderController', 'cancel'],
    'order/checkout/{id}' => ['OrderController', 'checkout'],
    'orders/checkout/{id}' => ['OrderController', 'checkout'],

    'wallet' => ['WalletController', 'index'],
    'wallet/fund' => ['WalletController', 'fund'],
    'wallet/withdraw' => ['WalletController', 'withdraw'],

    'loyalty' => ['LoyaltyController', 'index'],
    'affiliate' => ['AffiliateController', 'index'],

    'admin' => ['AdminController', 'index'],
    'admin/index' => ['AdminController', 'index'],
    'admin/register' => ['AdminController', 'register'],
    'admin/users' => ['AdminController', 'users'],
    'admin/products' => ['AdminController', 'products'],
    'admin/orders' => ['AdminController', 'orders'],
    'admin/reports' => ['AdminController', 'reports'],

    'admin/providers' => ['ProviderController', 'index'],
    'admin/providers/create' => ['ProviderController', 'create'],
    'admin/providers/edit/{id}' => ['ProviderController', 'edit'],
    'admin/providers/delete/{id}' => ['ProviderController', 'delete'],

    'api' => ['APIController', 'index'],
    'api/index' => ['APIController', 'index'],
    'api/create' => ['APIController', 'create'],
    'api/edit/{id}' => ['APIController', 'edit'],
    'api/delete/{id}' => ['APIController', 'delete'],
    'api/services' => ['APIController', 'services'],
    'api/services/{id}' => ['APIController', 'services'],
    'api/updateService' => ['APIController', 'updateService'],
    'api/syncServices/{id}' => ['APIController', 'syncServices'],
    'api/logs' => ['APIController', 'logs'],
    'api/test/{id}' => ['APIController', 'test'],

    'admin/users/toggle/{id}' => ['AdminController', 'toggleUserStatus'],

    'report/sales' => ['ReportController', 'sales'],
    'report/revenue' => ['ReportController', 'revenue'],
    'report/users' => ['ReportController', 'users'],
    'report/affiliates' => ['ReportController', 'affiliates'],
];

