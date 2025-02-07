import $ from "jquery";

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
    $(".save-btn").on("click", function () {
        var row = $(this).closest("tr");
        var inputs = row.find("input");

        // Disable the inputs after saving
        inputs.prop("disabled", true);

        // Save data to sessionStorage
        var rowData = {};
        inputs.each(function () {
            rowData[$(this).attr("name")] = $(this).val();
        });

        var regionId = row.find(".edit-btn").data("id");
        sessionStorage.setItem("region_" + regionId, JSON.stringify(rowData));

        // Hide the Save button and show the Edit button again
        row.find(".edit-btn").removeClass("hidden");
        $(this).addClass("hidden");
    });

    // Event listener for the Delete button
    $(".delete-btn").on("click", function () {
        var row = $(this).closest("tr");
        var regionId = $(this).data("id");

        // Remove the row data from sessionStorage
        sessionStorage.removeItem("region_" + regionId);

        // Optionally, remove the row from the table
        row.remove();
    });

    //Load data from sessionStorage when the page is reloaded

    for (var i = 0; i < sessionStorage.length; i++) {
        var key = sessionStorage.key(i);
        var tables = ["region", "subregion", "countries", "states", "cities"];

        tables.forEach((table) => {
            if (key.startsWith(table + "_")) {
                var id = key.split("_")[1];
                var savedData = JSON.parse(sessionStorage.getItem(key));
                var row = $(`tr[data-id=${id}]`);

                // Populate the inputs with saved data
                $.each(savedData, function (name, value) {
                    row.find('input[name="' + name + '"]').val(value);
                });
            }
        });
    }

    // $(".edit-btn").each(function () {
    //     var regionId = $(this).data("id");
    //     var savedData = JSON.parse(
    //         sessionStorage.getItem("region_" + regionId)
    //     );

    //     if (savedData) {
    //         var row = $(this).closest("tr");

    //         // Populate the inputs with saved data
    //         $.each(savedData, function (name, value) {
    //             row.find('input[name="' + name + '"]').val(value);
    //         });
    //     }
    // });
});
