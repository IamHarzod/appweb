    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formUpdateProduct" action="{{ url('/update-product/' . $product->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalUpdateProductLabel">Cập nhật sản phẩm</h5>
                    <button type="button" class="close" onclick="CloseModal('ModalEdit')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Tên sản phẩm</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="name" value="{{ $product->name }}"
                                placeholder="Nhập tên sản phẩm">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Giá</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="price" value="{{ $product->price }}"
                                placeholder="Nhập giá">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Số lượng tồn</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="stockQuantity"
                                value="{{ $product->stockQuantity }}" placeholder="Nhập số lượng tồn kho">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Giảm giá (%)</label>
                        <div class="col-sm-9">
                            <input type="number" step="0.01" class="form-control" name="discountPercent"
                                value="{{ $product->discountPercent }}" placeholder="Nhập % giảm giá">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Mô tả</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="description" rows="4" placeholder="Mô tả">{{ $product->description }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Trạng thái KD</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="status">
                                <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Đang kinh doanh
                                </option>
                                <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Ngừng kinh doanh
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Hiển thị</label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="IsActive" value="1"
                                        {{ $product->IsActive ? 'checked' : '' }}> Hiện
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="IsActive" value="0"
                                        {{ !$product->IsActive ? 'checked' : '' }}> Ẩn
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="val-skill">Thương hiệu
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-6">
                            <select class="form-control" id="id_brand" name="id_brand">
                                <option value="">Chọn thương hiệu </option>
                                @foreach ($brand as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $item->id == $product->id_brand ? 'selected' : '' }}>
                                        {{ $item->TenThuongHieu }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="val-skill">Danh mục
                            <span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-6">
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">Chọn danh mục </option>
                                @foreach ($category as $item)
                                    <option value="{{ $item->id }}"
                                        {{ $item->id == $product->category_id ? 'selected' : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Style</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="style" value="{{ $product->style }}"
                                placeholder="Nhập kiểu sản phẩm">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Hình ảnh</label>
                        <div class="col-sm-9">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <button class="btn btn-primary" type="button">Button</button>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" accept=".jpg, .png, .jpeg"
                                        name="imageURL">
                                    <label class="custom-file-label">Chọn tệp</label>
                                </div>
                            </div>
                            @if ($product->imageURL)
                                <img src="{{ asset('public/uploads/products/' . $product->imageURL) }}"
                                    width="100" height="100" alt="Ảnh sản phẩm">
                            @endif
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
