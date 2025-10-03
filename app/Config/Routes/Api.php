<?php
$routes->group('api',['namespace' => 'App\Controllers\Admin'
], function ($routes) {
    $routes->group('ingredient', function($routes) {
        $routes->get('all', 'Ingredient::search');
    });
    $routes->group('user', function($routes) {
        $routes->get('all', 'User::search');
    });
});