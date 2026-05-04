<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../core/App.php';
require_once '../core/Controller.php';
require_once '../core/Database.php';

// define("BASE_URL", '/' . basename(dirname(__DIR__)) . '/public/');
define("BASE_URL", 'index.php?url=');

$app = new App();