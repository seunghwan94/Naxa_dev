<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/qr', 'Qr::index');
$routes->get('/map', 'Mapcenter::index');
// Routes.php
$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->get ('geocode',  'NaverProxy::geocode');
    $routes->post('midpoint', 'Midpoint::index');   // ← POST
});
