import $ from 'jquery';


$(function() {
    $('#subregions-table').hide();
    $('#countries-table').hide();
    $('#states-table').hide();
    $('#cities-table').hide();
    
    function switchTab(tabId, tableId) {
        $('#regions-table, #subregions-table, #countries-table, #states-table, #cities-table').hide();

        $(tableId).show();
        $('.active-tab').removeClass('active-tab');
        $('#' + tabId).addClass('active-tab');
    }

    $('#table-tabs button').on('click', function() {
        switchTab($(this).attr('id'), $(this).attr('data-table'));
    });

    // Function to filter the table rows based on the search input
    $('#search-input').on('input', function () {
        const searchText = $(this).val().toLowerCase();
        $('#table-body tr').each(function () {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(searchText) !== -1);
        });
    });
});