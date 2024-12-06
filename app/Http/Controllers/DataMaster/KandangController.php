<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Constants;
use App\Models\UserManagement\User;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Location;
use App\Models\DataMaster\Company;
use Illuminate\Support\Arr;
use DB;

class KandangController extends Controller
{
    private const VALIDATION_RULES = [
        'capacity' => 'required|integer|max:100000',
        'type' => 'required',
        'pic' => 'required',
        'location_id' => 'required',
        'company_id' => 'required',
        
    ];

    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama kandang tidak boleh kosong',
        'name.unique' => 'Nama kandang sudah digunakan',
        'name.max' => 'Nama kandang melebihi 50 karakter',
        'capacity.required' => 'Kapasitas tidak boleh kosong',
        'capacity.integer' => 'Kapasitas harus berupa nomor',
        'capacity.max' => 'Kapasitas melebihi 100000',
        'type.required' => 'Tipe tidak boleh kosong',
        'pic.required' => 'PIC tidak boleh kosong',
        'location_id.required' => 'Lokasi tidak boleh kosong',
        'company_id.required' => 'Unit bisnis tidak boleh kosong',
    ];

    public function index(Request $req) {
        try {
            $data = Kandang::with(['location', 'company', 'user'])->get();
            $param = [
                'title' => 'Master Data > Kandang',
                'data' => $data,
                'type' => Constants::KANDANG_TYPE
            ];
            return view('data-master.kandang.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > Tambah',
                'type' => Constants::KANDANG_TYPE,
            ];
            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = [ 'required', 'string', 'max:50',
                    Rule::unique('kandang')->where(function ($query) use ($req) {
                        $query->where('location_id', $req->location_id);
                        $query->whereNull('deleted_at');
                        return $query;
                    })
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->all();
                    if(isset($input['company_id'])) {
                        $input['company_name'] = Company::find($req->input('company_id'))->name;
                    }
                    if(isset($input['location_id'])) {
                        $input['location_name'] = Location::find($req->input('location_id'))->name;
                    }
                    if(isset($input['pic'])) {
                        $input['pic_name'] = User::find($req->input('pic'))->name;
                    }
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                Kandang::create([
                    'name' => $req->input('name'),
                    'capacity' => $req->input('capacity'),
                    'type' => $req->input('type'),
                    'pic' => $req->input('pic'),
                    'company_id' => $req->input('company_id'),
                    'location_id' => $req->input('location_id'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('data-master.kandang.index')->with($success);
            }

            return view('data-master.kandang.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $kandang = Kandang::with(['location', 'company', 'user'])->findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Ubah',
                'data' => $kandang,
                'type' => Constants::KANDANG_TYPE,
            ];

            if ($req->isMethod('post')) {
                $dataUpdate = [
                    'name' => $req->input('name'),
                    'capacity' => $req->input('capacity'),
                    'type' => $req->input('type'),
                    'pic' => $req->input('pic'),
                    'company_id' => $req->input('company_id'),
                    'location_id' => $req->input('location_id'),
                ];

                $rules = self::VALIDATION_RULES;
                $rules['name'] = [ 'required', 'string', 'max:50',
                    Rule::unique('kandang')->where(function ($query) use ($req) {
                        $query->where('location_id', $req->location_id);
                        $query->whereNull('deleted_at');
                        return $query;
                    })->ignore($kandang->kandang_id, 'kandang_id')
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $kandang->update($dataUpdate);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('data-master.kandang.index')->with($success);
            }

            return view('data-master.kandang.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $kandang = Kandang::findOrFail($req->id);
            $kandang->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('data-master.kandang.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchKandang(Request $request) {
        $search = $request->input('q');
        $query = Kandang::with(['user'])->where('name', 'like', "%{$search}%");
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            $query->where($key, $value);
        }

        $data = $query->get();

        return response()->json($data->map(function ($val) {
            $active = $val->project_status?' ( Aktif )':''; 
            return ['id' => $val->kandang_id, 'text' => $val->name.$active, 'data' => $val];
        }));
    }
}
