<?php

namespace App\Http\Controllers\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\DataMaster\Company;
use App\Models\DataMaster\Department;
use App\Models\DataMaster\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    private const VALIDATION_RULES = [
        'company_id'  => 'required',
        'location_id' => 'required',
    ];

    private const VALIDATION_MESSAGES = [
        'name.required'        => 'Nama departemen tidak boleh kosong',
        'name.max'             => 'Nama departemen melebihi 50 karakter',
        'name.unique'          => 'Nama departemen telah digunakan',
        'company_id.required'  => 'Unit bisnis tidak boleh kosong',
        'location_id.required' => 'Lokasi tidak boleh kosong',
    ];

    public function index(Request $req)
    {
        try {
            $data = Department::with([
                'company',
                'location',
            ])->get();
            $param = [
                'title' => 'Master Data > Departemen',
                'data'  => $data,
            ];

            return view('data-master.department.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function add(Request $req)
    {
        try {
            $param = [
                'title' => 'Master Data > Departemen > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('departments')->where(function($query) use ($req) {
                        $query->where([
                            'company_id'  => $req->company_id,
                            'location_id' => $req->location_id,
                        ]);
                        $query->whereNull('deleted_at');

                        return $query;
                    }),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->all();
                    if (isset($input['company_id'])) {
                        $input['company_name'] = Company::find($req->input('company_id'))->name;
                    }
                    if (isset($input['location_id'])) {
                        $input['location_name'] = Location::find($req->input('location_id'))->name;
                    }

                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                Department::create([
                    'name'        => $req->input('name'),
                    'company_id'  => $req->input('company_id'),
                    'location_id' => $req->input('location_id'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('data-master.department.index')->with($success);
            }

            return view('data-master.department.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $department = Department::with([
                'company',
                'location',
            ])->findOrFail($req->id);
            $param = [
                'title' => 'Master Data > Departemen > Ubah',
                'data'  => $department,
            ];

            if ($req->isMethod('post')) {
                $rules         = self::VALIDATION_RULES;
                $rules['name'] = ['required', 'string', 'max:50',
                    Rule::unique('departments')->where(function($query) use ($req) {
                        $query->where([
                            'company_id'  => $req->company_id,
                            'location_id' => $req->location_id,
                        ]);
                        $query->whereNull('deleted_at');

                        return $query;
                    })->ignore($department->department_id, 'department_id'),
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $department->update([
                    'name'        => $req->input('name'),
                    'company_id'  => $req->input('company_id'),
                    'location_id' => $req->input('location_id'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('data-master.department.index')->with($success);
            }

            return view('data-master.department.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $department = Department::findOrFail($req->id);
            $department->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('data-master.department.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchDepartment(Request $request)
    {
        $search = $request->input('q');
        $query  = Department::with('location')->where('name', 'like', "%{$search}%");

        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            $query->where($key, $value);
        }

        $data = $query->get();

        return response()->json($data->map(function($val) {
            return ['id' => $val->department_id, 'text' => $val->name];
        }));
    }
}
