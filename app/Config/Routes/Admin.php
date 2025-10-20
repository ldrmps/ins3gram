<?php
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:administrateur'], function ($routes) {
    //Routes vers le tableau de bord
    $routes->get('dashboard', 'Admin::dashboard');

    $routes->group('user', function ($routes) {
        $routes->get('/', 'User::index');
        $routes->get('(:num)', 'User::edit/$1');
        $routes->get('new', 'User::create');
        $routes->post('update', 'User::update');
        $routes->post('insert', 'User::insert');
        $routes->post('switch-active','User::switchActive');
        $routes->get('search', 'User::search');
        $routes->post('delete-avatar', 'User::deleteAvatar');
    });

    $routes->group('user-permission', function ($routes) {
        $routes->get('/', 'UserPermission::index');
        $routes->post('update', 'UserPermission::update');
        $routes->post('insert', 'UserPermission::insert');
        $routes->post('delete', 'UserPermission::delete');
    });

    $routes->group('recipe', function ($routes) {
        $routes->get('/', 'Recipe::index');
        $routes->get('(:num)', 'Recipe::edit/$1');
        $routes->get('new', 'Recipe::create');
        $routes->post('insert', 'Recipe::insert');
        $routes->post('update', 'Recipe::update');
        $routes->post('switch-active','Recipe::switchActive');
    });

    $routes->group('brand', function ($routes) {
        $routes->get('/', 'Brand::index');
        $routes->post('update', 'Brand::update');
        $routes->post('insert', 'Brand::insert');
        $routes->post('delete', 'Brand::delete');
    });

    $routes->group('ingredient', function ($routes) {
        $routes->get('/', 'Ingredient::index');
        $routes->get('new', 'Ingredient::create');
        $routes->get('(:num)', 'Ingredient::edit/$1');
        $routes->get('search', 'Ingredient::search');
        $routes->post('insert', 'Ingredient::insert');
        $routes->post('update', 'Ingredient::update');
    });

    $routes->group('unit', function ($routes) {
        $routes->get('search', 'Unit::search');
    });

    $routes->group('category-ingredient', function ($routes) {
        $routes->get('/', 'CategIng::index');
        $routes->post('update', 'CategIng::update');
        $routes->post('insert', 'CategIng::insert');
        $routes->post('delete', 'CategIng::delete');
    });

    $routes->group('tag', function ($routes) {
        $routes->get('/', 'Tag::index');
        $routes->post('update', 'Tag::update');
        $routes->post('insert', 'Tag::insert');
        $routes->post('delete', 'Tag::delete');
    });

    $routes->group('media', function ($routes) {
        $routes->get('/', 'Media::index');
        $routes->get('load-more', 'Media::loadMore');
        $routes->post('delete', 'Media::delete');
    });
});
