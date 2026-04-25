<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use Illuminate\Http\Request;

class KuesionerController extends Controller
{
    /**
     * Show kuesioner management page
     */
    public function index()
    {
        $kuesioner = Questionnaire::paginate(15);
        return view('admin.kuesioner', compact('kuesioner'));
    }
}
