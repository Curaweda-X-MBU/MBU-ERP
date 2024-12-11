<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Nonstock;
use App\Models\DataMaster\Uom;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NonstockController extends Controller
{
    private const VALIDATION_RULES = [
        'uom_id' => 'required',
    ];

    private const VALIDATION_MESSAGES = [
        'name.required'   => 'Nama tidak boleh kosong',
        'name.max'        => 'Nama melebihi 50 karakter',
        'name.unique'     => 'Nama telah digunakan',
        'uom_id.required' => 'UOM tidak boleh kosong',
    ];

    public function index(Request $req)
    {
        try {
            $param = [
                'title' => 'Master Data > Non Stock',
                'data'  => Nonstock::get(),
            ];

            return view('data-master.nonstock.index', $param);
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
                'title' => 'Master Data > Non Stock > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('nonstocks')->where(function($query) {
                        $query->whereNull('deleted_at');

                        return $query;
                    }),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->input();
                    if (isset($input['uom_id'])) {
                        $input['uom_name'] = Uom::find($req->input('uom_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                Nonstock::create([
                    'name'   => $req->input('name'),
                    'uom_id' => $req->input('uom_id'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('data-master.nonstock.index')->with($success);
            }

            return view('data-master.nonstock.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $nonstock = Nonstock::with(['uom'])->findOrFail($req->id);
            $param    = [
                'title' => 'Master Data > Non Stock > Ubah',
                'data'  => $nonstock,
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('nonstocks')->where(function($query) {
                        $query->whereNull('deleted_at');

                        return $query;
                    })->ignore($nonstock->nonstock_id, 'nonstock_id'),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $nonstock->update([
                    'name'   => $req->input('name'),
                    'uom_id' => $req->input('uom_id'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('data-master.nonstock.index')->with($success);
            }

            return view('data-master.nonstock.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $nonstock = Nonstock::findOrFail($req->id);
            $nonstock->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('data-master.nonstock.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchNonstock(Request $request)
    {
        $search      = $request->input('q');
        $nonstocks   = Nonstock::with('uom')->where('name', 'like', "%{$search}%");
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            $nonstocks->where($key, $value);
        }

        $nonstocks = $nonstocks->get();

        return response()->json($nonstocks->map(function($nonstock) {
            return ['id' => $nonstock->nonstock_id, 'text' => $nonstock->name, 'data' => $nonstock];
        }));
    }
}
