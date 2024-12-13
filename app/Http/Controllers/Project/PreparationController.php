<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PreparationController extends Controller
{
    public function index(Request $req)
    {
        try {
            $param = [
                'title' => 'Project > Persiapan',
            ];

            return view('project.preparation.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
