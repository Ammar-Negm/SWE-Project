<?php

class StaffController extends Controller
{
    public function dashboard()
    {
        session_start();
        $this->view("staff/dashboard");
    }
}