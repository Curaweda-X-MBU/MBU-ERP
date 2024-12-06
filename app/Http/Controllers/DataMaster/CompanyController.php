<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\DataMaster\Company;
use DB;

class CompanyController extends Controller
{
    private const VALIDATION_RULES = [
        'address' => 'required|max:100'
    ];

    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama unit bisnis tidak boleh kosong',
        'name.max' => 'Nama unit bisnis melebihi 50 karakter',
        'name.unique' => 'Nama unit bisnis telah digunakan',
        'alias.required' => 'Nama alias tidak boleh kosong',
        'alias.max' => 'Nama alias tidak boleh melebihi 3 karakter',
        'alias.unique' => 'Nama alias telah digunakan',
        'address.required' => 'Alamat tidak boleh kosong',
        'address.max' => 'Alamat melebihi 100 karakter'
    ];

    public function index(Request $req) {
        try {
            
            $param = [
                'title' => 'Master Data > Unit Bisnis',
                'data' => Company::get()
            ];
            return view('data-master.company.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage()
            ])->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > Unit Bisnis > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('companies')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })
                ];
                $rules['alias'] = ['required', 'string', 'max:3',
                    Rule::unique('companies')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                Company::create([
                    'name' => $req->input('name'),
                    'alias' => $req->input('alias'),
                    'address' => $req->input('address'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('data-master.company.index')->with($success);
            }

            return view('data-master.company.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $company = Company::findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Unit Bisnis > Ubah',
                'data' => $company,
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('companies')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })->ignore($company->company_id, 'company_id')
                ];
                $rules['alias'] = ['required', 'string', 'max:3',
                    Rule::unique('companies')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })->ignore($company->company_id, 'company_id')
                ];
                
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $company->update([
                    'name' => $req->input('name'),
                    'alias' => $req->input('alias'),
                    'address' => $req->input('address'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('data-master.company.index')->with($success);
            }

            return view('data-master.company.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $company = Company::findOrFail($req->id);
            $company->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('data-master.company.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchCompany(Request $request) {
        $search = $request->input('q');
        $companies = Company::where('name', 'like', "%{$search}%")->get();

        return response()->json($companies->map(function ($company) {
            return ['id' => $company->company_id, 'text' => $company->name];
        }));
    }
}
