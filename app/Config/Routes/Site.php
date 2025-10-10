<?php
$routes->get('/', 'Home::index');
$routes->get('/forbidden','Site::forbidden');

$routes->get('/test-pagination','Site::testPagination');

$routes->get('/sign-in', 'Auth::signIn');
$routes->get('/register', 'User::register');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

$routes->group('recette', function ($routes) {
    $routes->get('/', 'Recipe::index');
    $routes->get('(:any)', 'Recipe::show/$1');
});

$routes->group('user', function ($routes) {
    $routes->post('insert', 'User::insert');
    $routes->post('update', 'User::update');
});

$routes->group('messagerie', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Chat::index');
    $routes->get('conversation', 'Chat::conversation');
    $routes->get('new-messages', 'Chat::newMessages');
    $routes->get('historique', 'Chat::historique');
    $routes->post('send', 'Chat::send');
});

//dataTable
$routes->post('/datatable/searchdatatable', 'DataTable::searchdatatable');