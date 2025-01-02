<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        try {
            $currentUserId = auth()->id();

            $data = Expense::with(['location', 'expense_payments', 'created_user'])
                ->where(function($query) use ($currentUserId) {
                    $query->where('expense_status', '!=', 0)
                        ->orWhere(function($subQuery) use ($currentUserId) {
                            $subQuery->where('expense_status', 0)
                                ->whereHas('created_user', function($userQuery) use ($currentUserId) {
                                    $userQuery->where('user_id', $currentUserId);
                                });
                        });
                })
                ->get();

            $param = [
                'title' => 'Biaya > List',
                'data'  => $data,
            ];

            return view('expense.list.index', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function recap()
    {
        try {
            $param = [
                'title' => 'Biaya > List',
            ];

            return view('expense.recap.index', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Biaya > Tambah',
            ];

            return view('expense.list.add', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function detail(Request $id)
    {
        try {
            $param = [
                'title' => 'Biaya > Detail',
            ];

            return view('expense.list.detail', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Request $req, Expense $id)
    {
        try {
            $param = [
                'title' => 'Biaya > Edit',
            ];

            return view('expense.list.edit', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function delete(Request $id)
    {
        try {
            //
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function approve(Request $req, $id)
    {
        //
    }

    public function searchExpense(Request $req)
    {
        //
    }
}
