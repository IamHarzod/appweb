<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function login()
    {
        return view("admin.auth.login_admin");
    }
    public function register_admin(Request $request)
    {
        return view("admin.auth.register_admin");
    }

    public function show_dasboard()
    {
        return view("layout.admin_layout");
    }

    public function users()
    {
        $users = User::orderBy('id','desc')->get(['id','name','email','phoneNumber','role','IsActive','created_at']);
        return view('admin.auth.users', compact('users'));
    }

    public function submit_login(Request $request)
    {
        // Validate đơn giản
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // bảo mật session
            // Cả admin và user đều được chuyển về trang home
            return redirect()->route('home');
        }

        // Sai thông tin → trả về lỗi chung
        return back()
            ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])
            ->onlyInput('email');
    }

    public function update_user_role(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
        return redirect()->route('admin.users')->with('success', 'Cập nhật quyền thành công');
    }

    public function submit_register(Request $request)
    {
        // Validate đơn giản
        $credentials = $request->validate([
            'name'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'phone'    => [
                'required',
                'regex:/^(0|\+84)(3|5|7|8|9)\d{8}$/'
                // ví dụ chấp nhận: 0981234567, +84981234567
            ],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $arrayData = ['name' => $request->name, 'email' => $request->email, 'phone' => $request->phone, 'password' => bcrypt($request->password)];
        User::create($arrayData);
        return redirect()->intended(route('admin'));
    }

    public function logout_admin()
    {
        Auth::logout();
        return redirect()->route('admin');
    }
}
