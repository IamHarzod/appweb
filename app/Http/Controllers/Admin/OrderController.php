<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    private const TABS = [
        'all' => ['label' => 'Tất cả', 'color' => 'blue', 'statuses' => []],
        'pending' => ['label' => 'Chờ xử lý', 'color' => 'slate', 'statuses' => ['pending', 'not_shipped', 'processing']],
        'ready' => ['label' => 'Chờ lấy hàng', 'color' => 'cyan', 'statuses' => ['ready_to_pick']],
        'picking' => ['label' => 'Đang lấy hàng', 'color' => 'cyan', 'statuses' => ['picking']],
        'delivering' => ['label' => 'Đang giao', 'color' => 'amber', 'statuses' => ['delivering', 'picked', 'storing', 'transporting', 'sorting']],
        'delivered' => ['label' => 'Thành công', 'color' => 'green', 'statuses' => ['delivered']],
        'return' => ['label' => 'Hoàn hàng', 'color' => 'orange', 'statuses' => ['return', 'returning', 'returned', 'return_transporting', 'return_sorting']],
        'cancelled' => ['label' => 'Đã hủy', 'color' => 'red', 'statuses' => ['cancelled']],
    ];

    // Hiển thị danh sách đơn hàng
    public function index(Request $request)
    {
        $paymentLabels = [
            'pending' => 'Chờ thanh toán',
            'initiated' => 'Đang chờ MoMo',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'cancelled' => 'Đã hủy',
            'refund_pending' => 'Chờ hoàn tiền',
            'refunded' => 'Đã hoàn tiền',
        ];

        $shippingLabels = [
            'pending' => 'Chờ tạo vận đơn',
            'not_shipped' => 'Chưa giao hàng',
            'processing' => 'Đang tạo vận đơn',
            'ready_to_pick' => 'Chờ lấy hàng',
            'picking' => 'Đang lấy hàng',
            'picked' => 'Đã lấy hàng',
            'storing' => 'Đang lưu kho',
            'transporting' => 'Đang trung chuyển',
            'sorting' => 'Đang phân loại',
            'delivering' => 'Đang giao hàng',
            'delivered' => 'Giao hàng thành công',
            'return' => 'Chờ hoàn hàng',
            'returning' => 'Đang hoàn hàng',
            'returned' => 'Đã hoàn hàng',
            'return_transporting' => 'Đang chuyển hoàn',
            'return_sorting' => 'Đang phân loại hoàn',
            'cancelled' => 'Đã hủy',
        ];

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'paid', 'paid_momo', 'cod_ordered', 'cod_paid', 'cancelled'])],
            'payment_status' => ['nullable', Rule::in(array_keys($paymentLabels))],
            'shipping_status' => ['nullable', Rule::in(array_keys($shippingLabels))],
            'gateway' => ['nullable', Rule::in(['cod', 'momo', 'unknown'])],
            'tab' => ['nullable', Rule::in(array_keys(self::TABS))],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('date_from') ? ['after_or_equal:date_from'] : [])],
            'per_page' => ['nullable', 'integer', Rule::in([25, 50, 100])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'amount_desc', 'amount_asc'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'date_to.after_or_equal' => 'Ngày kết thúc phải từ ngày bắt đầu trở đi.',
            '*.date_format' => 'Ngày lọc không hợp lệ.',
            '*.in' => 'Giá trị bộ lọc không hợp lệ.',
        ]);

        $paymentId = DB::table('payment_transactions')->select('id')->whereColumn('order_id', 'orders.id')
            ->orderByRaw("CASE WHEN status IN ('paid', 'refund_pending', 'refunded') THEN 0 ELSE 1 END")
            ->orderByDesc('id')->limit(1);

        $source = DB::table('orders')->leftJoin('payment_transactions as payment', function ($join) use ($paymentId) {
            $join->on('payment.order_id', '=', 'orders.id')->where('payment.id', '=', $paymentId);
        })->select('orders.*')
        ->selectRaw("COALESCE(payment.gateway, CASE WHEN orders.status IN ('cod_ordered', 'cod_paid') THEN 'cod' WHEN orders.status IN ('paid', 'paid_momo') THEN 'momo' ELSE 'unknown' END) as gateway")
        ->selectRaw("COALESCE(payment.status, CASE WHEN orders.status = 'cod_ordered' THEN 'pending' WHEN orders.status IN ('cod_paid', 'paid_momo') THEN 'paid' ELSE orders.status END) as payment_status");

        $query = Order::query()->fromSub($source, 'orders');

        foreach (['status', 'payment_status', 'gateway'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $filters[$field]);
            }
        }

        if ($request->filled('search')) {
            $search = trim($filters['search']);
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('shipping_name', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('shipping_phone', 'like', '%'.$search.'%')
                    ->orWhere('ghn_order_code', 'like', '%'.$search.'%')
                    ->orWhereHas('items.product', fn ($products) => $products->where('name', 'like', '%'.$search.'%'));

                if (preg_match('/^(?:#|DH)?0*(\d+)$/i', $search, $matches)) {
                    $query->orWhere('orders.id', $matches[1]);
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<', Carbon::parse($filters['date_to'])->addDay()->startOfDay());
        }

        // Số trên tab theo bộ lọc chung, không bị giới hạn bởi trang hiện tại.
        $shippingCounts = (clone $query)->select('shipping_status')->selectRaw('COUNT(*) as total')
            ->groupBy('shipping_status')->pluck('total', 'shipping_status');

        $tabs = collect(self::TABS)->map(function ($tab, $key) use ($shippingCounts) {
            $tab['count'] = $key === 'all' ? $shippingCounts->sum()
                : collect($tab['statuses'])->sum(fn ($status) => $shippingCounts->get($status, 0));
            return $tab;
        });

        $activeTab = $filters['tab'] ?? 'all';
        if ($activeTab !== 'all') {
            $query->whereIn('shipping_status', self::TABS[$activeTab]['statuses']);
        }

        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', $filters['shipping_status']);
        }

        [$column, $direction] = match ($filters['sort'] ?? 'newest') {
            'oldest' => ['created_at', 'asc'],
            'amount_desc' => ['total_price', 'desc'],
            'amount_asc' => ['total_price', 'asc'],
            default => ['created_at', 'desc'],
        };

        // Hỗ trợ xuất file CSV trang hiện tại ("Xuất trang này")
        if ($request->get('export') === 'csv') {
            $exportOrders = (clone $query)->with('items.product')->orderBy($column, $direction)->orderBy('id', $direction)->get();
            return $this->exportCsv($exportOrders);
        }

        $orders = $query->with('items.product')->orderBy($column, $direction)->orderBy('id', $direction)
            ->paginate((int) ($filters['per_page'] ?? 25))->withQueryString();

        return view('admin.orders.index', compact('orders', 'filters', 'tabs', 'activeTab', 'paymentLabels', 'shippingLabels'));
    }

    // Hiển thị chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'paymentTransactions' => function ($query) {
            $query->latest();
        }])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'shipping_status' => 'required|string',
        ]);

        $newShippingStatus = $request->input('shipping_status');

        // Kiểm tra quy tắc nghiệp vụ: Nếu đơn đang giao -> KHÔNG cho Hủy
        $deliveringStatuses = ['delivering', 'picked', 'storing', 'transporting', 'sorting'];
        if ($newShippingStatus === 'cancelled' && in_array($order->shipping_status, $deliveringStatuses)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đang ở trạng thái vận chuyển/giao hàng, KHÔNG THỂ HỦY!'
                ], 422);
            }
            return back()->with('error', 'Đơn hàng đang ở trạng thái vận chuyển/giao hàng, KHÔNG THỂ HỦY!');
        }

        $order->shipping_status = $newShippingStatus;
        if ($newShippingStatus === 'delivered') {
            $order->status = 'delivered';
        } elseif ($newShippingStatus === 'cancelled') {
            $order->status = 'cancelled';
        } elseif (in_array($newShippingStatus, $deliveringStatuses)) {
            $order->status = 'shipping';
        } elseif (in_array($newShippingStatus, ['pending', 'not_shipped', 'processing'])) {
            $order->status = 'processing';
        }
        $order->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng thành công!',
                'shipping_status' => $order->shipping_status,
            ]);
        }

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    // Hủy đơn hàng (nút Hủy nhanh)
    public function cancel(Request $request, $id, \App\Services\GHNService $ghnService)
    {
        $order = Order::findOrFail($id);

        $deliveringStatuses = ['delivering', 'picked', 'storing', 'transporting', 'sorting'];
        if (in_array($order->shipping_status, $deliveringStatuses)) {
            return back()->with('error', 'Đơn hàng đang giao, KHÔNG THỂ HỦY!');
        }

        // Nếu đơn đã đẩy sang GHN, đồng bộ hủy trên hệ thống GHN
        if (!empty($order->ghn_order_code)) {
            try {
                $ghnService->cancelOrder([$order->ghn_order_code]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Hủy đơn GHN thất bại: ' . $e->getMessage());
            }
        }

        $order->shipping_status = 'cancelled';
        $order->status = 'cancelled';
        $order->save();

        return back()->with('success', 'Đã hủy đơn hàng #' . $order->id . ' thành công!');
    }

    // Đẩy đơn hàng sang GHN (nếu đơn chưa có mã vận đơn)
    public function pushGhn(Request $request, $id, \App\Services\GHNOrderService $ghnOrderService)
    {
        $order = Order::with('items.product')->findOrFail($id);

        if ($order->ghn_order_code) {
            return back()->with('error', 'Đơn hàng này đã có mã vận đơn GHN: ' . $order->ghn_order_code);
        }

        if (!$order->to_district_id || !$order->to_ward_code) {
            return back()->with('error', 'Đơn hàng chưa có thông tin Quận/Huyện hoặc Phường/Xã chuẩn GHN để tạo vận đơn.');
        }

        $isPaid = in_array(strtoupper($order->payment_method ?? ''), ['MOMO', 'VNPAY', 'PAID']);
        $ghnRes = $ghnOrderService->create($order, $isPaid);

        if (!empty($ghnRes['data']['order_code'])) {
            $order->ghn_order_code = $ghnRes['data']['order_code'];
            $order->shipping_status = 'ready_to_pick';
            if ($order->status === 'pending') {
                $order->status = 'processing';
            }
            $order->save();

            return back()->with('success', 'Tạo vận đơn GHN thành công! Mã vận đơn: ' . $order->ghn_order_code);
        }

        $msg = $ghnRes['message'] ?? 'Không thể tạo vận đơn GHN';
        return back()->with('error', 'Lỗi tạo vận đơn GHN: ' . $msg);
    }

    // Xóa đơn hàng
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Xóa đơn hàng thành công!');
    }

    // Xuất CSV danh sách đơn hàng
    private function exportCsv($orders)
    {
        $fileName = 'don_hang_' . date('Y_m_d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 cho Excel đọc tiếng Việt không lỗi font
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, [
                'Mã đơn hàng',
                'Ngày tạo',
                'Khách hàng',
                'Số điện thoại',
                'Tổng tiền',
                'COD cần thu',
                'Mã vận đơn',
                'Đơn vị VC',
                'Trạng thái giao hàng',
                'Trạng thái thanh toán',
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    'DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    $order->created_at?->format('d/m/Y H:i'),
                    $order->shipping_name ?: $order->name,
                    $order->shipping_phone ?: $order->phone,
                    $order->total_price ?: $order->total_amount,
                    $order->cod_amount ?: 0,
                    $order->ghn_order_code ?: 'Chưa có',
                    $order->shipping_carrier ?: 'GHN',
                    $order->shipping_status,
                    $order->payment_status ?? $order->status,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
