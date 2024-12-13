<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Helpers\FileHelper;
use App\Models\UserManagement\User;
use App\Models\UserManagement\Role;
use App\Models\DataMaster\Department;
use App\Models\DataMaster\Location;
use App\Models\DataMaster\Company;
use Illuminate\Support\Arr;
use DB, Exception;

class UsersController extends Controller
{
    private const VALIDATION_RULES = [
        'name' => 'required|max:50',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'phone' => [
            'required',
            'regex:/^([0-9\s\-\+\(\)]*)$/',
            'min:10',
            'max:15',
        ],
        'password' => 'required|min:8|confirmed',
        'company_id' => 'required',
        'department_id' => 'required',
        'role_id' => 'required'
    ];

    private const VALIDATION_MESSAGES = [
        'image.image' => 'Foto tidak valid',
        'image.mimes' => 'Foto tidak valid',
        'image.max' => 'Foto melebihi kapasitas 2 MB',
        'name.required' => 'Nama user tidak boleh kosong',
        'name.max' => 'Nama user melebihi 50 karakter',
        'npk.required' => 'NPK tidak boleh kosong',
        'npk.max' => 'NPK melebihi 10 karakter',
        'npk.unique' => 'NPK telah digunakan',
        'email.required' => 'Email tidak boleh kosong',
        'email.email' => 'Alamat email tidak sesuai standar',
        'email.max' => 'Alamat email melebihi 50 karakter',
        'email.unique' => 'Alamat email telah digunakan',
        'phone.required' => 'No telepon tidak boleh kosong',
        'phone.regex' => 'No telepon tidak sesuai standar',
        'phone.min' => 'No telepon kurang dari 10 karakter',
        'phone.max' => 'No telepon lebih dari 50 karakter',
        'password.required' => 'Password tidak boleh kosong',
        'password.min' => 'Password kurang dari 8 karakter',
        'password.confirmed' => 'Password tidak sama',
        'company_id.required' => 'Unit bisnis tidak boleh kosong',
        'department_id.required' => 'Departement tidak boleh kosong',
        'role_id.required' => 'Role tidak boleh kosong',
    ];

