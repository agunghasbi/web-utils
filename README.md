WEB UTILS

# ServerSideDatatableExportAll Usage
```
$("table").DataTable({
    ajax: {
        url: baseUrl("/laporan-data-barang-fetch"),
        headers: { 'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') },
        dataSrc: "data",
        type: "POST",
    },
    processing: true,
    serverSide: true,
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    order: [],
    info: true,
    autoWidth: false,
    columns: [
        {
            data: "",
            render: function (data, i, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
            width: "20px",
        },
        { data: "barang_kode" },
        { data: "barang_nama" },
        { data: "barang_kategori_nama", name: "barang_kategori.barang_kategori_nama" },
        { data: "barang_satuan_nama", name: "barang_satuan.barang_satuan_nama" },
        {
            data: "barang_harga_pokok",
            render: function (data, i, row) {
                return data.toLocaleString()
            }
        },
        {
            data: "barang_harga_jual",
            render: function (data, i, row) {
                return data.toLocaleString()
            }
        },
        { data: "all_stok" }
    ],
    dom: 'B<"row mt-3"<"col-sm-12 col-md-6" l><"col-sm-12 col-md-6" f>> rtip',
    buttons: [{
        extend: 'pdf',
        title: 'Laporan Data Semua Barang',
        action: $.serverSideDatatableExportAll
    }, {
        extend: 'excel',
        title: 'Laporan Data Semua Barang',
        action: $.serverSideDatatableExportAll
    }, {
        extend: 'print',
        title: 'Laporan Data Semua Barang',
        action: $.serverSideDatatableExportAll
    }],
    createdRow: function (row, data) {
        // ==> Edit Button
        $(".action-edit", row).click(function (e) {
            e.preventDefault();

            $('input[name="id"]').val(data.durasi_rental_id);
            $('input[name="nama"]').val(data.durasi_rental_nama);
            $('input[name="jam"]').val(data.durasi_rental_jam);
            $('select[name="status"]').val(data.status).trigger("change");

            $(".message-error").empty();
            $("#modal-form").modal("show");
        });

        // ==> Delete Button
        $(".action-hapus", row).click(function (e) {
            e.preventDefault();
            swal({
                title: "Peringatan !",
                text: `Anda yakin akan menghapus data ini ??`,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#993333",
                cancelButtonColor: "#5d5d5d",
                cancelButtonText: "Tidak",
                confirmButtonText: "Hapus",
            }).then(
                function () {
                    $.LoadingOverlay("show");
                    $.httpRequest({
                        url: baseUrl(`/durasi-rental/${data.durasi_rental_id}`),
                        method: "DELETE",
                        response: (res) => {
                            $.LoadingOverlay("hide");
                            if (res.statusCode == 200) {
                                swal("Sukses !", res.message, "success");
                                table.ajax.reload();
                            }
                        },
                    });
                },
                function (dismiss) {
                    if (dismiss === "cancel") {
                    }
                }
            );
        });
    },
    initComplete: function () {

        /**
         * Styling Export PDF BUtton
         */
        let pdfButton = $(".buttons-pdf");
        pdfButton.removeClass("btn btn-secondary buttons-html5");
        pdfButton.addClass("btn btn-danger");
        pdfButton.prepend(
            '<i class="fa fa-file-pdf" aria-hidden="true"></i>&nbsp;'
        );

        /**
         * Styling Export EXCEL BUtton
         */
        let excelButton = $(".buttons-excel");
        excelButton.removeClass("btn btn-secondary buttons-html5");
        excelButton.addClass("btn btn-success");
        excelButton.prepend(
            '<i class="fa fa-file-excel" aria-hidden="true"></i>&nbsp;'
        );

        /**
         * Styling PRINT BUtton
         */
        let printButton = $(".buttons-print");
        printButton.removeClass("btn btn-secondary buttons-html5");
        printButton.addClass("btn btn-secondary");
        printButton.prepend('<i class="fa fa-print" aria-hidden="true"></i>&nbsp;');
    },
});
```
