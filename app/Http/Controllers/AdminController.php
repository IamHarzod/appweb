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
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total_amount') ?? 0;
        $totalOrders = \App\Models\Order::count();
        $totalProducts = \App\Models\Product::count();
        $totalUsers = User::where('role', 'user')->count();
        $recentOrders = \App\Models\Order::with('user')->orderBy('id', 'desc')->limit(5)->get();

        return view("admin.dashboard", compact('totalRevenue', 'totalOrders', 'totalProducts', 'totalUsers', 'recentOrders'));
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
            } elseif (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')->with('warning', 'Tài khoản của bạn chưa được xác thực email. Vui lòng kiểm tra email để kích hoạt tài khoản.');
            } else {
                return redirect()->intended(route('home'));
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

        if (Auth::id() == $id && $request->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không thể tự giáng quyền của chính mình!');
        }

        $adminCount = User::where('role', 'admin')->count();
        if ($user->role === 'admin' && $request->role !== 'admin' && $adminCount <= 1) {
            return redirect()->back()->with('error', 'Hệ thống phải có ít nhất 1 tài khoản Quản trị viên (Admin)!');
        }

        $user->role = $request->role;
        $user->save();
        return redirect()->route('admin.users')->with('success', 'Cập nhật quyền thành công');
    }

    public function destroy_user($id)
    {
        try {
            if (Auth::id() == $id) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Không thể xóa tài khoản của chính bạn!'], 400);
                }
                return redirect()->back()->with('error', 'Không thể xóa tài khoản của chính bạn!');
            }

            $user = User::findOrFail($id);
            $user->delete();

            if (request()->ajax()) {
                return true;
            }
            return redirect()->route('admin.users')->with('success', 'Xóa người dùng thành công!');
        } catch (\Throwable $e) {
            if (request()->ajax()) {
                return false;
            }
            return redirect()->back()->with('error', 'Lỗi khi xóa người dùng: ' . $e->getMessage());
        }
    }

    public function submit_register(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'phoneNumber' => [
                'required',
                'regex:/^(0|\+84)(3|5|7|8|9)\d{8}$/'
            ],
            'password'    => ['required', 'string', 'min:6'],
        ], [
            'name.required'        => 'Vui lòng nhập họ tên.',
            'email.required'       => 'Vui lòng nhập email.',
            'email.unique'         => 'Địa chỉ email này đã được đăng ký trong hệ thống. Vui lòng đăng nhập để tiếp tục hoặc xác thực tài khoản.',
            'phoneNumber.required' => 'Vui lòng nhập số điện thoại.',
            'phoneNumber.regex'    => 'Số điện thoại không đúng định dạng.',
            'password.required'    => 'Vui lòng nhập mật khẩu.',
            'password.min'         => 'Mật khẩu phải từ 6 ký tự trở lên.',
        ]);

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'phoneNumber' => $request->phoneNumber,
            'password'    => bcrypt($request->password),
            'IsActive'    => 1,
            'role'        => 'user',
        ]);

        Auth::login($user);

        // Sinh mã OTP 6 số và lưu vào Cache 10 phút
        $code = (string) random_int(100000, 999999);
        \Illuminate\Support\Facades\Cache::put("otp_user_{$user->id}", [
            'code'       => $code,
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts'   => 0,
        ], now()->addMinutes(10));

        // Gửi qua Email API (Port 443 HTTPS - không bao giờ bị chặn bởi tường lửa)
        $emailService = app(\App\Services\EmailApiService::class);
        $result = $emailService->sendOtpEmail($user->email, $user->name, $code);

        if ($result['success']) {
            return redirect()->route('verification.notice')
                ->with('success', 'Đăng ký tài khoản thành công! Mã OTP xác thực đã được gửi tới email của bạn qua kết nối bảo mật HTTPS.');
        }

        return redirect()->route('verification.notice')
            ->with('warning', 'Đăng ký tài khoản thành công! Vui lòng nhập mã xác thực OTP để hoàn tất kích hoạt tài khoản.');
    }

    public function logout_admin(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Đã đăng xuất thành công.');
    }
}
