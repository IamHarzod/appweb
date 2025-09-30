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
                .error(function (error) {
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
