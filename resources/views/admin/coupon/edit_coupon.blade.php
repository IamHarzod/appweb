<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Cập nhật Mã giảm giá</h4>
                        <button type="button" class="close" onclick="CloseModal('ModalEdit')" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('coupon.update', $coupon->id) }}" method="POST">
                            @csrf
                            <div class="form-validation">
                                <div class="row">
                                    <div class="col-xl-6">

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Mã Code
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="code"
                                                    value="{{ $coupon->code }}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Loại mã <span
                                                    class="text-danger">*</span></label>
                                            <div class="col-lg-6">
                                                <select class="form-control" name="type">
                                                    <option value="percent"
                                                        {{ $coupon->type == 'percent' ? 'selected' : '' }}>
                                                        Giảm theo phần trăm (%)</option>
                                                    <option value="fixed"
                                                        {{ $coupon->type == 'fixed' ? 'selected' : '' }}>
                                                        Giảm
                                                        theo tiền mặt (VNĐ)</option>
                                                    <option value="free_ship"
                                                        {{ $coupon->type == 'free_ship' ? 'selected' : '' }}>Miễn phí
                                                        vận
                                                        chuyển
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Giá trị giảm
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" name="value"
                                                    value="{{ $coupon->value }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-6">

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Số lượng mã
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-lg-6">
                                                <input type="number" class="form-control" name="quantity"
                                                    value="{{ $coupon->quantity }}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Ngày hết hạn
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-lg-6">
                                                <input type="date" class="form-control" name="expiry_date"
                                                    value="{{ date('Y-m-d', strtotime($coupon->expiry_date)) }}"
                                                    required>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-8 ml-auto">
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                        <a href="{{ route('coupon.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
