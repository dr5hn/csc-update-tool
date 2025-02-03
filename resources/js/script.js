import $ from "jquery";

$(function () {
    // hide all tables and dropdowns initially
    $("#subregions-table").hide();
    $("#countries-table").hide();
    $("#states-table").hide();
    $("#cities-table").hide();
    $("#countries-dropdown").hide();
    $("#states-dropdown").hide();

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
});
