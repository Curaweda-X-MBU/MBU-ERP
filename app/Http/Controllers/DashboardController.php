<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function indexMbu(Request $req)
    {
        try {
            $data = [
                'title' => 'Home',
            ];

            return view('dashboard.mbu.index', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function indexLti(Request $req)
    {
        try {
            $data = [
                'title' => 'Home',
            ];

            return view('dashboard.lti.index', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function indexManbu(Request $req)
    {
        try {
            $data = [
                'title' => 'Home',
            ];

            return view('dashboard.manbu.index', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function sidebarToggle(Request $req): void
    {
        $currentSessionToggle = Session::get('sidebar-toggle', 'menu-expanded');

        if ($currentSessionToggle === 'menu-expanded') {
            Session::put('sidebar-toggle', 'menu-collapsed');
        } else {
            Session::put('sidebar-toggle', 'menu-expanded');
        }
    }
}
