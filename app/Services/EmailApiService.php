<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmailApiService
{
    /**
     * Gửi email chứa mã OTP qua HTTP API (Port 443) thay vì cổng SMTP truyền thống.
     * Hỗ trợ tự động cả Resend và Brevo (Sendinblue).
     *
     * @param string $toEmail   Địa chỉ email người nhận
     * @param string $toName    Tên người nhận
     * @param string $otpCode   Mã OTP gồm 6 chữ số
     * @return array ['success' => bool, 'message' => string, 'provider' => string|null]
     */
    public function sendOtpEmail(string $toEmail, string $toName, string $otpCode): array
    {
        $subject = "[36Shop] Mã OTP xác thực tài khoản của bạn: {$otpCode}";
        $htmlContent = $this->renderOtpEmailTemplate($toName, $otpCode);

        // 1. Kiểm tra Resend API Key (re_...)
        $resendKey = env('RESEND_API_KEY');
        if (!empty($resendKey)) {
            return $this->sendViaResend($resendKey, $toEmail, $subject, $htmlContent);
        }

        // 2. Kiểm tra Brevo API Key (xkeysib-...)
        $brevoKey = env('BREVO_API_KEY');
        if (!empty($brevoKey)) {
            return $this->sendViaBrevo($brevoKey, $toEmail, $toName, $subject, $htmlContent);
        }

        // 3. Nếu chưa cấu hình API key, fallback cảnh báo và ghi log
        Log::warning("Chưa cấu hình RESEND_API_KEY hoặc BREVO_API_KEY. Không thể gửi email OTP qua HTTPS API tới {$toEmail}.");
        return [
            'success' => false,
            'message' => 'Hệ thống chưa cấu hình RESEND_API_KEY hoặc BREVO_API_KEY trên môi trường.',
            'provider' => null,
        ];
    }

    /**
     * Gửi qua Resend API (HTTPS Port 443)
     */
    protected function sendViaResend(string $apiKey, string $toEmail, string $subject, string $html): array
    {
        try {
            $from = env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev');
            $fromName = env('MAIL_FROM_NAME', '36Shop');

            $response = Http::timeout(10)
                ->withToken($apiKey)
                ->post('https://api.resend.com/emails', [
                    'from'    => "{$fromName} <{$from}>",
                    'to'      => [$toEmail],
                    'subject' => $subject,
                    'html'    => $html,
                ]);

            if ($response->successful()) {
                Log::info("Đã gửi email OTP thành công qua Resend API tới {$toEmail}. Response ID: " . ($response->json('id') ?? 'OK'));
                return ['success' => true, 'message' => 'Mã OTP đã được gửi thành công qua Resend.', 'provider' => 'Resend'];
            }

            Log::error("Lỗi khi gửi email qua Resend API: " . $response->body());
            return ['success' => false, 'message' => 'Lỗi từ Resend API: ' . ($response->json('message') ?? $response->body()), 'provider' => 'Resend'];
        } catch (\Throwable $e) {
            Log::error("Ngoại lệ khi gọi Resend API: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'provider' => 'Resend'];
        }
    }

    /**
     * Gửi qua Brevo API (HTTPS Port 443)
     */
    protected function sendViaBrevo(string $apiKey, string $toEmail, string $toName, string $subject, string $html): array
    {
        try {
            $fromEmail = env('MAIL_FROM_ADDRESS', 'no-reply@36shop.com');
            $fromName = env('MAIL_FROM_NAME', '36Shop');

            $response = Http::timeout(10)
                ->withHeaders([
                    'api-key'      => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender'      => ['name' => $fromName, 'email' => $fromEmail],
                    'to'          => [['name' => $toName, 'email' => $toEmail]],
                    'subject'     => $subject,
                    'htmlContent' => $html,
                ]);

            if ($response->successful()) {
                Log::info("Đã gửi email OTP thành công qua Brevo API tới {$toEmail}.");
                return ['success' => true, 'message' => 'Mã OTP đã được gửi thành công qua Brevo.', 'provider' => 'Brevo'];
            }

            Log::error("Lỗi khi gửi email qua Brevo API: " . $response->body());
            return ['success' => false, 'message' => 'Lỗi từ Brevo API: ' . ($response->json('message') ?? $response->body()), 'provider' => 'Brevo'];
        } catch (\Throwable $e) {
            Log::error("Ngoại lệ khi gọi Brevo API: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'provider' => 'Brevo'];
        }
    }

    /**
     * Mẫu giao diện Email HTML gửi OTP
     */
    protected function renderOtpEmailTemplate(string $name, string $otp): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực mã OTP</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
        <tr>
            <td align="center" style="padding: 40px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 540px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 30px 20px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: #ffffff;">
                            <h1 style="margin: 0; font-size: 26px; font-weight: 700; letter-spacing: 1px;">36SHOP</h1>
                            <p style="margin: 6px 0 0 0; font-size: 14px; opacity: 0.9;">Hệ thống Thương Mại Điện Tử An Toàn</p>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 35px 30px;">
                            <h2 style="margin: 0 0 15px 0; font-size: 20px; color: #1a1a1a;">Xin chào {$name},</h2>
                            <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 1.6; color: #555555;">
                                Cảm ơn bạn đã tham gia cộng đồng mua sắm tại <strong>36Shop</strong>. Dưới đây là mã xác thực OTP dùng để kích hoạt và bảo mật tài khoản của bạn:
                            </p>
                            
                            <!-- OTP Box -->
                            <div style="text-align: center; margin: 30px 0; padding: 20px; background-color: #f8f9fa; border: 2px dashed #0d6efd; border-radius: 10px;">
                                <div style="font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #6c757d; margin-bottom: 8px;">Mã xác thực của bạn</div>
                                <div style="font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #0d6efd;">{$otp}</div>
                                <div style="font-size: 13px; color: #dc3545; margin-top: 8px;">⏱️ Có hiệu lực trong vòng <strong>10 phút</strong></div>
                            </div>

                            <p style="margin: 0 0 10px 0; font-size: 14px; line-height: 1.5; color: #666;">
                                Vui lòng nhập mã gồm 6 chữ số này vào trang xác thực trên website để mở khóa toàn bộ quyền mua sắm.
                            </p>
                            <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #888;">
                                ⚠️ <em>Lưu ý bảo mật: Tuyệt đối không chia sẻ mã này cho bất kỳ ai, kể cả nhân viên hỗ trợ của 36Shop.</em>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #f8f9fa; border-top: 1px solid #eeeeee; font-size: 12px; color: #999999;">
                            © 2026 36Shop. Mọi quyền được bảo lưu.<br>
                            Đây là email tự động, vui lòng không trả lời thư này.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
