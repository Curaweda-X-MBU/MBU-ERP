<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $req)
    {
        try {
            $query = Notification::where('is_done', 0);

            if (auth()->user()->role->name !== 'Super Admin') {
                $query->where('role_id', auth()->user()->role->role_id);
            }

            $data = $query->get()->groupBy('module');

            $param = [
                'title'       => 'Notifikasi',
                'currentRole' => auth()->user()->role->name,
                'data'        => $data,
            ];

            return view('notification.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
