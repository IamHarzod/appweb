    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('category.update', ['id' => $category->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalEditLabel">Chỉnh sửa danh mục</h5>
                    <button type="button" class="close" onclick="CloseModal('ModalEdit')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Ảnh danh mục</label>
                        @if (!empty($category->ImageURL))
                            <div class="mb-2">
                                <img src="{{ asset('public/uploads/categories/' . $category->ImageURL) }}" alt="" width="60">
                            </div>
                        @endif
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <button class="btn btn-primary" type="button">Chọn tệp</button>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" accept=".jpg, .png, .jpeg, .webp" id="ImageURL" name="ImageURL">
                                <label class="custom-file-label">Choose file</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tên danh mục</label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" placeholder="Tên danh mục">
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea class="form-control" name="description" rows="4" style="height: 119px;">{{ old('description', $category->description) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalEdit')">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>


