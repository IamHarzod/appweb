<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailApiService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    protected EmailApiService $emailService;

    public function __construct(EmailApiService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Hiển thị giao diện nhập mã OTP xác thực tài khoản.
     */
    public function notice(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thực hiện xác thực tài khoản.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')->with('success', 'Tài khoản của bạn đã được xác thực trước đó.');
        }

        $cacheKey = "otp_user_{$user->id}";
        $otpData = Cache::get($cacheKey);

        // Nếu chưa có OTP hoặc đã hết hạn, tự động sinh mã mới và gửi qua Email API
        if (!$otpData) {
            $code = (string) random_int(100000, 999999);
            $expiresAt = now()->addMinutes(10)->timestamp;

            $otpData = [
                'code'       => $code,
                'expires_at' => $expiresAt,
                'attempts'   => 0,
            ];
            Cache::put($cacheKey, $otpData, now()->addMinutes(10));

            // Gửi qua Email API (HTTPS Port 443)
            $sendResult = $this->emailService->sendOtpEmail($user->email, $user->name, $code);
            if (!$sendResult['success']) {
                session()->flash('otp_api_notice', $sendResult['message']);
            }
        }

        // Tính số giây còn hiệu lực
        $secondsRemaining = max(0, $otpData['expires_at'] - time());

        // Kiểm tra cooldown gửi lại mã
        $cooldownKey = "otp_cooldown_{$user->id}";
        $cooldownSeconds = max(0, (int) (Cache::get($cooldownKey, 0) - time()));

        return view('auth.verify_otp', [
            'user'             => $user,
            'secondsRemaining' => $secondsRemaining,
            'cooldownSeconds'  => $cooldownSeconds,
            // Cung cấp mã OTP trực tiếp trong môi trường local/debug hoặc khi chưa cấu hình Email API để tránh tắc nghẽn
            'debugOtp'         => (config('app.debug') || empty(env('RESEND_API_KEY')) && empty(env('BREVO_API_KEY'))) ? $otpData['code'] : null,
        ]);
    }

    /**
     * Xác thực mã OTP người dùng nhập vào form.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP gồm 6 chữ số.',
            'otp.size'     => 'Mã OTP phải chính xác 6 chữ số.',
            'otp.regex'    => 'Mã OTP chỉ bao gồm các chữ số (0-9).',
        ]);

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')->with('success', 'Tài khoản của bạn đã được xác thực trước đó.');
        }

        $cacheKey = "otp_user_{$user->id}";
        $otpData = Cache::get($cacheKey);

        if (!$otpData || time() > $otpData['expires_at']) {
            Cache::forget($cacheKey);
            return back()->with('error', 'Mã OTP đã hết hiệu lực (quá 10 phút). Vui lòng nhấn "Gửi lại mã OTP" bên dưới.');
        }

        // Kiểm tra chống dò mã (Brute-force protection: tối đa 5 lần thử sai)
        if ($otpData['attempts'] >= 5) {
            Cache::forget($cacheKey);
            return back()->with('error', 'Bạn đã nhập sai mã OTP quá 5 lần liên tiếp. Vì lý do bảo mật, mã này đã bị vô hiệu hóa. Vui lòng yêu cầu mã mới.');
        }

        // Kiểm tra mã OTP an toàn với hàm so sánh chuỗi thời gian cố định
        if (!hash_equals((string) $otpData['code'], (string) $request->otp)) {
            $otpData['attempts']++;
            $remainingAttempts = 5 - $otpData['attempts'];
            $ttlSeconds = max(1, $otpData['expires_at'] - time());
            Cache::put($cacheKey, $otpData, $ttlSeconds);

            if ($remainingAttempts <= 0) {
                Cache::forget($cacheKey);
                return back()->with('error', 'Bạn đã nhập sai mã OTP quá 5 lần. Mã đã bị hủy, vui lòng yêu cầu mã mới.');
            }

            return back()->with('error', "Mã OTP không chính xác. Bạn còn {$remainingAttempts} lần thử.");
        }

        // Mã OTP chính xác -> Đánh dấu tài khoản đã xác thực
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Xóa mã OTP và cooldown sau khi hoàn tất
        Cache::forget($cacheKey);
        Cache::forget("otp_cooldown_{$user->id}");

        return redirect()->route('home')->with('success', 'Xác thực tài khoản bằng OTP thành công! Chào mừng bạn đến với 36Shop.');
    }

    /**
     * Yêu cầu gửi lại mã OTP mới.
     */
    public function resendOtp(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để yêu cầu mã OTP.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')->with('success', 'Tài khoản của bạn đã được xác thực trước đó.');
        }

        // Kiểm tra cooldown chống spam (chờ 60 giây giữa các lần yêu cầu)
        $cooldownKey = "otp_cooldown_{$user->id}";
        $existingCooldown = Cache::get($cooldownKey);
        if ($existingCooldown && time() < $existingCooldown) {
            $secondsLeft = $existingCooldown - time();
            return back()->with('error', "Vui lòng đợi {$secondsLeft} giây trước khi yêu cầu gửi lại mã OTP mới.");
        }

        // Sinh mã OTP 6 số mới
        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10)->timestamp;

        $cacheKey = "otp_user_{$user->id}";
        Cache::put($cacheKey, [
            'code'       => $code,
            'expires_at' => $expiresAt,
            'attempts'   => 0,
        ], now()->addMinutes(10));

        // Thiết lập cooldown 60 giây
        Cache::put($cooldownKey, time() + 60, 60);

        // Gửi qua Email API (HTTPS Port 443)
        $sendResult = $this->emailService->sendOtpEmail($user->email, $user->name, $code);

        if ($sendResult['success']) {
            return back()->with('success', "Mã OTP mới đã được gửi tới {$user->email} qua kênh bảo mật HTTPS.");
        }

        return back()->with('warning', 'Đã tạo mã OTP mới. ' . ($sendResult['message'] ?? ''));
    }

    /**
     * Fallback: Xử lý liên kết xác thực cũ nếu người dùng bấm từ email trước đây.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('Liên kết xác thực không hợp lệ hoặc đã hết hạn.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')->with('success', 'Email của bạn đã được xác thực trước đó.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect()->route('home')->with('success', 'Xác thực tài khoản thành công! Chào mừng bạn đến với 36Shop.');
    }
}
