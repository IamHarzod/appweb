function DeleteData(url) {
    Swal.fire({
        title: "Xác nhận",
        text: "Bạn có chắc muốn xoá dữ liệu này?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Confirm",
        cancelButtonText: "Cancel",
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: true,
    }).then((result) => {
        if (result.value) {
            $.ajax({
                method: "get",
                url: url,
            })
                .done(function (res) {
                    if (res) {
                        window.location.reload();
                    } else {
                        toastr.error("Đã có lỗi khi xoá dữ liệu", "Lỗi", {
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
                    }
                })
                .fail(function (xhr) {
                    toastr.error("Đã có lỗi khi xoá dữ liệu", "Lỗi", {
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
        }
    });
}

$(function () {
    $("#myTable").DataTable({
        // pageLength: 10,
        // lengthMenu: [5, 10, 25, 50],
        // order: [
        //     [0, 'asc']
        // ],
        // language: {
        //     url: '//cdn.datatables.net/plug-ins/1.10.18/i18n/Vietnamese.json'
        // }
    });
});

window.CloseModal = function (id) {
    const $m = $("#" + id);
    if (!$m.length) {
        console.warn("CloseModal: not found #" + id);
        return;
    }
    // Nếu bạn dùng Bootstrap 4 với jQuery:
    if (typeof $m.modal === "function") {
        console.log("type: ", typeof $.fn.modal);
        $m.modal("hide");
    } else {
        // Bootstrap 5 (không jQuery) – dùng native API
        const modalEl = $m.get(0);
        const inst =
            bootstrap.Modal.getInstance(modalEl) ||
            new bootstrap.Modal(modalEl);
        inst.hide();
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
                }
            })
            .fail(function (xhr) {
                toastr.error("Đã có lỗi khi xoá dữ liệu", "Lỗi", {
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
