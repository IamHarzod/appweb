<!-- add_category.blade.php -->
<div class="modal fade" id="ModalCreateCategory" tabindex="-1" aria-labelledby="ModalCreateCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ url('/create-category') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalCreateCategoryLabel">Thêm mới danh mục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        onclick="CloseModal('ModalCreateCategory')" aria-label="Close">X</button>
                </div>
                <div class="modal-body">
                    {{-- Upload ảnh --}}
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Ảnh danh mục</label>
                        <div class="col-sm-10">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <button class="btn btn-primary" type="button">Chọn ảnh</button>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .webp"
                                        id="ImageURL" name="ImageURL">
                                    <label class="custom-file-label">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tên danh mục --}}
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Tên danh mục</label>
                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control" placeholder="Tên danh mục" required>
                        </div>
                    </div>

                    {{-- Mô tả --}}
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Mô tả</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="description" rows="4" style="height: 119px;"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="CloseModal('ModalCreateCategory')"
                        data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
