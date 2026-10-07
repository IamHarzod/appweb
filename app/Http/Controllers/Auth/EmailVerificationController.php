<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    /**
     * Hiển thị giao diện thông báo yêu cầu xác thực email.
     */
    public function notice(Request $request)
    {
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        return view('auth.verify_email');
    }

    /**
     * Xử lý liên kết xác thực email người dùng bấm từ hòm thư.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('Liên kết xác thực email không hợp lệ hoặc đã hết hạn.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')->with('success', 'Email của bạn đã được xác thực trước đó.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Tự động đăng nhập nếu người dùng mở link trên trình duyệt mới
        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect()->route('home')->with('success', 'Xác thực email thành công! Chào mừng bạn đến với 36Shop.');
    }

    /**
     * Gửi lại email xác thực cho người dùng.
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để gửi lại email xác thực.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        try {
            $user->sendEmailVerificationNotification();
            return back()->with('status', 'verification-link-sent');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi khi gửi lại email xác thực: ' . $e->getMessage());

            $fallbackUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
            );

            return back()->with('error', 'Không thể kết nối tới máy chủ SMTP (Do Render gói Free chặn cổng gửi mail 587/465). Bạn có thể kích hoạt trực tiếp bằng nút bên dưới.')
                ->with('fallback_verify_url', $fallbackUrl);
        }
    }
}
