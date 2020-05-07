<?php
include 'env.php';
if (APP_DEBUG == true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

session_start();
include 'includes/functions.php';
require __DIR__ . "/vendor/autoload.php";

use App\Helper\Helper;
use App\Controller\ErrorController;
use App\Controller\CatController;

#Routing
if (isset($_SERVER['PATH_INFO'])) {
    $path = $_SERVER['PATH_INFO'];
} else {
    $path = '/';
}
$path = explode('/', $path);
$helper = new Helper();

if (isset($path[1]) && !empty($path[1])) {
    $controller = $helper->getController($path[1]);
    if (isset($path[2]) && !empty($path[2])) {
        $method = $path[2];
    } else {
        $method = 'index';
    }
    if (class_exists($controller)) {
        $object = new $controller;
        if (method_exists($object, $method)) {
            if (isset($path[3]) && !empty($path[3])) {
                $id = $path[3];
                $object->{$method}($id);
            } else {
                $object->{$method}();
            }
        } else {
            $object = new ErrorController();
            $object->errorMethod();
        }
    } else {
        $object = new ErrorController();
        $object->errorPage();
    }
    
} else {
    $object = new CatController();
    $object->index();
}
