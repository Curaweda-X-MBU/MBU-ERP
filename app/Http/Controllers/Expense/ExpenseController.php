<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        try {
            $param = [
                'title' => 'Biaya > List',
            ];

            return view('expense.index', $param);
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

            return view('expense.add', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function detail(Expense $expense)
    {
        try {
            $data = $expense->load([
                'created_user',
                'location',
                'expense_kandang',
                'expense_main_prices',
                'expense_addit_prices',
            ]);

            $param = [
                'title' => 'Biaya > Detail',
                'data'  => $data,
            ];

            return view('expense.detail', $param);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Request $req, $id)
    {
        try {
            $param = [
                'title' => 'Biaya > Edit',
            ];

            return view('expense.edit', $param);
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
