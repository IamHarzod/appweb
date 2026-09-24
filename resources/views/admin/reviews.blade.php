@extends('layout.admin_layout')
@section('view-content')
<div class="container-fluid"><h3>Đánh giá sản phẩm</h3><p>Chỉ những đánh giá đang hiển thị được tính vào điểm trung bình của sản phẩm.</p>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="card card-body"><div class="table-responsive"><table class="table"><thead><tr><th>Sản phẩm</th><th>Người đánh giá</th><th>Sao</th><th>Nội dung</th><th>Ngày gửi</th><th>Hiển thị</th></tr></thead><tbody>
@forelse($reviews as $review)<tr><td>@if($review->product)<a href="{{ route('product.detail', $review->product_id) }}">{{ $review->product->name }}</a>@else Sản phẩm đã xóa @endif</td><td>{{ $review->author_name }} @if($review->is_verified_purchase)<span class="badge badge-success">Đã mua hàng</span>@endif</td><td>{{ $review->rating }}/5</td><td style="max-width:400px;white-space:normal;word-break:break-word">{{ $review->comment }}</td><td>{{ $review->created_at?->format('d/m/Y H:i') }}</td><td><form method="POST" action="{{ route('admin.reviews.update', $review) }}">@csrf @method('PATCH')<input type="hidden" name="is_approved" value="{{ $review->is_approved ? 0 : 1 }}"><button class="btn btn-sm {{ $review->is_approved ? 'btn-outline-danger' : 'btn-outline-success' }}">{{ $review->is_approved ? 'Ẩn đánh giá' : 'Hiện đánh giá' }}</button></form></td></tr>@empty<tr><td colspan="6">Chưa có đánh giá nào.</td></tr>@endforelse
</tbody></table></div>{{ $reviews->links() }}</div></div>
@endsection
