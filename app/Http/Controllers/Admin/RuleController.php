<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    /**
     * Show rules management page
     */
    public function index()
    {
        $rules = Recommendation::all();
        return view('admin.rules', compact('rules'));
    }
}
