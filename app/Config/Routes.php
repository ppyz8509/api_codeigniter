<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/users/getAll', 'UserController::index');             // แสดงผู้ใช้ทั้งหมด
$routes->get('/users/getById/(:num)', 'UserController::show/$1');    // แสดงผู้ใช้ตาม ID
$routes->post('/users/create', 'UserController::create');   // สร้างผู้ใช้ใหม่
$routes->put('/users/update/(:num)', 'UserController::update/$1');  // อัปเดตผู้ใช้ตาม ID
$routes->delete('/users/delete/(:num)', 'UserController::delete/$1'); // ลบผู้ใช้ตาม ID