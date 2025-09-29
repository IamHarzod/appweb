<div class="modal fade" id="ModalCreateBrand" tabindex="-1" aria-labelledby="ModalCreateBrandLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ url('/create-brand') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalCreateBrandLabel">Thêm mới thương hiệu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        onclick="CloseModal('ModalCreateBrand')" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Logo</label>
                        <div class="col-sm-10">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <button class="btn btn-primary" type="button">Button</button>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" accept=".jpg, .png, .jpeg"
                                        id="Logo" name="Logo">
                                    <label class="custom-file-label">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Tên thương hiệu</label>
                        <div class="col-sm-10">
                            <input type="text" name="TenThuongHieu" class="form-control"
                                placeholder="Tên thương hiệu">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Trạng thái</label>
                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="TrangThai" value="1">Hiển thị</label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="TrangThai" value="0">Ẩn</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Mô tả</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="MoTa" rows="4" id="comment" style="height: 119px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalCreateBrand')"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
