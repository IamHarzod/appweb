<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Chuyển hướng người dùng sang trang đăng nhập xác thực của Google OAuth
     */
    public function redirectToGoogle()
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with(
                'error',
                'Chức năng đăng nhập Google chưa được cấu hình GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET trong file .env. Vui lòng tham khảo tài liệu hướng dẫn cấu hình.'
            );
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            Log::error('Lỗi chuyển hướng Google OAuth: ' . $e->getMessage());
            return redirect()->route('login')->with(
                'error',
                'Không thể kết nối với dịch vụ Google: ' . $e->getMessage()
            );
        }
    }

    /**
     * Tiếp nhận và xử lý callback từ Google sau khi người dùng xác thực
     */
    public function handleGoogleCallback(Request $request)
    {
        // Kiểm tra nếu người dùng bấm Hủy hoặc Google trả về lỗi trên query param
        if ($request->has('error')) {
            return redirect()->route('login')->with(
                'error',
                'Bạn đã hủy đăng nhập bằng Google hoặc xảy ra lỗi xác thực (' . $request->get('error') . ').'
            );
        }

        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Lỗi nhận dữ liệu từ Google OAuth: ' . $e->getMessage());
            return redirect()->route('login')->with(
                'error',
                'Không thể lấy thông tin đăng nhập từ Google. Vui lòng thử lại.'
            );
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        if (empty($email)) {
            return redirect()->route('login')->with(
                'error',
                'Tài khoản Google của bạn không cung cấp địa chỉ email công khai.'
            );
        }

        // 1. Tìm kiếm người dùng qua google_id trước
        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            // 2. Nếu chưa liên kết google_id, kiểm tra xem đã có tài khoản trùng email chưa
            $user = User::where('email', $email)->first();

            if ($user) {
                // Tự động liên kết tài khoản đã tồn tại với Google
                $user->google_id = $googleId;
                if (empty($user->avatar) && $googleUser->getAvatar()) {
                    $user->avatar = $googleUser->getAvatar();
                }
                if (empty($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }
                $user->save();
            } else {
                // 3. Nếu hoàn toàn mới -> Tạo tài khoản người dùng mới
                $name = $googleUser->getName();
                if (empty($name)) {
                    $name = explode('@', $email)[0];
                }

                $user = User::create([
                    'name'              => $name,
                    'email'             => $email,
                    'google_id'         => $googleId,
                    'avatar'            => $googleUser->getAvatar(),
                    'phoneNumber'       => '',
                    'password'          => Hash::make(Str::random(32)),
                    'IsActive'          => 1,
                    'role'              => 'user',
                    'email_verified_at' => now(),
                ]);
            }
        } else {
            // Cập nhật lại avatar mới nhất từ Google nếu có
            if ($googleUser->getAvatar() && $user->avatar !== $googleUser->getAvatar()) {
                $user->avatar = $googleUser->getAvatar();
                $user->save();
            }
        }

        // Kiểm tra trạng thái tài khoản
        if (isset($user->IsActive) && !$user->IsActive) {
            return redirect()->route('login')->with(
                'error',
                'Tài khoản của bạn đã bị vô hiệu hóa hoặc tạm khóa. Vui lòng liên hệ quản trị viên.'
            );
        }

        // Đăng nhập người dùng vào hệ thống
        Auth::login($user, true);
        $request->session()->regenerate();

        session()->flash('status', 'Đăng nhập bằng Google thành công! Xin chào ' . $user->name);

        // Chuyển hướng theo phân quyền
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home'));
    }
}
