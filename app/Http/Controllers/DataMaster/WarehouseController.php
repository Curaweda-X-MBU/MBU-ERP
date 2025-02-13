<?php

namespace App\Http\Controllers\DataMaster;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Models\DataMaster\Area;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Kandang;
use App\Models\DataMaster\Location;
use App\Models\DataMaster\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    private const VALIDATION_RULES = [
        'type'        => 'required',
        'location_id' => 'required',
    ];

    private const VALIDATION_MESSAGES = [
        'name.required'        => 'Nama Gudang tidak boleh kosong',
        'name.max'             => 'Nama Gudang melebihi 50 karakter',
        'name.unique'          => 'Nama Gudang telah digunakan',
        'type.required'        => 'Tipe Gudang tidak boleh kosong',
        'location_id.required' => 'Lokasi tidak boleh kosong',
        'kandang_id.required'  => 'Kandang tidak boleh kosong',
    ];

    public function index(Request $req)
    {
        try {

            $param = [
                'title' => 'Master Data > Gudang',
                'data'  => Warehouse::get(),
                'type'  => Constants::WAREHOUSE_TYPE,
            ];

            return view('data-master.warehouse.index', $param);
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
                'title' => 'Master Data > Gudang > Tambah',
                'type'  => Constants::WAREHOUSE_TYPE,
            ];

            if ($req->isMethod('post')) {
                $rules               = self::VALIDATION_RULES;
                $rules['kandang_id'] = $req->input('type') == 2 ? ['required'] : ['nullable'];
                $rules['name']       = ['required', 'string', 'max:50',
                    Rule::unique('warehouses')->where(function($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->input();
                    if (isset($input['company_id'])) {
                        $input['company_name'] = company::find($req->input('company_id'))->name;
                    }
                    if (isset($input['area_id'])) {
                        $input['area_name'] = area::find($req->input('area_id'))->name;
                    }
                    if (isset($input['location_id'])) {
                        $input['location_name'] = Location::find($req->input('location_id'))->name;
                    }
                    if (isset($input['kandang_id'])) {
                        $input['kandang_name'] = Kandang::find($req->input('kandang_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                Warehouse::create([
                    'name'        => $req->input('name'),
                    'type'        => $req->input('type'),
                    'location_id' => $req->input('location_id'),
                    'kandang_id'  => $req->input('kandang_id'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('data-master.warehouse.index')->with($success);
            }

            return view('data-master.warehouse.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $warehouse = Warehouse::with(['location', 'kandang'])->findOrFail($req->id);
            $param     = [
                'title' => 'Master Data > Gudang > Ubah',
                'data'  => $warehouse,
                'type'  => Constants::WAREHOUSE_TYPE,
            ];

            if ($req->isMethod('post')) {
                $rules               = self::VALIDATION_RULES;
                $rules['kandang_id'] = $req->input('type') == 2 ? ['required'] : ['nullable'];
                $rules['name']       = ['required', 'string', 'max:50',
                    Rule::unique('warehouses')->where(function($query) {
                        return $query->whereNull('deleted_at');
                    })->ignore($warehouse->warehouse_id, 'warehouse_id'),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $warehouse->update([
                    'name'        => $req->input('name'),
                    'type'        => $req->input('type'),
                    'location_id' => $req->input('location_id'),
                    'kandang_id'  => $req->input('kandang_id'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('data-master.warehouse.index')->with($success);
            }

            return view('data-master.warehouse.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $warehouse = Warehouse::findOrFail($req->id);
            $warehouse->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('data-master.warehouse.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchWarehouse(Request $request)
    {
        $search       = $request->input('q');
        $locationIds  = $request->input('location_ids');
        $warehouseIds = $request->input('warehouse_ids');
        $warehouses   = Warehouse::with(['location', 'kandang'])->where('name', 'like', "%{$search}%");
        if ($locationIds) {
            $warehouses->whereIn('location_id', $locationIds);
        }
        if ($warehouseIds) {
            $warehouses->whereIn('warehouse_id', $warehouseIds);
        }
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q', 'location_ids', 'warehouse_ids']);
        // if (auth()->user()->role->name !== 'Super Admin') {
        foreach ($queryParams as $key => $value) {
            $warehouses->where($key, $value);
        }
        // }

        $warehouses = $warehouses->get();

        return response()->json($warehouses->map(function($warehouse) {
            return ['id' => $warehouse->warehouse_id, 'text' => $warehouse->name];
        }));
    }

    public function searchKandangWarehouse(Request $request)
    {
        $search     = $request->input('q');
        $warehouses = Warehouse::with(['location', 'kandang'])
            ->where('type', 2)
            ->where('name', 'like', "%{$search}");
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        if (auth()->user()->role->name !== 'Super Admin') {
            foreach ($queryParams as $key => $value) {
                $warehouses->where($key, $value);
            }
        }

        $warehouses = $warehouses->get();

        return response()->json($warehouses->map(function($warehouse) {
            return ['id' => $warehouse->warehouse_id, 'text' => $warehouse->name];
        }));
    }
}
