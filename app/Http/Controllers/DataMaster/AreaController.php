<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama area tidak boleh kosong',
        'name.max'      => 'Nama area melebihi 50 karakter',
        'name.unique'   => 'Nama area telah digunakan',
    ];

    public function index(Request $req)
    {
        try {

            $param = [
                'title' => 'Master Data > Area',
                'data'  => Area::get(),
            ];

            return view('data-master.area.index', $param);
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
                'title' => 'Master Data > Area > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = ['name' => ['required', 'string', 'max:50',
                    Rule::unique('areas')->where(function($query) {
                        return $query->whereNull('deleted_at');
                    })],
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                Area::create([
                    'name' => $req->input('name'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('data-master.area.index')->with($success);
            }

            return view('data-master.area.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $area  = Area::findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Area > Ubah',
                'data'  => $area,
            ];

            if ($req->isMethod('post')) {
                $rules = ['name' => ['required', 'string', 'max:50',
                    Rule::unique('areas')->where(function($query) {
                        return $query->whereNull('deleted_at');
                    })->ignore($area->area_id, 'area_id')],
                ];
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $area->update([
                    'name' => $req->input('name'),

                ]);

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('data-master.area.index')->with($success);
            }

            return view('data-master.area.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $area = Area::findOrFail($req->id);
            $area->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('data-master.area.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchArea(Request $request)
    {
        $search = $request->input('q');
        $areas  = Area::where('name', 'like', "%{$search}%")->get();

        return response()->json($areas->map(function($area) {
            return ['id' => $area->area_id, 'text' => $area->name];
        }));
    }
}
