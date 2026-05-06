<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Controller.php';
class App
{
    protected $controller = 'AuthController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
    
        $url = $this->parseUrl();

        // ======================
        // CONTROLLER
        // ======================
        if (isset($url[0]) && $url[0] != '') {

            $controllerName = ucfirst($url[0]) . "Controller";
            $controllerPath = "../app/controllers/{$controllerName}.php";

            if (file_exists($controllerPath)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        $controllerPath = "../app/controllers/{$this->controller}.php";

if (!file_exists($controllerPath)) {
    die("Controller not found: " . $controllerPath);
}

require_once $controllerPath;

if (!class_exists($this->controller)) {
    die("Controller class not found: " . $this->controller);
}

$this->controller = new $this->controller;

        // ======================
        // METHOD
        // ======================
        // if (isset($url[1]) && $url[1] != '') {
        //     if (method_exists($this->controller, $url[1])) {
        //         $this->method = $url[1];
        //         unset($url[1]);
        //     }
        // }
        $this->method = 'index';

if (isset($url[1]) && !empty($url[1])) {
    if (method_exists($this->controller, $url[1])) {
        $this->method = $url[1];
        unset($url[1]);
    }
}

        // ======================
        // PARAMETERS
        // ======================
        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl()
    {
        if (isset($_GET['url']) && !empty($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
