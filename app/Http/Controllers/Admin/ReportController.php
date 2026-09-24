<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class ReportController extends Controller
{
    public const STATUSES = ['pending' => 'Chờ xử lý', 'confirmed' => 'Đã xác nhận', 'processing' => 'Đang xử lý', 'shipping' => 'Đang giao', 'completed' => 'Hoàn tất', 'delivered' => 'Đã giao', 'paid' => 'Đã thanh toán', 'paid_momo' => 'Đã thanh toán MoMo', 'cod_ordered' => 'Chờ thu COD', 'cod_paid' => 'Đã thu COD', 'cancelled' => 'Đã hủy'];

    private function filtered(Request $request): array
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('date_from') ? ['after_or_equal:date_from'] : [])],
            'status' => ['nullable', Rule::in(array_keys(self::STATUSES))],
        ]);
        $query = Order::query();
        if (!empty($filters['date_from'])) $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        if (!empty($filters['date_to'])) $query->where('created_at', '<', Carbon::parse($filters['date_to'])->addDay()->startOfDay());
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        return [$query, $filters];
    }

    private function completed($query)
    {
        return $query->where('status', '!=', 'cancelled')->where(function ($q) {
            $q->whereIn('status', ['completed', 'delivered'])->orWhere('shipping_status', 'delivered');
        });
    }

    public function index(Request $request)
    {
        [$query, $filters] = $this->filtered($request);
        $totalOrders = (clone $query)->count();
        $totalRevenue = $this->completed(clone $query)->sum('total_amount');
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'user')->count();
        $recentOrders = (clone $query)->latest('id')->paginate(15)->withQueryString();
        $statusCounts = (clone $query)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $daily = $this->completed(clone $query)->selectRaw('DATE(created_at) as day, SUM(total_amount) as total')->groupByRaw('DATE(created_at)')->orderByDesc('day')->limit(30)->get()->reverse();
        $statuses = self::STATUSES;
        return view('admin.dashboard', compact('filters', 'totalOrders', 'totalRevenue', 'totalProducts', 'totalUsers', 'recentOrders', 'statusCounts', 'daily', 'statuses'));
    }

    public function export(Request $request, string $format)
    {
        abort_unless(in_array($format, ['xlsx', 'pdf'], true), 404);
        [$query, $filters] = $this->filtered($request);
        $count = (clone $query)->count();
        if ($format === 'pdf' && $count > 1000) {
            return back()->with('error', 'PDF tối đa 1.000 đơn. Hãy thu hẹp khoảng ngày hoặc xuất Excel.');
        }
        $filename = 'don-hang-'.now()->format('Ymd-His').'.'.$format;
        if ($format === 'pdf') {
            $orders = $query->orderBy('id')->get();
            $pdf = new Dompdf(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans']);
            $pdf->loadHtml(view('admin.reports.pdf', ['orders' => $orders, 'filters' => $filters, 'statuses' => self::STATUSES])->render(), 'UTF-8');
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();
            return response($pdf->output(), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="'.$filename.'"']);
        }
        $path = tempnam(storage_path('app'), 'report-');
        try {
            $writer = new Writer();
            $writer->openToFile($path);
            $writer->addRow(Row::fromValues(['BÁO CÁO ĐƠN HÀNG — 36SHOP']));
            $writer->addRow(Row::fromValues(['Từ ngày', $filters['date_from'] ?? 'Tất cả', 'Đến ngày', $filters['date_to'] ?? 'Tất cả', 'Trạng thái', self::STATUSES[$filters['status'] ?? ''] ?? 'Tất cả']));
            $writer->addRow(Row::fromValues(['Mã đơn', 'Ngày đặt', 'Khách hàng', 'Email', 'Điện thoại', 'Trạng thái', 'Thanh toán', 'Giảm giá (VND)', 'Phí vận chuyển (VND)', 'Tổng tiền (VND)']));
            $total = 0;
            foreach ($query->orderBy('id')->lazyById(500) as $order) {
                $values = [(int) $order->id, $order->created_at?->format('d/m/Y H:i'), $order->shipping_name, $order->shipping_email, $order->shipping_phone, self::STATUSES[$order->status] ?? $order->status, $order->payment_method, (float) $order->discount_amount, (float) $order->shipping_fee, (float) $order->total_amount];
                // Customer text must never become an executable spreadsheet formula.
                $writer->addRow(new Row(array_map(fn ($value) => is_string($value)
                    ? new \OpenSpout\Common\Entity\Cell\StringCell($value, null)
                    : \OpenSpout\Common\Entity\Cell::fromValue($value), $values)));
                $total += (float) $order->total_amount;
            }
            $writer->addRow(Row::fromValues(['Tổng cộng', $count.' đơn', '', '', '', '', '', '', '', $total]));
            $writer->close();
        } catch (\Throwable $e) {
            @unlink($path);
            throw $e;
        }
        return response()->download($path, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])->deleteFileAfterSend(true);
    }
}
