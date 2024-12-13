<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    private const VALIDATION_MESSAGES = [
        'name.required' => 'Nama tidak boleh kosong',
        'name.unique'   => 'Nama telah digunakan',
    ];

    public function index(Request $req)
    {
        try {

            $param = [
                'title' => 'User Management > Permission',
                'data'  => Permission::get(),
            ];

            return view('user-management.permission.index', $param);
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
                'title' => 'User Management > Permission > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = ['name' => ['required', 'string', 'unique:permissions,name']];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                Permission::create([
                    'name'       => $req->input('name'),
                    'guard_name' => 'web',
                    'created_at' => date('Y-m-d'),
                ]);

                $success = ['success' => 'Data Berhasil disimpan'];

                return redirect()->route('user-management.permission.index')->with($success);
            }

            return view('user-management.permission.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req)
    {
        try {
            $permission = Permission::findOrFail($req->id);
            $param      = [
                'title' => 'User Management > Permission > Ubah',
                'data'  => $permission,
            ];

            if ($req->isMethod('post')) {
                $rules     = ['name' => ['required', 'string', 'max:100', Rule::unique('permissions')->ignore($req->id, 'permission_id')]];
                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $permission->update([
                    'name'       => $req->input('name'),
                    'updated_at' => date('Y-m-d'),
                ]);

                $success = ['success' => 'Data berhasil dirubah'];

                return redirect()->route('user-management.permission.index')->with($success);
            }

            return view('user-management.permission.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req)
    {
        try {
            $permission = Permission::findOrFail($req->id);
            $permission->delete();

            $success = ['success' => 'Data berhasil dihapus'];

            return redirect()->route('user-management.permission.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchPermission(Request $request)
    {
        $search      = $request->input('q');
        $permissions = Permission::where('name', 'like', "%{$search}%")->get();

        return response()->json($permissions->map(function($permission) {
            return ['id' => $area->area_id, 'text' => $area->name];
        }));
    }
}
