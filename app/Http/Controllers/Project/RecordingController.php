<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecordingController extends Controller
{
    public function index(Request $req)
    {
        try {
            $param = [
                'title' => 'Project > Recording',
            ];

            return view('project.recording.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
