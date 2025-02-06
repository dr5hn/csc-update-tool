import $, { data } from "jquery";

$(function () {
    // hide all tables and dropdowns initially
    $("#subregions-table").hide();
    $("#countries-table").hide();
    $("#states-table").hide();
    $("#cities-table").hide();
    $("#countries-dropdown").hide();
    $("#states-dropdown").hide();
    $("#actions").hide();

    function switchTab(tabId, tableId) {
        $(
            "#regions-table, #subregions-table, #countries-table, #states-table, #cities-table"
        ).hide();

        $(tableId).show();
        $(".active-tab").removeClass("active-tab");
        $("#" + tabId).addClass("active-tab");
        $(".search-input").attr("id", "search-input-" + tabId);

        var tabTitle = $("#" + tabId).text();
        $("#form-title").text("Database Changes Request - " + tabTitle);

        // update session storage data
        loadSavedData();
    }

    // Function to switch the tab
    $("#table-tabs button").on("click", function () {
        switchTab($(this).attr("id"), $(this).attr("data-table"));

        if (!$(this).is("#states-tab") && !$(this).is("#cities-tab")) {
            $("#countries-dropdown").hide(); // Hide the dropdown
            $("#states-dropdown").hide();
        }
    });

    // Function to filter the table rows based on the search input for regions
    $("#search-input").on("input", function () {
        const searchText = $(this).val().toLowerCase();
        $("#regions-table").load(`/regions?search=${searchText}`);
    });

    // Function to filter the table rows based on the search input for subregions
    $("#search-input").on("input", function () {
        const searchText = $(this).val().toLowerCase();
        $("#subregions-table").load(`/subregions?search=${searchText}`);
    });

    // Function to filter the table rows based on the search input for countries
    $("#search-input").on("input", function () {
        const searchText = $(this).val().toLowerCase();
        $("#countries-table").load(`/countries?search=${searchText}`);
    });

    // Function to filter the table rows based on the search input for states
    $("#search-input").on("input", function () {
        const searchText = $(this).val().toLowerCase();
        const country_id = $("#countries-select").val();
        $("#states-table").load(
            `/states?country_id=${country_id}&search=${searchText}`
        );
    });

    // Function to filter the table rows based on the search input for cities
    $("#search-input").on("input", function () {
        const searchText = $(this).val().toLowerCase();
        const country_id = $("#countries-select").val();
        const state_id = $("#states-select").val();
        $("#cities-table").load(
            `/cities-by-country?country_id=${country_id}&search=${searchText}`
        );
        $("#cities-table").load(
            `/cities-by-state?state_id=${state_id}&search=${searchText}`
        );
    });

    // When the "States" tab is clicked, show the dropdown
    $("#states-tab").on("click", () => {
        $("#countries-select").prop("selectedIndex", 0);
        $("#countries-dropdown").show();
        $("#states-dropdown").hide();
        $("#countries-select").on("change", function () {
            const searchText = $("#search-input-states-tab")
                .val()
                .toLowerCase();
            $("#states-table")
                .show()
                .load(
                    `/states?country_id=${$(this).val()}&search=${searchText}`
                );
        });
    });

    // When the "Cities" tab is clicked, show the dropdown
    $("#cities-tab").on("click", () => {
        $("#countries-select").prop("selectedIndex", 0);
        $("#states-select").prop("selectedIndex", 0);
        $("#countries-dropdown").show();
        $("#states-dropdown").show();
        $("#cities-table").show();
        $("#countries-select").on("change", function () {
            $("#states-table").hide();
            const country_id = $(this).val();
            const searchText = $("#search-input-cities-tab")
                .val()
                .toLowerCase();
            $.get(
                `/states-dropdown?country_id=${country_id}&search=${searchText}`,
                (data) => {
                    $("#states-dropdown").html(data);
                    $("#states-select").on("change", function () {
                        const state_id = $(this).val();
                        const searchText = $("#search-input-cities-tab")
                            .val()
                            .toLowerCase();
                        $("#cities-table")
                            .show()
                            .load(
                                `/cities-by-state?state_id=${state_id}&search=${searchText}`
                            );
                    });
                }
            );
            $("#cities-table").load(
                `/cities-by-country?country_id=${country_id}&search=${searchText}`
            );
        });
    });

    // Add new row functionality
    $("#add-row-btn").on("click", function () {
        addNewRow();
    });

    // Event listener for the Edit button
    $("body").on("click", ".edit-btn", function () {
        var row = $(this).closest("tr");
        var inputs = row.find("input");

        // Enable the inputs to be editable
        inputs.prop("disabled", false);

        // Hide the Edit button and show the Save button
        $(this).addClass("hidden");
        row.find(".save-btn").removeClass("hidden");
    });

    // Event listener for the Save button
    // $(".save-btn").on("click", function () {
    $("body").on("click", ".save-btn", function () {
        var row = $(this).closest("tr");
        var inputs = row.find("input");

        // Disable the inputs after saving
        inputs.prop("disabled", true);

        // Save data to sessionStorage
        var rowData = {};
        inputs.each(function () {
            rowData[$(this).attr("name")] = $(this).val();
        });

        var id = row.data("id");
        // console.log(id);
        sessionStorage.setItem(id, JSON.stringify(rowData));

        // Hide the Save button and show the Edit button again
        row.find(".edit-btn").removeClass("hidden");
        $(this).addClass("hidden");
    });

    // Event listener for the Delete button
    // $(".delete-btn").on("click", function () {
    $("body").on("click", ".delete-btn", function () {
        var row = $(this).closest("tr");
        var Id = row.data("id");

        // Remove the row data from sessionStorage
        sessionStorage.removeItem("deleted-" + Id);
        sessionStorage.setItem("deleted-" + Id, true);
        row.addClass("deleted-row");

        // hide the delete and edit buttons and show the undo button
        $(this).hide();
        row.find(".edit-btn").hide();
        row.find(".undo-btn").show();
        row.find(".save-btn").addClass("hidden");
    });

    // Event listener for the Undo button
    // $(".undo-btn").on("click", function () {
    $("body").on("click", ".undo-btn", function () {
        var row = $(this).closest("tr");
        var Id = row.data("id");

        // Remove the row data from sessionStorage
        sessionStorage.removeItem("deleted-" + Id);

        // remove the deleted-row class
        row.removeClass("deleted-row");

        // hide the undo button and show the delete and edit buttons
        $(this).hide();
        row.find(".delete-btn").show();
        row.find(".edit-btn").show();
    });

    //Load data from sessionStorage when the page is reloaded
    function loadSavedData() {
        for (var i = 0; i < sessionStorage.length; i++) {
            var key = sessionStorage.key(i);
            var tables = ["region", "subregion", "country", "state", "city"];

            tables.forEach((table) => {
                var savedData = JSON.parse(sessionStorage.getItem(key));

                if (key.startsWith(table + "_")) {
                    var id = key.split("_")[1];
                    var row = $(`tr[data-id=${table}_${id}]`);

                    // Populate the inputs with saved data
                    $.each(savedData, function (name, value) {
                        row.find('input[name="' + name + '"]').val(value);
                    });
                }
                if (key.startsWith("deleted-")) {
                    console.log(key);
                    var id = key.split("-")[1];
                    var row = $(`tr[data-id=${id}]`);
                    row.addClass("deleted-row");
                    row.find(".delete-btn, .edit-btn").hide();
                    row.find(".undo-btn").show();
                }
                if (key.startsWith("added-")) {
                    var id = key.split("-")[1];
                    var type = id.split("_")[0];
                    var rowid = id.split("_")[1];
                    console.log(id);
                    let row = $(`tr[data-id=added-${id}]`);

                    if (!row.length && table == type) {
                        addNewRow(rowid);
                        let row = $(`tr[data-id=added-${id}]`);
                        console.log(row);
                        if (row.length) {
                            row.addClass("added-row");

                            // Populate the inputs with saved data
                            $.each(savedData, function (name, value) {
                                row.find('input[name="' + name + '"]').val(
                                    value
                                );
                            });
                        }
                    }
                }
            });
        }
    }
    loadSavedData();

    function addNewRow(newId = false) {
        // Get the active table
        const activeTable = $(".active-tab").attr("data-table");
        const tableBody = $(activeTable).find("#table-body");

        // Get the table type from the active tab ID
        const tableType = $(".active-tab")
            .attr("id")
            .replace("-tab", "")
            .replace("s", "");

        if (!newId) {
            // Find the highest existing ID
            let maxId = 0;
            tableBody.find("tr").each(function () {
                const rowId = $(this).data("id");
                if (rowId) {
                    const id = parseInt(rowId.split("_")[1]);
                    maxId = Math.max(maxId, id);
                }
            });

            // Create new row ID
            newId = maxId + 1;
        }

        // Create new row with empty inputs
        const newRow = $("<tr>")
            .addClass("")
            .attr("data-id", `added-${tableType}_${newId}`);

        // Add ID cell
        newRow.append(`
        <td class="text-xs text-center">${newId}
            <input type="hidden" name="id" value="${newId}">
        </td>
    `);

        // Add input fields based on table headers
        const headers = $(activeTable).find("thead th");
        headers.each(function (index) {
            if (index > 0 && index < headers.length - 1) {
                // Skip ID column and Actions column
                newRow.append(`
                <td class="px-1 py-1">
                    <input type="text" name="${$(this)
                        .text()
                        .toLowerCase()
                        .replace(/\s+/g, "_")}" value="" disabled>
                </td>
            `);
            }
        });

        // Add action buttons
        newRow.append(`
        <td class="text-center">
            <button class="edit-btn text-blue-500 hover:text-blue-700">Edit</button>
            <button class="save-btn text-green-500 hover:text-green-700 hidden">Save</button>
            <button class="delete-btn text-red-500 hover:text-red-700">Delete</button>
            <button class="undo-btn text-gray-500 hover:text-gray-700 hidden">Undo</button>
        </td>
    `);

        // Append the new row to the table
        tableBody.append(newRow);

        // Enable editing for the new row
        const editBtn = newRow.find(".edit-btn");
        editBtn.trigger("click");
    }
});
