<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Constants;
use Illuminate\Validation\Rule;
use App\Models\DataMaster\Customer;
use DB;

class CustomerController extends Controller
{
    private const VALIDATION_RULES = [
        'type' => 'required',
        'assign_to' => 'required',
        'address' => 'required|string|max:100',
        'email' => 'nullable|email|max:50',
        'phone' => [
            'nullable',
            'regex:/^([0-9\s\-\+\(\)]*)$/',
            'min:10',
            'max:15',
        ]
    ];

    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama Pelanggan tidak boleh kosong',
        'name.max' => 'Nama Pelanggan melebihi 50 karakter',
        'name.unique' => 'Nama Pelanggan telah digunakan',
        'type.required' => 'Tipe tidak boleh kosong',
        'assign_to.required' => 'Nama Penanggung jawab tidak boleh kosong',
        'address.required' => 'Alamat tidak boleh kosong',
        'address.max' => 'Alamat melebihi 100 karakter',
        'email.email' => 'Alamat email tidak sesuai standar',
        'email.max' => 'Alamat email melebihi 50 karakter',
        'phone.regex' => 'No telepon tidak sesuai standar',
        'phone.min' => 'No telepon kurang dari 10 karakter',
        'phone.max' => 'No telepon lebih dari 50 karakter',
    ];

    public function index(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > Pelanggan',
                'data' => Customer::with('user')->get(),
                'type' => Constants::CUSTOMER_TYPE
            ];
            return view('data-master.customer.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > Pelanggan > Tambah',
                'type' => Constants::CUSTOMER_TYPE
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('customers')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                Customer::create([
                    'name' => $req->input('name'),
                    'assign_to' => $req->input('assign_to'),
                    'type' => $req->input('type'),
                    'phone' => $req->input('phone'),
                    'email' => $req->input('email'),
                    'address' => $req->input('address'),
                    'tax_num' => $req->input('tax_num'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('data-master.customer.index')->with($success);
            }

            return view('data-master.customer.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $customer = Customer::findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Pelanggan > Ubah',
                'data' => $customer,
                'type' => Constants::CUSTOMER_TYPE
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('customers')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })->ignore($customer->customer_id, 'customer_id')
                ];
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $customer->update([
                    'name' => $req->input('name'),
                    'assign_to' => $req->input('assign_to'),
                    'type' => $req->input('type'),
                    'phone' => $req->input('phone'),
                    'email' => $req->input('email'),
                    'address' => $req->input('address'),
                    'tax_num' => $req->input('tax_num'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('data-master.customer.index')->with($success);
            }

            return view('data-master.customer.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $customer = Customer::findOrFail($req->id);
            $customer->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('data-master.customer.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
