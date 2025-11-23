<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <form action="{{ route('coupon.store') }}" method="post">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="ModalCreateCouponLabel">Thêm mới Mã giảm giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="CloseModal('ModalEdit')"
                    aria-label="Close">X</button>
            </div>

            <div class="modal-body">
                <div class="form-validation">
                    <div class="row">
                        <div class="col-xl-6">

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="code">Mã Code <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="code" name="code"
                                        placeholder="VD: SALES50" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="type">Loại mã <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <select class="form-control" id="type" name="type">
                                        <option value="percent">Giảm theo phần trăm (%)</option>
                                        <option value="fixed">Giảm theo tiền mặt (VNĐ)</option>
                                        <option value="free_ship">Miễn phí vận chuyển</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="value">Giá trị giảm <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="number" class="form-control" id="value" name="value"
                                        placeholder="Nhập số tiền hoặc %..." required>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="quantity">Số lượng <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="number" class="form-control" id="quantity" name="quantity"
                                        placeholder="Nhập số lượng..." required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-lg-4 col-form-label" for="expiry_date">Hết hạn <span
                                        class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="date" class="form-control" id="expiry_date" name="expiry_date"
                                        required>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalCreateCoupon')"
                    data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary">Lưu mã</button>
            </div>
        </form>
    </div>
</div>
</div>
