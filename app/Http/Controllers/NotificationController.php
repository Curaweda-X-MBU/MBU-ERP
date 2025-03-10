<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $req)
    {
        try {
            $param = [
                'title'       => 'Notifikasi',
                'currentRole' => auth()->user()->role->name,
            ];

            return view('notification.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
