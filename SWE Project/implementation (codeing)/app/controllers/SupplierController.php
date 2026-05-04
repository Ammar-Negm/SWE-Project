<?php

class SupplierController extends Controller
{
    public function dashboard()
    {
        $this->view("supplier/dashboard");
    }
    public function invoice()
{
    $this->view("supplier/invoice-manager");
}

public function orders()
{
    $this->view("supplier/purchase-orders");
}
}