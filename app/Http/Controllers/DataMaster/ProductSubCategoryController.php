<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\DataMaster\ProductSubCategory;
use App\Models\DataMaster\ProductCategory;
use DB;

class ProductSubCategoryController extends Controller
{
    private const VALIDATION_RULES = [
        'product_category_id' => 'required'
    ];

    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama sub kategori produk tidak boleh kosong',
        'name.max' => 'Nama sub kategori produk melebihi 50 karakter',
        'name.unique' => 'Nama sub kategori produk telah digunakan',
        'product_category_id.required' => 'Kategori Produk tidak boleh kosong'
    ];

    public function index(Request $req) {
        try {
            $data = ProductSubCategory::with('product_category')->get();
            $param = [
                'title' => 'Master Data > Sub Kategori Produk',
                'data' => $data
            ];
            return view('data-master.product-sub-category.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > Sub Kategori Produk > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = [ 'required', 'string', 'max:50',
                    Rule::unique('product_sub_categories')->where(function ($query) use ($req) {
                        $query->where('product_category_id', $req->product_category_id);
                        $query->whereNull('deleted_at');
                        return $query;
                    })
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->all();
                    if(isset($input['product_category_id'])) {
                        $oldData = ProductCategory::find($req->input('product_category_id'));
                        $input['product_category_name'] = $oldData->name;
                        $input['product_category_code'] = $oldData->category_code;
                    }
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                ProductSubCategory::create([
                    'name' => $req->input('name'),
                    'product_category_id' => $req->input('product_category_id'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('data-master.product-sub-category.index')->with($success);
            }

            return view('data-master.product-sub-category.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $productSubCategory = ProductSubCategory::with('product_category')->findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Sub Kategori Produk > Ubah',
                'data' => $productSubCategory,
            ];

            
            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = [ 'required', 'string', 'max:50',
                    Rule::unique('product_sub_categories')->where(function ($query) use ($req) {
                        $query->where('product_category_id', $req->product_category_id);
                        $query->whereNull('deleted_at');
                        return $query;
                    })->ignore($productSubCategory->product_sub_category_id, 'product_sub_category_id')
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $productSubCategory->update([
                    'name' => $req->input('name'),
                    'product_category_id' => $req->input('product_category_id'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('data-master.product-sub-category.index')->with($success);
            }

            return view('data-master.product-sub-category.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $productSubCategory = ProductSubCategory::findOrFail($req->id);
            $productSubCategory->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('data-master.product-sub-category.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchProductSubCategory(Request $request) {
        $search = $request->input('q');
        $query = ProductSubCategory::where('name', 'like', "%{$search}%");
        if($request->has('product_category_id')) {
            $productCategoryId = $request->query('product_category_id');
            $query->where('product_category_id', $productCategoryId);
        }
        $data = $query->get();

        return response()->json($data->map(function ($val) {
            return ['id' => $val->product_sub_category_id, 'text' => $val->name];
        }));
    }
}
