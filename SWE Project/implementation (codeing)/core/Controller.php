<?php

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);
        $path = "../app/views/{$view}.php";
        
        if (!file_exists($path)) {
            die("View not found: " . $path);
        }
        
        require_once $path;
    }
}