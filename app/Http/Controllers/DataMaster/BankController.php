<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\DataMaster\Bank;
use DB;

class BankController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama bank tidak boleh kosong',
        'name.max' => 'Nama bank melebihi 50 karakter',
        'account_number.required' => 'Nomor rekening tidak boleh kosong',
        'account_number.max' => 'Nomor rekening melebihi 50 karakter',
        'account_number.unique' => 'Nomor rekening telah digunakan',
        'owner.required' => 'Atas nama tidak boleh kosong',
        'owner.max' => 'Atas nama melebihi 50 karakter',
        'alias.required' => 'Alias tidak boleh kosong',
        'alias.max' => 'Atas nama melebihi 20 karakter',
    ];

    private const VALIDATION_RULES = [
        'name' => 'required|max:50',
        'account_number' => 'required|max:50',
        'owner' => 'required|max:50',
        'alias' => 'required|max:20'
    ];

    public function index(Request $req) {
        try {
            
            $param = [
                'title' => 'Master Data > Bank',
                'data' => Bank::get()
            ];
            return view('data-master.bank.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > Bank > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['account_number'] = ['required', 'string', 'max:50',
                    Rule::unique('banks')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                Bank::create([
                    'name' => $req->input('name'),
                    'owner' => $req->input('owner'),
                    'account_number' => $req->input('account_number'),
                    'alias' => $req->input('alias')
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('data-master.bank.index')->with($success);
            }

            return view('data-master.bank.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $bank = Bank::findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Bank > Ubah',
                'data' => $bank,
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['account_number'] = ['required', 'string', 'max:50',
                    Rule::unique('banks')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })->ignore($bank->bank_id, 'bank_id')
                ];
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $bank->update([
                    'name' => $req->input('name'),
                    'owner' => $req->input('owner'),
                    'account_number' => $req->input('account_number'),
                    'alias' => $req->input('alias')
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('data-master.bank.index')->with($success);
            }

            return view('data-master.bank.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $bank = Bank::findOrFail($req->id);
            $bank->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('data-master.bank.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchBank(Request $request) {
        $search = $request->input('q');
        $banks = Bank::where('alias', 'like', "%{$search}%")
            ->orWhere('owner', 'like', "%{$search}%")
            ->orWhere('account_number', 'like', "%{$search}%")
            ->get();

        return response()->json($banks->map(function ($val) {
            return ['id' => $val->bank_id, 'text' => $val->alias.' - '.$val->account_number.' - '.$val->owner];
        }));
    }
}
