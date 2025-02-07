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

    function storeOriginalData() {
        $("table tr").each(function () {
            let rowData = {};
            $(this)
                .find("input")
                .each(function () {
                    rowData[$(this).attr("name")] = $(this).val();
                });
            $(this).data("original", rowData);
        });
    }

    // Call on page load
    storeOriginalData();

    // Function to check if row data has changed
    function hasRowChanged(row) {
        const originalData = $(row).data("original");
        if (!originalData) return false;

        let changed = false;
        $(row)
            .find("input")
            .each(function () {
                const name = $(this).attr("name");
                const currentValue = $(this).val();
                if (originalData[name] !== currentValue) {
                    changed = true;
                    return false; // Break the loop
                }
            });
        return changed;
    }

    // Helper function to check if all inputs in a row are empty
    function isRowEmpty(row) {
        let isEmpty = true;
        row.find('input[type="text"]').each(function () {
            if ($(this).val().trim() !== "") {
                isEmpty = false;
                return false; // Break the loop
            }
        });
        return isEmpty;
    }

    // Event listener for the Edit button
    $("body").on("click", ".edit-btn", function () {
        var row = $(this).closest("tr");
        var inputs = row.find("input");

        // Enable the inputs to be editable
        inputs.prop("disabled", false);

        inputs.on("input", function () {
            if (hasRowChanged(row)) {
                row.addClass("changed-row");
            } else {
                row.removeClass("changed-row");
            }
        });

        // Hide the Edit button and show the Save button
        $(this).addClass("hidden");
        row.find(".save-btn").removeClass("hidden");
    });

    // Event listener for the Save button
    $("body").on("click", ".save-btn", function () {
        var row = $(this).closest("tr");
        var inputs = row.find("input");
        var id = row.data("id");

        // Only keep changed-row class if data actually changed
        if (!hasRowChanged(row)) {
            row.removeClass("changed-row");
        }

        // Disable the inputs after saving
        inputs.prop("disabled", true);
        inputs.off("input"); // Remove input handlers

        // Check if this is a newly added row and all fields are empty
        if (id.startsWith("added-") && isRowEmpty(row)) {
            // Don't save empty rows to sessionStorage
            row.find(".edit-btn").removeClass("hidden");
            $(this).addClass("hidden");
            return;
        }

        // Save data to sessionStorage
        var rowData = {};
        inputs.each(function () {
            rowData[$(this).attr("name")] = $(this).val();
        });

        // Don't mark new rows as changed
        if (!id.startsWith("added-") && hasRowChanged(row)) {
            sessionStorage.setItem(id, JSON.stringify(rowData));
            row.addClass("changed-row");
        }

        if (id.startsWith("added-") && !isRowEmpty(row)) {
            sessionStorage.setItem(id, JSON.stringify(rowData));
            row.addClass("added-row");
        }

        // Hide the Save button and show the Edit button again
        row.find(".edit-btn").removeClass("hidden");
        $(this).addClass("hidden");
    });

    // Event listener for the Delete button
    $("body").on("click", ".delete-btn", function () {
        var row = $(this).closest("tr");
        var Id = row.data("id");

        // Check if this is a newly added row
        if (Id.startsWith("added-")) {
            // For new rows, simply remove the row from DOM
            row.remove();
            sessionStorage.removeItem(Id);
            return;
        }

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

            // Handle edited rows
            if (key.startsWith("changed-")) {
                var rowId = key.replace("changed-", "");
                var row = $(`tr[data-id="${rowId}"]`);
                if (row.length) {
                    row.addClass("changed-row");
                }
            }

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
                    let row = $(`tr[data-id=added-${id}]`);

                    if (!row.length && table == type) {
                        addNewRow(rowid);
                        let row = $(`tr[data-id=added-${id}]`);
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
        newRow.addClass("added-row");
        tableBody.append(newRow);
    }

    // Submit button click handler
    $("#change-request-submit").on("click", function (e) {
        e.preventDefault();

        // Validate form
        const title = $("#request_title").val();
        const description = $("#request_description").val();

        if (!title || !description) {
            alert("Please fill in both title and description");
            return;
        }

        // Collect data from sessionStorage
        const changes = {
            modifications: {},
            deletions: [],
            additions: {},
        };

        // Go through sessionStorage
        for (let i = 0; i < sessionStorage.length; i++) {
            const key = sessionStorage.key(i);

            // Handle deleted items
            if (key.startsWith("deleted-")) {
                const id = key.replace("deleted-", "");
                changes.deletions.push(id);
                continue;
            }

            // Handle added items
            if (key.startsWith("added-")) {
                const data = JSON.parse(sessionStorage.getItem(key));
                if (!changes.additions[key]) {
                    changes.additions[key] = data;
                }
                continue;
            }

            // Handle modified items
            const tables = ["region", "subregion", "country", "state", "city"];
            tables.forEach((table) => {
                if (key.startsWith(table + "_")) {
                    if (!changes.modifications[table]) {
                        changes.modifications[table] = {};
                    }
                    changes.modifications[table][key] = JSON.parse(
                        sessionStorage.getItem(key)
                    );
                }
            });
        }

        $("#new_data").val(JSON.stringify(changes));

        $("#change-request-form").trigger("submit");
    });
});
