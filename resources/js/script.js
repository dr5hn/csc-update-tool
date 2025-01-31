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
        if (!$(this).is('#states-tab') && !$(this).is('#cities-tab')) {
            $('#countries-dropdown').hide();  // Hide the dropdown
            $('#states-dropdown').hide();
        }
    });

    // Function to filter the table rows based on the search input
    $('#search-input').on('input', function () {
        const searchText = $(this).val().toLowerCase();
        $('#table-body tr').each(function () {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(searchText) !== -1);
            $('#countries-dropdown').hide();
            $('#states-dropdown').hide();
        });
    });
    // Hide the dropdown initially
    $('#countries-dropdown').hide();
    $('#states-dropdown').hide();
    
    // When the "States" tab is clicked, show the dropdown
    $('#states-tab').on('click', function() {
        $('#countries-dropdown').show();  // Show the dropdown
        $('#states-dropdown').hide();
    });

    // When the "Cities" tab is clicked, show the dropdown
    $('#cities-tab').on('click', function() {
        $('#states-dropdown').show();  // Show the dropdown
        $('#countries-dropdown').show();
    });
    
});

