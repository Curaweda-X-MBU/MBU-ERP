<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\Permission;
use App\Models\DataMaster\Company;
use DB;

class RoleController extends Controller
{
    private const VALIDATION_RULES = [
        'company_id' => 'required'
    ];

    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama role tidak boleh kosong',
        'name.max' => 'Nama role melebihi 50 karakter',
        'name.unique' => 'Nama role telah digunakan',
        'company_id.required' => 'Unit bisnis tidak boleh kosong',
    ];

    public function index(Request $req) {
        try {
            $data = Role::with('company')->get();
            $param = [
                'title' => 'Master Data > Role',
                'data' => $data
            ];
            return view('user-management.role.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    private function buildNestedArray($array) {
        $tree = [];
        foreach ($array as $item) {
            $parts = explode('.', $item);
            $current = &$tree;

            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
        }

        return $tree;
    }

    public function add(Request $req) {
        try {
            $permissions = Permission::orderBy('permission_id')->pluck('name')->toArray();
            $nestedArray = $this->buildNestedArray($permissions);

            $param = [
                'title' => 'Master Data > Role > Tambah',
                'modul' => $nestedArray
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = [ 'required', 'string', 'max:50',
                    Rule::unique('roles')->where(function ($query) use ($req) {
                        $query->where('company_id', $req->company_id);
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
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                $userRole = Role::create([
                    'name' => $req->input('name'),
                    'company_id' => $req->input('company_id'),
                    'guard_name' => 'web',
                    'all_area' => $req->input('all_area')?true:false,
                    'all_location' => $req->input('all_location')?true:false
                ]);

                $permission = array_keys($req->input('permission')??[]);
                $userRole->givePermissionTo($permission);
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('user-management.role.index')->with($success);
            }

            return view('user-management.role.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            $role = Role::with('company')->findOrFail($req->id);
            $permissions = Permission::orderBy('permission_id')->pluck('name')->toArray();
            $nestedArray = $this->buildNestedArray($permissions);
            $param = [
                'title' => 'Master Data > Role > Ubah',
                'data' => $role,
                'modul' => $nestedArray,
                'old_modul' => $role->getAllPermissions()->pluck('name')->toArray()
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['name'] = [ 'required', 'string', 'max:50',
                    Rule::unique('roles')->where(function ($query) use ($req) {
                        $query->where('company_id', $req->company_id);
                        $query->whereNull('deleted_at');
                        return $query;
                    })->ignore($role->role_id, 'role_id')
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $permission = array_keys($req->input('permission')??[]);
                $role->revokePermissionTo($param['old_modul']);
                $role->givePermissionTo($permission);
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                $role->update([
                    'name' => $req->input('name'),
                    'company_id' => $req->input('company_id'),
                    'guard_name' => 'web',
                    'all_area' => $req->input('all_area')?true:false,
                    'all_location' => $req->input('all_location')?true:false
                ]);

                $success = ['success' => 'Data berhasil dirubah'];
                return redirect()->route('user-management.role.index')->with($success);
            }

            return view('user-management.role.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $role = Role::findOrFail($req->id);
            $oldPermissions = $role->getAllPermissions()->pluck('name')->toArray();
            $role->revokePermissionTo($oldPermissions);
            $role->delete();

            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('user-management.role.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchRole(Request $request) {
        $search = $request->input('q');
        $query = Role::where('name', 'like', "%{$search}%");

        if($request->has('company_id')) {
            $companyId = $request->query('company_id');
            $query->where('company_id', $companyId);
        }

        $data = $query->get();

        return response()->json($data->map(function ($val) {
            return ['id' => $val->role_id, 'text' => $val->name];
        }));
    }
}
