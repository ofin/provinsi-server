<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
// $routes->get('/updatenik', 'Auth::updateNik');
// $routes->get('/updatenikkk', 'Auth::updateNikKK');
// $routes->get('/updateKab', 'Auth::updateKab');
$routes->post('/auth', 'Auth::doLogin');
$routes->get('/encrypt', 'Home::encrypt');
$routes->get('/decrypt', 'Home::decrypt');
$routes->get('/kabupaten', 'Kabupaten::index');
$routes->put('/kabupaten/update', 'Kabupaten::Update',  ['filter' => 'AdminFilter']);
$routes->delete('/kabupaten/delete', 'Kabupaten::delete',  ['filter' => 'AdminFilter']);
$routes->get('/kecamatan/(:any)', 'Kecamatan::index/$1');
$routes->post('/kecamatan/tambah', 'Kecamatan::Tambah', ['filter' => 'AdminFilter']);
$routes->put('/kecamatan/update', 'Kecamatan::Update',  ['filter' => 'AdminFilter']);
$routes->get('/desa/(:any)', 'Desa::index/$1');
$routes->post('/desa/tambah', 'Desa::tambah', ['filter' => 'AdminFilter']);
$routes->put('/desa/update', 'Desa::update', ['filter' => 'AdminFilter']);
$routes->delete('/desa/delete', 'Desa::delete', ['filter' => 'AdminFilter']);
$routes->post('/anggota-keluarga', 'AnggotaKeluarga::index');
$routes->post('/dokumentasi', 'Home::dokumentasi');

$routes->post('/admin/pendataan', 'Admin\Pendataan::index', ['filter' => 'AdminFilter']);
$routes->delete('/admin/pendataan/delete', 'Admin\Pendataan::delete', ['filter' => 'AdminFilter']);
$routes->post('/admin/tim', 'Admin\Tim::index', ['filter' => 'AuthFilter']);
$routes->post('/admin/tim/insert', 'Admin\Tim::insert', ['filter' => 'AuthFilter']);
$routes->delete('/admin/tim/delete', 'Admin\Tim::delete', ['filter' => 'AdminFilter']);
$routes->post('/admin/tim/detail', 'Admin\Tim::Detail', ['filter' => 'AdminFilter']);
$routes->post('/admin/tim/update', 'Admin\Tim::ubah', ['filter' => 'AdminFilter']);

$routes->get('/admin/admin', 'Admin\Admin::index', ['filter' => 'AdminFilter']);
$routes->post('/admin/admin/tambah', 'Admin\Admin::Tambah', ['filter' => 'AuthFilter']);
$routes->post('/admin/admin/update', 'Admin\Admin::ubah', ['filter' => 'AuthFilter']);
$routes->delete('/admin/admin/delete', 'Admin\Admin::delete', ['filter' => 'AuthFilter']);

$routes->delete('/kecamatan/delete', 'kecamatan::delete');
// // $routes->get('/tim', 'Tim::index');
// $routes->get('/tim/kabupaten', 'Tim::showkab');
// $routes->post('/tim/insert', 'Tim::create');
// $routes->patch('/tim/update/(:any)', 'Tim::update');
// $routes->delete('/tim/delete/(:any)', 'Tim::delete/$1');
// $routes->get('/tim/(:any)', 'Tim::getdatabyid/$1');

// $routes->get('/kecamatan/(:any)', 'Kecamatan::getById/$1');

// $routes->get('/desa/(:any)', 'Desa::getDesaById/$1');

// $routes->get('/user', 'UserLogin::index');
// $routes->post('/user/insert', 'UserLogin::create');
// $routes->patch('/user/update/(:any)', 'UserLogin::update/$1');
// $routes->delete('/user/delete/(:any)', 'UserLogin::delete/$1');

// $routes->post('/login', 'Login::index');
// $routes->patch('/user/update/(:any)', 'User::update');
// $routes->delete('/user/delete/(:any)', 'User::delete/$1');
// $routes->get('/user/(:any)', 'User::getdatabyid/$1');

$routes->post('/tim/pendataan', 'Tim\Pendataan::index', ['filter' => 'TimFilter']);
// $routes->post('/tim/pendataan/detail', 'Tim\Pendataan::detail', ['filter' => 'TimFilter']);
$routes->post('/tim/pendataan/insert', 'Tim\Pendataan::insert', ['filter' => 'TimFilter']);
$routes->delete('/tim/pendataan/delete', 'Tim\Pendataan::delete', ['filter' => 'TimFilter']);


$routes->post('/admin/pie', 'Admin\Pendataan::pieChart', ['filter' => 'AdminFilter']);
$routes->post('/admin/stackedcolumns', 'Admin\Pendataan::stackedColumnsChart', ['filter' => 'AdminFilter']);
$routes->post('/admin/pie-bantuan', 'Admin\Pendataan::pieChartBantuan', ['filter' => 'AdminFilter']);
$routes->post('/admin/stackedcolumns-bantuan', 'Admin\Pendataan::stackedColumnsChartBantuan', ['filter' => 'AdminFilter']);
// $routes->post('/admin/progres', 'Admin\Pendataan::pieChartProgres', ['filter' => 'AdminFilter']);
$routes->post('/admin/bar-drilldown', 'Admin\Pendataan::barDrilldown', ['filter' => 'AdminFilter']);



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
