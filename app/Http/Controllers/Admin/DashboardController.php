<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_admins' => Admin::count(),
            'avg_focus' => 7.8,
            'avg_screen' => 4.2,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}