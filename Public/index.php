<?php

require '../vendor/autoload.php';

use Framework\Router;

session_start();

require '../helpers.php';

///require '../helpers.php';
//require basePath('Framework/Router.php');
//require basePath('Framework/Database.php');
// $config = require basePath('config/db.php');

// $db = new Database($config);

//Instatiate the router
$router = new Router();

//Get routes
$routes = require basePath('routes.php');

// Get current URI and normalize it for subfolder deployment
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
    if ($uri === '') {
        $uri = '/';
    }
}

// Route the request
$router->route($uri);

