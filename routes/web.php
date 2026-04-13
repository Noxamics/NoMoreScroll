<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\KuesionerController;
use App\Http\Controllers\Admin\RuleController;

// ════════════════════════════════════════════════════
// Admin Authentication Routes (PUBLIC)
// ════════════════════════════════════════════════════
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');

// ════════════════════════════════════════════════════
// Admin Dashboard Routes (PROTECTED - JWT)
// ════════════════════════════════════════════════════
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('admin.monitoring');
    Route::get('/kuesioner', [KuesionerController::class, 'index'])->name('admin.kuesioner');
    Route::get('/rules', [RuleController::class, 'index'])->name('admin.rules');
});