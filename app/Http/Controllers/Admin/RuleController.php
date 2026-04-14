<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RuleController extends Controller
{
    public function index()
    {
        // Ambil langsung dari collection MongoDB — tidak pakai model Recommendation
        $rules = collect(
            DB::connection('mongodb')
                ->collection('recommendation_rules')
                ->get()
        );

        return view('admin.rules', compact('rules'));
    }
}