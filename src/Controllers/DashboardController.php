<?php

namespace Terranet\Administrator\Controllers;

use Terranet\Administrator\Architect;

class DashboardController extends AdminController
{
    public function index()
    {
        return view(Architect::template()->layout('dashboard'));
    }
}
