<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// باقي الكود زي ما هو
require_once '../core/App.php';
require_once '../core/Controller.php';
require_once '../core/Database.php';
// ...
class App
{
    // protected $controller = 'HomeController';
    // protected $method = 'index';
 
    protected $controller = 'AuthController';
    protected $method = 'login';
    
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // Controller
        if (isset($url[0]) && file_exists("../app/controllers/" . ucfirst($url[0]) . "Controller.php")) {
            $this->controller = ucfirst($url[0]) . "Controller";
            unset($url[0]);
        }

        require_once "../app/controllers/{$this->controller}.php";
        $this->controller = new $this->controller;

        // Method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Params
        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
