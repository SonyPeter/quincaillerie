<?php

/**
 * QUINCAILLERIE DE LA LIBERTÉ
 * Point d'entrée unique de l'application (Front Controller)
 */

session_start();

define('BASE_PATH', __DIR__);
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

require_once BASE_PATH . '/config/connect.php';
require_once BASE_PATH . '/config/security.php';
require_once BASE_PATH . '/core/Autoloader.php';
require_once BASE_PATH . '/core/Router.php';

$router = new Router();

$router->get('/', 'homeController@index');
$router->get('/login', 'authController@showLogin');
$router->post('/login', 'authController@login');
$router->post('/logout', 'authController@logout');

$router->get('/dashboard', 'dashboardController@index');

$router->get('/categories', 'categoryController@index');
$router->post('/categories', 'categoryController@store');

$router->get('/produits', 'productController@index');
$router->post('/produits', 'productController@store');

$router->get('/achats', 'achatController@index');
$router->post('/achats', 'achatController@store');

$router->get('/ventes', 'venteController@index');
$router->post('/ventes', 'venteController@store');

$router->get('/transactions', 'transactionController@index');

$router->get('/caisse', 'caisseController@index');
$router->post('/caisse', 'caisseController@store');

$router->get('/utilisateurs', 'accountController@index');
$router->post('/utilisateurs', 'accountController@store');

$router->get('/messages', 'messageController@index');
$router->post('/messages', 'messageController@store');

$router->dispatch();
