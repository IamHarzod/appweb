<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * Chặn toàn bộ người dùng đã đăng nhập nhưng chưa xác thực email (email_verified_at is null),
     * không cho phép truy cập bất kỳ trang dịch vụ nào (trang chủ, sản phẩm, giỏ hàng, thanh toán, API...).
     * Chỉ cho phép truy cập: màn hình xác thực (/email/verify), gửi lại email, bấm link xác thực, hoặc đăng xuất.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->hasVerifiedEmail() && $user->role !== 'admin') {
            // Danh sách các route được phép truy cập khi chưa xác thực email
            $allowedRoutes = [
                'verification.notice',
                'verification.send',
                'verification.verify',
                'verification.verify_otp',
                'verification.resend_otp',
                'logout',
            ];

            if ($request->routeIs($allowedRoutes)) {
                return $next($request);
            }

            // Nếu là gọi API hoặc AJAX (thêm giỏ hàng, thao tác dev tool, fetch)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tài khoản của bạn chưa được xác thực email. Vui lòng xác thực tài khoản qua email để tiếp tục sử dụng dịch vụ.',
                ], 403);
            }

            // Nếu là truy cập web thông thường (bấm logo, đổi url, vào trang chủ, mua hàng...)
            return redirect()->route('verification.notice')->with('warning', 'Tài khoản của bạn chưa được xác thực email. Vui lòng xác thực email để tiếp tục sử dụng các dịch vụ của 36Shop.');
        }

        return $next($request);
    }
}
