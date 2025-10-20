<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Hiển thị form quên mật khẩu
     */
    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot_password');
    }

    /**
     * Xử lý yêu cầu quên mật khẩu
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.exists' => 'Địa chỉ email này không tồn tại trong hệ thống.'
        ]);

        $email = $request->email;
        
        // Tạo token reset
        $token = Str::random(64);
        
        // Lưu token vào database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Gửi email reset (tạm thời chỉ log, cần cấu hình mail sau)
        try {
            // TODO: Cấu hình mail service để gửi email thực tế
            \Log::info("Password reset link for {$email}: " . url("/password/reset/{$token}"));
            
            return back()->with('success', 'Liên kết khôi phục mật khẩu đã được gửi đến email của bạn.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Có lỗi xảy ra khi gửi email. Vui lòng thử lại sau.']);
        }
    }

    /**
     * Hiển thị form reset mật khẩu
     */
    public function showResetForm($token)
    {
        return view('admin.auth.reset_password', compact('token'));
    }

    /**
     * Xử lý reset mật khẩu
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.exists' => 'Địa chỉ email này không tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $email = $request->email;
        $token = $request->token;
        $password = $request->password;

        // Kiểm tra token có hợp lệ không
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord || !Hash::check($token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Token không hợp lệ hoặc đã hết hạn.']);
        }

        // Kiểm tra token có hết hạn không (24 giờ)
        if (Carbon::parse($resetRecord->created_at)->addHours(24)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return back()->withErrors(['email' => 'Token đã hết hạn. Vui lòng yêu cầu reset mật khẩu mới.']);
        }

        // Cập nhật mật khẩu
        User::where('email', $email)->update([
            'password' => Hash::make($password)
        ]);

        // Xóa token đã sử dụng
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('admin')->with('success', 'Mật khẩu đã được cập nhật thành công. Vui lòng đăng nhập với mật khẩu mới.');
    }
}