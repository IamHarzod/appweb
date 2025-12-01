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
        $users = User::orderBy('id', 'desc')->get(['id', 'name', 'email', 'phoneNumber', 'role', 'IsActive', 'created_at']);
        return view('admin.auth.users', compact('users'));
    }

    public function submit_login(Request $request)
    {
        // Validate với thông báo lỗi chi tiết
        $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.max' => 'Địa chỉ email không được vượt quá 255 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate(); // bảo mật session

            // Kiểm tra role để redirect phù hợp
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('home');
            }
        }

        // Sai thông tin → trả về lỗi cụ thể
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

        $credentials = $request->validate([
            'name'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'phoneNumber'    => [
                'required',
                'regex:/^(0|\+84)(3|5|7|8|9)\d{8}$/'
            ],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $arrayData = ['name' => $request->name, 'email' => $request->email, 'phoneNumber' => $request->phoneNumber, 'password' => bcrypt($request->password)];
        User::create($arrayData);
        return redirect()->intended(route('admin'));
    }

    public function logout_admin()
    {
        Auth::logout();
        return redirect()->route('admin');
    }
}
