<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
 
    public function indexsuperadmin()
    {
        return view('dashboard.superadmin');  // Halaman dashboard admin
    }

    public function indexmarketing()
    {
        return view('dashboard.marketing');  // Halaman dashboard admin
    }
}
