<div class="modal fade" id="ModalCreateProduct" tabindex="-1" aria-labelledby="ModalCreateProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ url('/create-product') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalCreateProductLabel">Thêm mới sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        onclick="CloseModal('ModalCreateProduct')" aria-label="Close">X</button>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Quản lý sản phẩm</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-validation">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label" for="product-name">Tên sản phẩm
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control" id="name"
                                                        name="name" placeholder="Nhập tên sản phẩm.." />
                                                </div>
                                            </div>
                                            <!-- <div>Nhập hình ảnh sản phẩm</div> -->

                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label" for="price">Giá <span
                                                        class="text-danger">*</span>
                                                </label>
                                                <div class="col-lg-6">
                                                    <input type="number" class="form-control" id="price"
                                                        name="price" placeholder="Nhập giá của sản phẩm.." />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label" for="stock-quantity">Số lượng tồn
                                                    kho
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-lg-6">
                                                    <input type="number" class="form-control" id="stockQuantity"
                                                        name="stockQuantity" placeholder="Nhập số lượng tồn kho.." />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label"
                                                    for="val-confirm-password">Discount percent
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-lg-6">
                                                    <input type="number" class="form-control" id="discountPercent"
                                                        name="discountPercent" placeholder="nhập % giảm giá!" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label" for="val-suggestions">Mô tả <span
                                                        class="text-danger">*</span>
                                                </label>
                                                <div class="col-lg-6">
                                                    <textarea class="form-control" id="description" name="description" rows="5" placeholder="Nhập mô tả..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label" for="val-skill">Status
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-lg-6">
                                                    <select class="form-control" id="status" name="status">
                                                        <option value="1">Đang kinh doanh</option>
                                                        <option value="0">Đã ngừng kinh doanh</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Trạng thái</label>
                                                <div class="col-sm-10">
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="IsActive" value="1">
                                                            Active</label>
                                                    </div>
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="IsActive" value="0">
                                                            InActive</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group row">
                            <label
                              class="col-lg-4 col-form-label"
                              for="val-currency"
                              >Line
                              <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-6">
                              <input
                                type="text"
                                class="form-control"
                                id="val-currency"
                                name="val-currency"
                                placeholder="nhập line!"
                              />
                            </div>
                          </div> -->
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label" for="val-website">Style
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control" id="style"
                                                        name="style" placeholder="nhập kiểu sản phẩm!" />
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Logo</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <button class="btn btn-primary"
                                                                type="button">Button</button>
                                                        </div>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                accept=".jpg, .png, .jpeg" id="imageURL"
                                                                name="imageURL">
                                                            <label class="custom-file-label">Chọn tệp</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="form-group row">
                            <label
                              class="col-lg-4 col-form-label"
                              for="val-phoneus"
                              >Phone (US)
                              <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-6">
                              <input
                                type="text"
                                class="form-control"
                                id="val-phoneus"
                                name="val-phoneus"
                                placeholder="212-999-0000"
                              />
                            </div>
                          </div>
                          <div class="form-group row">
                            <label
                              class="col-lg-4 col-form-label"
                              for="val-digits"
                              >Digits <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-6">
                              <input
                                type="text"
                                class="form-control"
                                id="val-digits"
                                name="val-digits"
                                placeholder="5"
                              />
                            </div>
                          </div>
                          <div class="form-group row">
                            <label
                              class="col-lg-4 col-form-label"
                              for="val-number"
                              >Number <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-6">
                              <input
                                type="text"
                                class="form-control"
                                id="val-number"
                                name="val-number"
                                placeholder="5.0"
                              />
                            </div>
                          </div>
                          <div class="form-group row">
                            <label
                              class="col-lg-4 col-form-label"
                              for="val-range"
                              >Range [1, 5]
                              <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-6">
                              <input
                                type="text"
                                class="form-control"
                                id="val-range"
                                name="val-range"
                                placeholder="4"
                              />
                            </div>
                          </div>
                          <div class="form-group row">
                            <label class="col-lg-4 col-form-label"
                              ><a href="javascript:void()"
                                >Terms &amp; Conditions</a
                              >
                              <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-8">
                              <label
                                class="css-control css-control-primary css-checkbox"
                                for="val-terms"
                              >
                                <input
                                  type="checkbox"
                                  class="css-control-input mr-2"
                                  id="val-terms"
                                  name="val-terms"
                                  value="1"
                                />
                                <span class="css-control-indicator"></span> I
                                agree to the terms</label
                              >
                            </div>
                          </div>-->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="CloseModal('ModalCreateProduct')"data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-primary">Lưu</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
            </form>
        </div>
    </div>
</div>
</div>
<script></script>
