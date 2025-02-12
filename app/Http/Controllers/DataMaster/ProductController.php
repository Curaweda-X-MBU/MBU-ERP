<?php

namespace App\Http\Controllers\DataMaster;

use App\Helpers\Parser;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Product;
use App\Models\DataMaster\ProductCategory;
use App\Models\DataMaster\ProductSubCategory;
use App\Models\DataMaster\Uom;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private const VALIDATION_RULES = [
        'brand'                => 'required|max:50',
        'uom_id'               => 'required',
        'company_id'           => 'required',
        'product_category_id'  => 'required',
        'product_sub_category' => 'nullable',
        'product_price'        => 'required|numeric|min:0',
        'selling_price'        => 'nullable|numeric|min:0',
        'tax'                  => 'nullable|numeric|between:0,100',
        'expiry_period'        => 'nullable|numeric|min:0',
    ];

    private const VALIDATION_MESSAGES = [
        'name.required'                => 'Nama tidak boleh kosong',
        'name.max'                     => 'Nama melebihi 50 karakter',
        'name.unique'                  => 'Nama telah digunakan',
        'brand.required'               => 'Merek tidak boleh kosong',
        'brand.max'                    => 'Merek melebihi 50 karakter',
        'uom_id.required'              => 'UOM tidak boleh kosong',
        'company_id.required'          => 'Unit Bisnis tidak boleh kosong',
        'product_category_id.required' => 'Kategori Produk tidak boleh kosong',
        'product_price.required'       => 'Harga Produk tidak boleh kosong',
        'product_price.numeric'        => 'Harga Produk harus berupa angka',
        'product_price.min'            => 'Harga Produk minimal 0',
        'selling_price.numeric'        => 'Harga Jual harus berupa angka',
        'selling_price.min'            => 'Harga Jual minimal 0',
        'tax.numeric'                  => 'Pajak harus berupa angka',
        'tax.between'                  => 'Pajak minimal 0 maksimal 100',
        'expiry_period.numeric'        => 'Periode Kadaluarsa harus berupa angka',
        'expiry_period.min'            => 'Periode Kadaluarsa minimal 0',
    ];

    public function index(Request $req)
    {
        try {
            $param = [
                'title' => 'Master Data > Produk',
                'data'  => Product::with([
                    'company',
                    'product_category',
                    'product_sub_category',
                    'uom',
                ])->get(),
            ];

            return view('data-master.product.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Master Data > Produk > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('products')->where(function($query) use ($req) {
                        $query->where('company_id', $req->company_id);
                        $query->whereNull('deleted_at');

                        return $query;
                    }),
                ];

                $input                  = $req->all();
                $productPrice           = $req->input('product_price') ? (int) str_replace('.', '', $req->input('product_price')) : null;
                $input['product_price'] = $productPrice;
                if ($req->input('selling_price')) {
                    $sellingPrice           = (int) str_replace('.', '', $req->input('selling_price'));
                    $input['selling_price'] = $sellingPrice;
                }

                $validator = Validator::make($input, $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    if (isset($input['uom_id'])) {
                        $input['uom_name'] = Uom::find($req->input('uom_id'))->name;
                    }
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find($req->input('company_id'))->name;
                    }
                    if (isset($input['product_category_id'])) {
                        $input['product_category_name'] = ProductCategory::find($req->input('product_category_id'))->name;
                    }
                    if (isset($input['product_sub_category_id'])) {
                        $input['product_sub_category_name'] = ProductSubCategory::find($req->input('product_sub_category_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                $product = Product::create([
                    'name'                    => $req->input('name'),
                    'company_id'              => $req->input('company_id'),
                    'product_category_id'     => $req->input('product_category_id'),
                    'product_sub_category_id' => $req->input('product_sub_category_id'),
                    'brand'                   => $req->input('brand'),
                    'sku'                     => $req->input('sku'),
                    'uom_id'                  => $req->input('uom_id'),
                    'tax'                     => $req->input('tax'),
                    'product_price'           => $input['product_price'],
                    'selling_price'           => $input['selling_price'],
                    'expiry_period'           => $req->input('expiry_period'),
                    'can_be_sold'             => $req->input('can_be_sold') ? true : false,
                    'can_be_purchased'        => $req->input('can_be_purchased') ? true : false,
                    'is_active'               => $req->input('is_active') ? true : false,
                ]);

                // Sync suppliers
                $suppliersToSync = [];
                foreach ($req->input('product_supplier') ?? [] as $key => $value) {
                    if (isset($value['supplier_id']) ?? false) {
                        $suppliersToSync[$value['supplier_id']] = [
                            'product_price' => Parser::parseLocale($value['product_price']) ?? 0,
                            'selling_price' => $input['selling_price'],
                        ];
                    }
                }

                $product->suppliers()->sync($suppliersToSync);

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('data-master.product.index')->with($success);
            }

            return view('data-master.product.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $product = Product::with([
                'company',
                'product_category',
                'product_sub_category',
                'uom',
                'suppliers',
            ])->findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Produk > Ubah',
                'data'  => $product,
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('products')->where(function($query) use ($req) {
                        $query->where('company_id', $req->company_id);
                        $query->whereNull('deleted_at');

                        return $query;
                    })->ignore($product->product_id, 'product_id'),
                ];
                $input                  = $req->all();
                $productPrice           = $req->input('product_price') ? (int) str_replace('.', '', $req->input('product_price')) : null;
                $input['product_price'] = $productPrice;
                if ($req->input('selling_price')) {
                    $sellingPrice           = (int) str_replace('.', '', $req->input('selling_price'));
                    $input['selling_price'] = $sellingPrice;
                }
                $validator = Validator::make($input, $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $product->update([
                    'name'                    => $req->input('name'),
                    'company_id'              => $req->input('company_id'),
                    'product_category_id'     => $req->input('product_category_id'),
                    'product_sub_category_id' => $req->input('product_sub_category_id'),
                    'brand'                   => $req->input('brand'),
                    'sku'                     => $req->input('sku'),
                    'uom_id'                  => $req->input('uom_id'),
                    'tax'                     => $req->input('tax'),
                    'product_price'           => $input['product_price'],
                    'selling_price'           => $input['selling_price'],
                    'expiry_period'           => $req->input('expiry_period'),
                    'can_be_sold'             => $req->input('can_be_sold') ? true : false,
                    'can_be_purchased'        => $req->input('can_be_purchased') ? true : false,
                    'is_active'               => $req->input('is_active') ? true : false,
                ]);

                // Sync suppliers
                $suppliersToSync = [];
                foreach ($req->input('product_supplier') ?? [] as $key => $value) {
                    if (isset($value['supplier_id']) ?? false) {
                        $suppliersToSync[$value['supplier_id']] = ['product_price' => Parser::parseLocale($value['product_price']) ?? 0];
                    }
                }

                $product->suppliers()->sync($suppliersToSync);

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('data-master.product.index')->with($success);
            }

            return view('data-master.product.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $product = Product::findOrFail($req->id);
            $product->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('data-master.product.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchProduct(Request $request)
    {
        $search      = $request->input('q');
        $query       = Product::with(['uom', 'product_category'])->where('name', 'like', "%{$search}%");
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            $hasRelation = explode('-', $key);
            if (count($hasRelation) > 1) {
                $tblRelation = $hasRelation[0];
                $column      = $hasRelation[1];
                $query->whereHas($tblRelation, function($q) use ($column, $value) {
                    $q->where($column, $value);
                });
            } else {
                $query->where($key, $value);
            }
        }
        $query->orderBy('name');
        $data = $query->get();

        return response()->json($data->map(function($val) {
            return ['id' => $val->product_id, 'text' => $val->name, 'data' => $val];
        }));
    }
}
