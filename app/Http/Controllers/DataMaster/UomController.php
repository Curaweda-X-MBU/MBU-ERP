<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\DataMaster\Uom;
use DB;

class UomController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama UOM tidak boleh kosong',
        'name.max' => 'Nama UOM melebihi 50 karakter',
        'name.unique' => 'Nama UOM telah digunakan'
    ];

    public function index(Request $req) {
        try {
            
            $param = [
                'title' => 'Master Data > UOM',
                'data' => Uom::get()
            ];
            return view('data-master.uom.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage()
            ])->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > UOM > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = ['name' => ['required', 'string', 'max:50',
                    Rule::unique('uom')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })]
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                Uom::create([
                    'name' => $req->input('name'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('data-master.uom.index')->with($success);
            }

            return view('data-master.uom.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $uom = Uom::findOrFail($req->id);
            $param = [
                'title' => 'Master Data > UOM > Ubah',
                'data' => $uom,
            ];

            if ($req->isMethod('post')) {
                $rules = ['name' => ['required', 'string', 'max:50',
                    Rule::unique('uom')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })->ignore($uom->uom_id, 'uom_id')]
                ];
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $uom->update([
                    'name' => $req->input('name'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('data-master.uom.index')->with($success);
            }

            return view('data-master.uom.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $uom = Uom::findOrFail($req->id);
            $uom->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('data-master.uom.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchUom(Request $request) {
        $search = $request->input('q');
        $uoms = Uom::where('name', 'like', "%{$search}%")->get();

        return response()->json($uoms->map(function ($uom) {
            return ['id' => $uom->uom_id, 'text' => $uom->name];
        }));
    }
}
