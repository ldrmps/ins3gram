<?php
$routes->group('api',['namespace' => 'App\Controllers\Admin'
], function ($routes) {
    $routes->group('ingredient', function($routes) {
        $routes->get('all', 'Ingredient::search');
    });
});