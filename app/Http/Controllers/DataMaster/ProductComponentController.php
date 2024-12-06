<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\DataMaster\ProductComponent;
use App\Models\DataMaster\Suppler;
use App\Models\DataMaster\Uom;
use DB;

class ProductComponentController extends Controller
{
    private const VALIDATION_RULES = [
        'brand' => 'required|max:50',
        'uom_id' => 'required',
        'supplier_id' => 'nullable',
        'price' => 'required|numeric|min:0',
        'tax' => 'nullable|numeric|between:0,100',
        'expiry_period' => 'nullable|numeric|min:0'
    ];

    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama tidak boleh kosong',
        'name.max' => 'Nama melebihi 50 karakter',
        'name.unique' => 'Nama telah digunakan',
        'brand.required' => 'Merek tidak boleh kosong',
        'brand.max' => 'Merek melebihi 50 karakter',
        'uom_id.required' => 'UOM tidak boleh kosong',
        'supplier_id.required' => 'Pemasok tidak boleh kosong',
        'price.required' => 'Harga tidak boleh kosong',
        'price.numeric' => 'Harga harus berupa angka',
        'price.min' => 'Harga minimal 0',
        'tax.numeric' => 'Pajak harus berupa angka',
        'tax.between' => 'Pajak minimal 0 maksimal 100',
        'expiry_period.numeric' => 'Periode Kadaluarsa harus berupa angka',
        'expiry_period.min' => 'Periode Kadaluarsa minimal 0'
    ];

    public function index(Request $req) {
        try {
            
            $param = [
                'title' => 'Master Data > Bahan Baku',
                'data' => ProductComponent::with(['supplier', 'uom'])->get()
            ];
            return view('data-master.product-component.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > Bahan Baku > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('product_components')->where(function ($query) use ($req) {
                        if($req->supplier_id) {
                            $query->where('supplier_id', $req->supplier_id);
                        }
                        $query->whereNull('deleted_at');
                        return $query;
                    })
                ];
                $plainNumber = $req->input('price')?(int) str_replace(',', '', $req->input('price')):null;
                $input = $req->all();
                $input['price'] = $plainNumber;
                $validator = Validator::make($input, $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                ProductComponent::create([
                    'name' => $req->input('name'),
                    'supplier_id' => $req->input('supplier_id'),
                    'brand' => $req->input('brand'),
                    'uom_id' => $req->input('uom_id'),
                    'tax' => $req->input('tax'),
                    'price' => $plainNumber,
                    'expiry_period' => $req->input('expiry_period'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('data-master.product-component.index')->with($success);
            }

            return view('data-master.product-component.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $productComponent = ProductComponent::with(['supplier', 'uom'])->findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Bahan Baku > Ubah',
                'data' => $productComponent,
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('product_components')->where(function ($query) use ($req) {
                        if($req->supplier_id) {
                            $query->where('supplier_id', $req->supplier_id);
                        }
                        $query->whereNull('deleted_at');
                        return $query;
                    })->ignore($productComponent->product_component_id, 'product_component_id')
                ];
                $plainNumber = $req->input('price')?(int) str_replace(',', '', $req->input('price')):null;
                $input = $req->all();
                $input['price'] = $plainNumber;
                $validator = Validator::make($input, $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $productComponent->update([
                    'name' => $req->input('name'),
                    'supplier_id' => $req->input('supplier_id'),
                    'brand' => $req->input('brand'),
                    'uom_id' => $req->input('uom_id'),
                    'tax' => $req->input('tax'),
                    'price' => $plainNumber,
                    'expiry_period' => $req->input('expiry_period'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('data-master.product-component.index')->with($success);
            }

            return view('data-master.product-component.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $productCategory = ProductComponent::findOrFail($req->id);
            $productCategory->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('data-master.product-component.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchProductComponent(Request $request) {
        $search = $request->input('q');
        $productComponents = ProductComponent::where('name', 'like', "%{$search}%")->get();

        return response()->json($productComponents->map(function ($val) {
            return ['id' => $val->product_component_id, 'text' => $val->name];
        }));
    }
}
