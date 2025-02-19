<?php

namespace App\Http\Controllers\DataMaster;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    private const VALIDATION_RULES = [
        'type'     => 'required',
        'hatchery' => 'nullable',
        'pic_name' => 'required|string|max:50',
        'address'  => 'required|string|max:100',
        'email'    => 'nullable|email|max:50',
        'phone'    => [
            'nullable',
            'regex:/^([0-9\s\-\+\(\)]*)$/',
            'min:10',
            'max:15',
        ],
    ];

    private const VALIDATION_MESSAGES = [
        'name.required'     => 'Nama Pemasok tidak boleh kosong',
        'name.max'          => 'Nama Pemasok melebihi 50 karakter',
        'name.unique'       => 'Nama Pemasok telah digunakan',
        'alias.required'    => 'Nama Alias tidak boleh kosong',
        'alias.max'         => 'Nama Alias tidak boleh melebihi 3 karakter',
        'alias.unique'      => 'Nama Alias telah digunakan',
        'type.required'     => 'Tipe tidak boleh kosong',
        'pic_name.required' => 'Nama PIC tidak boleh kosong',
        'pic_name.max'      => 'Nama PIC melebihi 50 karakter',
        'address.required'  => 'Alamat tidak boleh kosong',
        'address.max'       => 'Alamat melebihi 100 karakter',
        'email.email'       => 'Alamat email tidak sesuai standar',
        'email.max'         => 'Alamat email melebihi 50 karakter',
        'phone.regex'       => 'No telepon tidak sesuai standar',
        'phone.min'         => 'No telepon kurang dari 10 karakter',
        'phone.max'         => 'No telepon lebih dari 50 karakter',
    ];

    public function index(Request $req)
    {
        try {
            $param = [
                'title' => 'Master Data > Pemasok',
                'data'  => Supplier::get(),
                'type'  => Constants::SUPPLIER_TYPE,
            ];

            return view('data-master.supplier.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Master Data > Pemasok > Tambah',
                'type'  => Constants::SUPPLIER_TYPE,
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('suppliers')->where(function($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ];
                $rules['alias'] = ['required', 'string', 'max:3',
                    Rule::unique('suppliers')->where(function($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                Supplier::create([
                    'name'     => $req->input('name'),
                    'alias'    => $req->input('alias'),
                    'pic_name' => $req->input('pic_name'),
                    'type'     => $req->input('type'),
                    'hatchery' => json_encode($req->input('hatchery')),
                    'phone'    => $req->input('phone'),
                    'email'    => $req->input('email'),
                    'address'  => $req->input('address'),
                    'tax_num'  => $req->input('tax_num'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('data-master.supplier.index')->with($success);
            }

            return view('data-master.supplier.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $supplier = Supplier::findOrFail($req->id);
            $param    = [
                'title' => 'Master Data > Pemasok > Ubah',
                'data'  => $supplier,
                'type'  => Constants::SUPPLIER_TYPE,
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('suppliers')->where(function($query) {
                        return $query->whereNull('deleted_at');
                    })->ignore($supplier->supplier_id, 'supplier_id'),
                ];
                $rules['alias'] = ['required', 'string', 'max:3',
                    Rule::unique('suppliers')->where(function($query) {
                        return $query->whereNull('deleted_at');
                    })->ignore($supplier->supplier_id, 'supplier_id'),
                ];
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $supplier->update([
                    'name'     => $req->input('name'),
                    'alias'    => $req->input('alias'),
                    'pic_name' => $req->input('pic_name'),
                    'type'     => $req->input('type'),
                    'hatchery' => json_encode($req->input('hatchery')),
                    'phone'    => $req->input('phone'),
                    'email'    => $req->input('email'),
                    'address'  => $req->input('address'),
                    'tax_num'  => $req->input('tax_num'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('data-master.supplier.index')->with($success);
            }

            return view('data-master.supplier.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $supplier = Supplier::findOrFail($req->id);
            $supplier->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('data-master.supplier.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchSupplier(Request $request)
    {
        $search    = $request->input('q');
        $suppliers = Supplier::with(['products.uom', 'products.product_category'])->where('name', 'like', "%{$search}%")->get();

        return response()->json($suppliers->map(function($supplier) {
            return ['id' => $supplier->supplier_id, 'text' => $supplier->name, 'data' => $supplier];
        }));
    }

    public function searchHatchery(Request $request)
    {
        $search      = $request->input('q');
        $data        = null;
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        if ($queryParams) {
            foreach ($queryParams as $key => $value) {
                $data = Supplier::where($key, $value);
            }
            $data = $data->first();
        }

        $arrHatchery         = $data ? json_decode($data->hatchery) ?? [] : [];
        $arrHatcheryResponse = [];
        foreach ($arrHatchery as $val) {
            $arrHatcheryResponse[] = ['id' => $val, 'text' => $val];
        }

        return response()->json($arrHatcheryResponse);

    }
}
