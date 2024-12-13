<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\DataMaster\Location;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Area;
use Illuminate\Support\Arr;
use DB;

class LocationController extends Controller
{
    private const VALIDATION_RULES = [
        'address' => 'required|string|max:100',
        'company_id' => 'required',
        'area_id' => 'required'
    ];

    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama lokasi tidak boleh kosong',
        'name.max' => 'Nama lokasi melebihi 50 karakter',
        'name.unique' => 'Nama lokasi telah digunakan',
        'address.required' => 'Alamat tidak boleh kosong',
        'address.max' => 'Alamat melebihi 100 karakter',
        'company_id.required' => 'Unit bisnis tidak boleh kosong',
        'area_id.required' => 'Area tidak boleh kosong'
    ];

    public function index(Request $req) {
        try {
            $data = Location::with(['company', 'area'])->get();
            $param = [
                'title' => 'Master Data > Lokasi',
                'data' => $data
            ];
            return view('data-master.location.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Master Data > Lokasi > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = [ 'required', 'string', 'max:50',
                    Rule::unique('locations')->where(function ($query) use ($req) {
                        $query->where('area_id', $req->area_id);
                        $query->where('company_id', $req->company_id);
                        $query->whereNull('deleted_at');
                        return $query;
                    })
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->all();
                    if(isset($input['company_id'])) {
                        $input['company_name'] = Area::find($req->input('company_id'))->name;
                    }
                    if(isset($input['area_id'])) {
                        $input['area_name'] = Area::find($req->input('area_id'))->name;
                    }
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                Location::create([
                    'name' => $req->input('name'),
                    'address' => $req->input('address'),
                    'company_id' => $req->input('company_id'),
                    'area_id' => $req->input('area_id'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('data-master.location.index')->with($success);
            }

            return view('data-master.location.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $location = Location::with(['company', 'area'])->findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Lokasi > Ubah',
                'data' => $location,
            ];

            
            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = [ 'required', 'string', 'max:50',
                    Rule::unique('locations')->where(function ($query) use ($req) {
                        $query->where('area_id', $req->area_id);
                        $query->where('company_id', $req->company_id);
                        $query->whereNull('deleted_at');
                        return $query;
                    })->ignore($location->location_id, 'location_id')
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $location->update([
                    'name' => $req->input('name'),
                    'address' => $req->input('address'),
                    'company_id' => $req->input('company_id'),
                    'area_id' => $req->input('area_id'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('data-master.location.index')->with($success);
            }

            return view('data-master.location.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $location = Location::findOrFail($req->id);
            $location->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('data-master.location.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchLocation(Request $request) {
        $search = $request->input('q');
        $locations = Location::with('area')->where('name', 'like', "%{$search}%");
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            $locations->where($key, $value);
        }

        $locations = $locations->get();
        return response()->json($locations->map(function ($location) {
            return ['id' => $location->location_id, 'text' => $location->name];
        }));
    }
}
