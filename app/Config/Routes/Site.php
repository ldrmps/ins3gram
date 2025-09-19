<?php
$routes->get('/', 'Home::index');
$routes->get('/forbidden','Site::forbidden');

$routes->get('/sign-in', 'Auth::signIn');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

$routes->group('recette', function ($routes) {
    $routes->get('/', 'Recipe::index');
    $routes->get('(:any)', 'Recipe::show/$1');
});
//dataTable
$routes->post('/datatable/searchdatatable', 'DataTable::searchdatatable');