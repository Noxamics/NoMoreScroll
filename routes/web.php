<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\KuesionerController;
use App\Http\Controllers\Admin\RuleController;

Route::get('/admin/dashboard', fn() => view('admin.dashboard'));
Route::get('/admin/users', fn() => view('admin.users'));
Route::get('/admin/monitoring', fn() => view('admin.monitoring'));
Route::get('/admin/kuesioner', fn() => view('admin.kuesioner'));
Route::get('/admin/rules', fn() => view('admin.rules'));