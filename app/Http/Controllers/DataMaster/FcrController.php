<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Fcr;
use App\Models\DataMaster\Product;
use App\Models\DataMaster\Uom;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FcrController extends Controller
{
    private const VALIDATION_RULES = [
        'value'      => 'required',
        'product_id' => 'required',
        'uom_id'     => 'required',
    ];

    private const VALIDATION_MESSAGES = [
        'name.required'       => 'Nama FCR tidak boleh kosong',
        'name.max'            => 'Nama FCR melebihi 50 karakter',
        'name.unique'         => 'Nama FCR telah digunakan',
        'value.required'      => 'Nilai FCR tidak boleh kosong',
        'product_id.required' => 'Poduk tidak boleh kosong',
        'uom_id.required'     => 'UOM tidak boleh kosong',
    ];

    public function index(Request $req)
    {
        try {

            $param = [
                'title' => 'Master Data > FCR',
                'data'  => Fcr::get(),
            ];

            return view('data-master.fcr.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ])->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Master Data > FCR > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('fcr')->where(function($query) use ($req) {
                        $query->where('product_id', $req->product_id);
                        $query->whereNull('deleted_at');

                        return $query;
                    }),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->input();
                    if (isset($input['product_id'])) {
                        $input['product_name'] = Product::find($req->input('product_id'))->name;
                    }
                    if (isset($input['uom_id'])) {
                        $input['uom_name'] = Uom::find($req->input('uom_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                Fcr::create([
                    'name'       => $req->input('name'),
                    'value'      => $req->input('value'),
                    'product_id' => $req->input('product_id'),
                    'uom_id'     => $req->input('uom_id'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('data-master.fcr.index')->with($success);
            }

            return view('data-master.fcr.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $fcr   = Fcr::with(['product', 'uom'])->findOrFail($req->id);
            $param = [
                'title' => 'Master Data > FCR > Ubah',
                'data'  => $fcr,
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('fcr')->where(function($query) use ($req) {
                        $query->where('product_id', $req->product_id);
                        $query->whereNull('deleted_at');

                        return $query;
                    })->ignore($fcr->fcr_id, 'fcr_id'),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $fcr->update([
                    'name'       => $req->input('name'),
                    'value'      => $req->input('value'),
                    'product_id' => $req->input('product_id'),
                    'uom_id'     => $req->input('uom_id'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('data-master.fcr.index')->with($success);
            }

            return view('data-master.fcr.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $fcr = Fcr::findOrFail($req->id);
            $fcr->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('data-master.fcr.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchFcr(Request $request)
    {
        $search      = $request->input('q');
        $fcrs        = Fcr::with('uom')->where('name', 'like', "%{$search}%");
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            $fcrs->where($key, $value);
        }

        $fcrs = $fcrs->get();

        return response()->json($fcrs->map(function($fcr) {
            return ['id' => $fcr->fcr_id, 'text' => $fcr->name.' - '.$fcr->value.' '.$fcr->uom->name];
        }));
    }
}
