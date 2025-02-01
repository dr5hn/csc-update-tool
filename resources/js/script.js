import $ from "jquery";

$(function () {
    // hide all tables and dropdowns initially
    $("#subregions-table").hide();
    $("#countries-table").hide();
    $("#states-table").hide();
    $("#cities-table").hide();
    $("#countries-dropdown").hide();
    $("#states-dropdown").hide();
    $("#cities-countries-dropdown").hide();

    function switchTab(tabId, tableId) {
        $(
            "#regions-table, #subregions-table, #countries-table, #states-table, #cities-table"
        ).hide();

        $(tableId).show();
        $(".active-tab").removeClass("active-tab");
        $("#" + tabId).addClass("active-tab");
    }

    $("#table-tabs button").on("click", function () {
        switchTab($(this).attr("id"), $(this).attr("data-table"));
        if (!$(this).is("#states-tab") && !$(this).is("#cities-tab")) {
            $("#countries-dropdown").hide(); // Hide the dropdown
            $("#states-dropdown").hide();
        }
    });

    // Function to filter the table rows based on the search input
    $("#search-input").on("input", function () {
        const searchText = $(this).val().toLowerCase();
        const country_id = $("#countries-select").val();
        $("#states-table").load(`/states?country_id=${country_id}&search=${searchText}`);
    });

    // When the "States" tab is clicked, show the dropdown
    $("#states-tab").on("click", () => {
        $("#countries-select").prop("selectedIndex", 0);
        $("#countries-dropdown").show();
        $("#cities-countries-dropdown, #states-dropdown").hide();
        $("#countries-select").on("change", function () {
            const searchText = $("#search-input").val().toLowerCase();
            $("#states-table")
                .show()
                .load(`/states?country_id=${$(this).val()}&search=${searchText}`);
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
            $.get(`/states-dropdown?country_id=${country_id}`, (data) => {
                $("#states-dropdown").html(data);
                $("#states-select").on("change", function () {
                    $("#cities-table").load(
                        `/cities-by-state?state_id=${$(this).val()}`
                    );
                });
            });
            $("#cities-table").load(
                `/cities-by-country?country_id=${country_id}`
            );
        });
    });
});
