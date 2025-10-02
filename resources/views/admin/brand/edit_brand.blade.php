    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ url('/update-brand') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalEditLabel">Chỉnh sửa thương hiệu</h5>

                    <button type="button" class="close" onclick="CloseModal('ModalEdit')"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Logo</label>
                        <div class="col-sm-10">
                            <input type="hidden" name="id" value="{{ $brand->id }}">
                            @if ($brand->Logo != null)
                                <div>
                                    <img src="{{ asset('public/uploads/brands/' . $brand->Logo) }}" alt=""
                                        width="50">
                                </div>
                            @endif
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
                            <input type="text" name="TenThuongHieu" value="{{ $brand->TenThuongHieu }}"
                                class="form-control" placeholder="Tên thương hiệu">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Trạng thái</label>
                        <div class="col-sm-10">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="TrangThai" {{ $brand->TrangThai ? 'checked' : '' }}
                                        value="1">Hiển thị</label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="TrangThai" {{ !$brand->TrangThai ? 'checked' : '' }}
                                        value="0">Ẩn</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Mô tả</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="MoTa" rows="4" id="comment" style="height: 119px;">{{ $brand->MoTa }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalEdit')">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