    public function index(Request $req) {
        try {
            $data = User::getData(true, false);
            $param = [
                'title' => 'Managemen User > User',
                'data' => $data
            ];

            return view('user-management.user.index', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage()
            ])->withInput();
        }
    }

    public function add(Request $req) {
        try {
            $param = [
                'title' => 'Managemen User > User > Tambah',
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $rules['npk'] = [ 'required', 'string', 'max:10',
                    Rule::unique('users')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })
                ];
                $rules['email'] = [ 'required', 'email', 'max:50',
                    Rule::unique('users')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
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
                    if(isset($input['department_id'])) {
                        $input['department_name'] = Department::find($req->input('department_id'))->name;
                    }
                    if(isset($input['role_id'])) {
                        $input['role_name'] = Role::find($req->input('role_id'))->name;
                    }
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                if ($req->has('image')) {
                    $imageUrl = FileHelper::upload($req->file('image'));
                    if (!$imageUrl['status']) {
                        return redirect()->back()->with('error', $imageUrl['message'])->withInput();
                    }
                    $req->input()['image'] = $imageUrl['url'];
                }

                $user = User::create([
                    'npk' => $req->input('npk'),
                    'name' => $req->input('name'),
                    'email' => $req->input('email'),
                    'phone' => $req->input('phone'),
                    'image' => $req->input('image'),
                    'password' => Hash::make($req->input('password')),
                    'department_id' => $req->input('department_id'),
                    'role_id' => $req->input('role_id'),
                    'is_active' => $req->input('is_active')?true:false,
                ]);

                $role = Role::find($req->input('role_id'));
                $user->assignRole($role->name);

                $success = ['success' => 'Data Berhasil disimpan'];
                return redirect()->route('user-management.user.index')->with($success);
            }

            return view('user-management.user.add', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $req) {
        try {
            if($req->id != auth()->user()->user_id && auth()->user()->role->name !== 'Super Admin') {
                return abort(403);
            }
            $user = User::getData(false, false, ['user_id' => $req->id]);
            if(!$user) {
                return redirect()->back()->with('error', 'Data tidak ditemukan')->withInput();
            }
            $param = [
                'title' => 'Managemen User > User > Ubah',
                'data' => $user
            ];

            if ($req->isMethod('post')) {
                $rules = self::VALIDATION_RULES;
                $dataUpdate = [
                    'npk' => $req->input('npk'),
                    'name' => $req->input('name'),
                    'email' => $req->input('email'),
                    'phone' => $req->input('phone'),
                    'password' => Hash::make($req->input('password')),
                    'department_id' => $req->input('department_id'),
                    'role_id' => $req->input('role_id'),
                    'is_active' => $req->input('is_active')?true:false,
                ];
                if(!$req->input('password')) {
                    $rules['password'] = 'nullable';
                    unset($dataUpdate['password']);
                }

                $rules['npk'] = [ 'required', 'string', 'max:10',
                    Rule::unique('users')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })->ignore($user->user_id, 'user_id')
                ];
                $rules['email'] = [ 'required', 'email', 'max:50',
                    Rule::unique('users')->where(function ($query) use ($req) {
                        return $query->whereNull('deleted_at');
                    })->ignore($user->user_id, 'user_id')
                ];

                $validator = Validator::make($req->all(), $rules, self::VALIDATION_MESSAGES);
                if ($validator->fails()) {
                    $input = $req->all();
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput($input);
                }

                if ($req->has('image')) {
                    if ($user->image) {
                        $delete = FileHelper::delete($user->image);
                    }
                    $imageUrl = FileHelper::upload($req->file('image'));
                    if (!$imageUrl['status']) {
                        return redirect()->back()->with('error', $imageUrl['message'])->withInput();
                    }
                    $dataUpdate['image'] = $imageUrl['url'];
                }

                if ($user->hasRole($user->role->name??'')) {
                    $user->removeRole($user->role->name);
                }
                
                $newRole = Role::find($dataUpdate['role_id']);
                $user->assignRole($newRole->name);

                $user->update($dataUpdate);

                $success = ['success' => 'Data Berhasil dirubah'];

                if (auth()->user()->role->hasPermissionTo('user-management.user.index')) {
                    return redirect()->route('user-management.user.index')->with($success);
                } else {
                    return redirect()->back()->with($success);
                }
            }

            return view('user-management.user.edit', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function delete(Request $req) {
        try {
            $user = User::with('role')->findOrFail($req->id);
            if ($user->hasRole($user->role->name??'')) {
                $user->removeRole($user->role->name);
            }
            $user->delete();

            $success = ['success' => 'Data berhasil dihapus'];
            return redirect()->route('user-management.user.index')->with($success);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function searchUser(Request $request) {
        $search = $request->input('q');
        $users = User::with(['department', 'role'])->where('name', 'like', "%{$search}%");
        $queryParams = $request->query();
        $queryParams = Arr::except($queryParams, ['q']);
        foreach ($queryParams as $key => $value) {
            switch ($key) {
                case 'company_id':
                    $users->whereHas('department', function ($query) use ($value) {
                        $query->with('company');
                        $query->whereHas('company', function ($query) use ($value) {
                            $query->where('company_id', $value);
                        });
                    });
                    break;
                case 'role_name':
                    $users->whereHas('role', function ($query) use ($value) {
                        $query->where('name', $value);
                    });
                    break;
                case 'area_id':
                    $users->whereHas('department', function ($query) use ($value) {
                        $query->with('location');
                        $query->whereHas('location', function ($query) use ($value) {
                            $query->where('area_id', $value);
                        });
                    });
                    break;
                default: 
                    $users->where($key, $value);
                    break;
            }
        }

        $users = $users->get();

        return response()->json($users->map(function ($user) {
            return ['id' => $user->user_id, 'text' => $user->name];
        }));
    }
}
