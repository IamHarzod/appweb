<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GHNWebhookController extends Controller
{
    /**
     * Nhận và xử lý Webhook tự động từ GHN (Giao Hàng Nhanh).
     * Endpoint: POST /ghn/webhook hoặc POST /api/ghn/webhook
     */
    public function handle(Request $request)
    {
        $webhookSecret = (string) config('services.ghn.webhook_secret');
        if ($webhookSecret === '' || !hash_equals($webhookSecret, (string) $request->header('X-GHN-Webhook-Secret'))) {
            return response()->json(['code' => 401, 'message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        Log::info('GHN Webhook received:', $payload);

        // Lấy mã vận đơn GHN & mã đơn của Shop (nếu có)
        $orderCode = $request->input('OrderCode') ?? $request->input('order_code');
        $clientOrderCode = $request->input('ClientOrderCode') ?? $request->input('client_order_code');
        $ghnStatus = strtolower(trim((string)($request->input('Status') ?? $request->input('status') ?? '')));
        $codAmount = (float)($request->input('CODAmount') ?? $request->input('cod_amount') ?? 0);
        $description = $request->input('Description') ?? $request->input('description') ?? '';

        if (!$orderCode && !$clientOrderCode) {
            return response()->json([
                'code' => 400,
                'message' => 'Missing OrderCode or ClientOrderCode',
            ], 400);
        }

        // Tìm đơn hàng trong CSDL
        // Prefer the exact GHN waybill. Only use ClientOrderCode as a strict
        // local order ID fallback; stripping arbitrary characters can match
        // an unrelated order (for example "abc12" -> order 12).
        $order = $orderCode
            ? Order::where('ghn_order_code', $orderCode)->first()
            : null;
        if (!$order && $clientOrderCode && preg_match('/^(?:ORDER[-_])?(\d+)$/i', $clientOrderCode, $matches)) {
            $order = Order::find((int) $matches[1]);
        }

        if (!$order) {
            Log::warning("GHN Webhook: Order not found for OrderCode: {$orderCode}, ClientOrderCode: {$clientOrderCode}");
            return response()->json([
                'code' => 404,
                'message' => 'Order not found in system',
            ], 404);
        }

        // Nếu đơn hàng chưa lưu mã vận đơn GHN thì bổ sung luôn
        if ($orderCode && empty($order->ghn_order_code)) {
            $order->ghn_order_code = $orderCode;
        }

        DB::beginTransaction();
        try {
            // Định nghĩa nhóm trạng thái
            $deliveringStatuses = ['delivering', 'money_collect_delivering', 'picked', 'storing', 'transporting', 'sorting'];
            $returnStatuses = ['return', 'returning', 'returned', 'return_transporting', 'return_sorting', 'waiting_to_return', 'return_fail'];

            switch (true) {
                case ($ghnStatus === 'delivered'):
                    // 1. Cập nhật trạng thái giao hàng thành công
                    $order->shipping_status = 'delivered';
                    $order->status = 'delivered';

                    // GHN chỉ thu hộ COD. Do not mark a prepaid order paid based
                    // on a delivery callback; its payment gateway confirms it.
                    $isCOD = strtoupper($order->payment_method ?? '') === 'COD';
                    if ($isCOD) {
                        $order->payment_status = 'paid';

                        // Ghi nhận bản ghi giao dịch COD thành công
                        $collectedAmount = $codAmount > 0 ? $codAmount : ($order->cod_amount ?: $order->total_price ?: $order->total_amount ?: 0);

                        PaymentTransaction::updateOrCreate(
                            [
                                'order_id' => $order->id,
                                'gateway' => 'cod',
                            ],
                            [
                                'transaction_code' => 'GHN_COD_' . ($order->ghn_order_code ?: $order->id),
                                'amount' => $collectedAmount,
                                'status' => 'paid',
                                'payload' => json_encode([
                                    'webhook_data' => $payload,
                                    'confirmed_at' => Carbon::now()->toIso8601String(),
                                ]),
                            ]
                        );
                    }

                    $orderNote = "[GHN Webhook " . Carbon::now()->format('d/m/Y H:i') . "] Giao hàng thành công & GHN đã thu tiền COD" . ($description ? " ($description)" : "");
                    $order->notes = trim(($order->notes ? $order->notes . " | " : "") . $orderNote);
                    break;

                case ($ghnStatus === 'cancel'):
                    // Chỉ hủy nếu đơn hàng chưa giao thành công
                    if ($order->shipping_status !== 'delivered') {
                        $order->shipping_status = 'cancelled';
                        $order->status = 'cancelled';
                        $orderNote = "[GHN Webhook " . Carbon::now()->format('d/m/Y H:i') . "] Đơn hàng đã bị hủy trên GHN" . ($description ? " ($description)" : "");
                        $order->notes = trim(($order->notes ? $order->notes . " | " : "") . $orderNote);
                    }
                    break;

                case in_array($ghnStatus, $deliveringStatuses):
                    $order->shipping_status = ($ghnStatus === 'money_collect_delivering') ? 'delivering' : $ghnStatus;
                    $order->status = 'shipping';
                    break;

                case in_array($ghnStatus, $returnStatuses):
                    $order->shipping_status = $ghnStatus;
                    if ($ghnStatus === 'returned') {
                        $order->status = 'return';
                    }
                    break;

                case ($ghnStatus === 'ready_to_pick' || $ghnStatus === 'picking'):
                    $order->shipping_status = $ghnStatus;
                    if ($order->status === 'pending') {
                        $order->status = 'processing';
                    }
                    break;

                default:
                    // Các trạng thái khác vẫn lưu vào shipping_status nếu có giá trị
                    if (!empty($ghnStatus)) {
                        $order->shipping_status = $ghnStatus;
                    }
                    break;
            }

            $order->save();
            DB::commit();

            Log::info("GHN Webhook processed successfully for order #{$order->id}: status={$order->status}, shipping_status={$order->shipping_status}, payment_status={$order->payment_status}");

            return response()->json([
                'code' => 200,
                'message' => 'Processed successfully',
                'order_id' => $order->id,
                'shipping_status' => $order->shipping_status,
                'payment_status' => $order->payment_status,
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("GHN Webhook processing error for order #{$order->id}: " . $e->getMessage());
            return response()->json([
                'code' => 500,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
