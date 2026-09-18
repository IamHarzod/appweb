function DeleteData(url) {
    Swal.fire({
        title: "Xác nhận xóa",
        text: "Bạn có chắc chắn muốn xoá dữ liệu này? Thao tác này không thể hoàn tác.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Đồng ý xóa",
        cancelButtonText: "Hủy bỏ",
        reverseButtons: true,
        allowOutsideClick: false,
    }).then((result) => {
        if (result.isConfirmed || result.value) {
            const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
            $.ajax({
                method: "POST",
                url: url,
                data: {
                    _method: "DELETE",
                    _token: token
                },
                headers: {
                    'X-CSRF-TOKEN': token
                }
            })
            .done(function (res) {
                toastr.success("Xóa dữ liệu thành công!", "Thành công");
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            })
            .fail(function (xhr) {
                let msg = "Đã có lỗi khi xoá dữ liệu";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg, "Lỗi");
            });
        }
    });
}

$(function () {
    if ($("#myTable").length && !$.fn.DataTable.isDataTable("#myTable")) {
        $("#myTable").DataTable({
            pageLength: 10,
            language: {
                search: "Tìm kiếm:",
                lengthMenu: "Hiển thị _MENU_ bản ghi",
                info: "Hiển thị _START_ đến _END_ trong _TOTAL_ bản ghi",
                infoEmpty: "Hiển thị 0 đến 0 trong 0 bản ghi",
                infoFiltered: "(lọc từ _MAX_ tổng số bản ghi)",
                zeroRecords: "Không tìm thấy dữ liệu phù hợp",
                emptyTable: "Chưa có dữ liệu",
                paginate: {
                    first: "Đầu",
                    previous: "Trước",
                    next: "Tiếp",
                    last: "Cuối"
                }
            }
        });
    }
});

window.CloseModal = function (id) {
    const $m = $("#" + id);
    if (!$m.length) return;
    if (typeof $m.modal === "function") {
        $m.modal("hide");
    } else if (window.bootstrap && $m.get(0)) {
        const el = $m.get(0);
        const inst = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        inst.hide();
    } else {
        $m.removeClass("show").hide();
        $("body").removeClass("modal-open").css("padding-right", "");
        $(".modal-backdrop").remove();
    }
};
function OpenModal(id, url) {
    id = url != null ? "ModalEdit" : id;
    const modal = $("#" + id);

    if (url != null) {
        $.ajax({
            method: "get",
            url: url,
        })
            .done(function (res) {
                if (res) {
                    modal.html(res);
                    // Chỉ show sau khi đã inject nội dung modal
                    if (!modal.parent().is("body")) modal.appendTo("body");
                    modal.modal("show");
                }
            })
            .fail(function (xhr) {
                toastr.error("Đã có lỗi khi thêm dữ liệu", "Lỗi", {
                    timeOut: 500000000,
                    closeButton: !0,
                    debug: !1,
                    newestOnTop: !0,
                    progressBar: !0,
                    positionClass: "toast-top-right",
                    preventDuplicates: !0,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    tapToDismiss: !1,
                });
            });
        return; // không show trước khi nạp xong
    }
    if (!modal.parent().is("body")) modal.appendTo("body");
    modal.modal("show");
}

// Dọn backdrop “kẹt” (đặt một lần khi trang load)
$(document).on("hidden.bs.modal", ".modal", function () {
    // nếu vì lý do nào đó backdrop không bị gỡ, ta ép gỡ
    $(".modal-backdrop").remove();
    $("body").removeClass("modal-open").css("padding-right", "");
});
