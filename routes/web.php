<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/test-mongo', function () {
    DB::connection('mongodb')->getMongoClient();
    return "MongoDB Connected!";
});