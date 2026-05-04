<?php

class SupplierController extends Controller
{
    public function dashboard()
    {
        session_start();
        $this->view("supplier/dashboard");
    }
}