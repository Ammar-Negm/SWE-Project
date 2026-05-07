<?php
 session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error) {
        print_r($error);
    }
});
require_once '../core/App.php';
require_once '../core/Controller.php';
require_once '../core/Database.php';

//  define("BASE_URL", '/' . basename(dirname(__DIR__)) . '/public/');
// define("BASE_URL", 'index.php?url=');
//  define("BASE_URL", '/Php project/SWE-Project/SWE Project/implementation (codeing)/public/');
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define("BASE_URL", rtrim($basePath, '/') . '/');


$app = new App();