<?php

namespace Terranet\Administrator\Controllers;

class DashboardController extends AdminController
{
    public function index()
    {
        return view(app('scaffold.template')->layout('dashboard'));
    }
}
