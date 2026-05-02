$(document).ready(function () {
    // Initialize DataTable for units
    var table = $("#units").DataTable({
        stateSave: true,
        columnDefs: [
            {
                targets: [0, 1, 2, 3],
                width: "10%",
            },
            {
                targets: -1,
                orderable: false,
                width: "20%",
            },
        ],
        lengthMenu: [100, 500, 1000],
        pageLength: 100,
        fixedColumns: true,
    });

    // Custom search functionality
    $("#mySearchInput").on("keyup", function () {
        table.search(this.value).draw();
    });

    // Initialize jQuery UI Dialog for delete confirmation
    $("#delete_dialog").dialog({
        autoOpen: false,
        dialogClass: "alert",
        modal: true,
        buttons: {
            Delete: function () {
                $("#delete_form").submit();
                $(this).dialog("close");
            },
            Cancel: function () {
                $(this).dialog("close");
            },
        },
    });

    // Delete button click handler
    $(document).on("click", ".delete_unit", function () {
        var unitId = $(this).data("unit-id");
        $("#delete_unit_id").val(unitId);
        $("#delete_dialog").dialog("open");
    });

    // Cancel button handler
    $(document).on("click", ".do-not-delete", function () {
        window.location.href = "/admin/units";
    });
});
