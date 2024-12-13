<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\DataMaster\ProductCategory;
use DB;

class ProductCategoryController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama Kategori Produk tidak boleh kosong',
        'name.max' => 'Nama Kategori Produk melebihi 50 karakter',
        'name.unique' => 'Nama Kategori Produk telah digunakan',
        'category_code.required' => 'Kode Kategori Produk tidak boleh kosong',
        'category_code.max' => 'Kode Kategori Produk melebihi 3 karakter',
        'category_code.unique' => 'Kode Kategori Produk telah digunakan'
    ];

    public function index(Request $req) {
        try {
            
            $param = [
                'title' => 'Master Data > Kategori Produk',
                'data' => ProductCategory::get()
            ];
            return view('data-master.product-category.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > Kategori Produk > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = [
                    'name' => ['required', 'string', 'max:50',
                        Rule::unique('product_categories')->where(function ($query) use ($req) {
                            return $query->whereNull('deleted_at');
                        })
                    ],
                    'category_code' => ['required', 'max:3',
                        Rule::unique('product_categories')->where(function ($query) use ($req) {
                            return $query->whereNull('deleted_at');
                        })
                    ],
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                ProductCategory::create([
                    'name' => $req->input('name'),
                    'category_code' => $req->input('category_code'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('data-master.product-category.index')->with($success);
            }

            return view('data-master.product-category.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $productCategory = ProductCategory::findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Kategori Produk > Ubah',
                'data' => $productCategory,
            ];

            if ($req->isMethod('post')) {
                $rules = [
                    'name' => ['required', 'string', 'max:50',
                        Rule::unique('product_categories')->where(function ($query) use ($req) {
                            return $query->whereNull('deleted_at');
                        })->ignore($productCategory->product_category_id, 'product_category_id')
                    ],
                    'category_code' => ['required', 'max:3',
                        Rule::unique('product_categories')->where(function ($query) use ($req) {
                            return $query->whereNull('deleted_at');
                        })->ignore($productCategory->product_category_id, 'product_category_id')
                    ],
                ];
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $productCategory->update([
                    'name' => $req->input('name'),
                    'category_code' => $req->input('category_code'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('data-master.product-category.index')->with($success);
            }

            return view('data-master.product-category.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $productCategory = ProductCategory::findOrFail($req->id);
            $productCategory->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('data-master.product-category.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchProductCategory(Request $request) {
        $search = $request->input('q');
        $productCategories = ProductCategory::where('name', 'like', "%{$search}%")->get();

        return response()->json($productCategories->map(function ($val) {
            return ['id' => $val->product_category_id, 'text' => $val->category_code.' - '.$val->name];
        }));
    }
}
