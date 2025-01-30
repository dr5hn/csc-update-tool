import $ from 'jquery';


$(function() {
    $('#subregions-table').hide();
    $('#countries-table').hide();
    $('#states-table').hide();
    $('#cities-table').hide();

    $('#regions-tab').on('click', function() {
        $('#regions-table').show();
        $('#subregions-table').hide();
        $('#countries-table').hide();
        $('#states-table').hide();
        $('#cities-table').hide();
        $('.active-tab').removeClass('active-tab');
        $('#regions-tab').addClass('active-tab');
    });

    $('#subregions-tab').on('click', function() {
        $('#regions-table').hide();
        $('#subregions-table').show();
        $('#countries-table').hide();
        $('#states-table').hide();
        $('#cities-table').hide();
        $('.active-tab').removeClass('active-tab');
        $('#subregions-tab').addClass('active-tab');
    });

    $('#countries-tab').on('click', function() {
        $('#regions-table').hide();
        $('#subregions-table').hide();
        $('#countries-table').show();
        $('#states-table').hide();
        $('#cities-table').hide();
        $('.active-tab').removeClass('active-tab');
        $('#countries-tab').addClass('active-tab');
    });

    $('#states-tab').on('click', function() {
        $('#regions-table').hide();
        $('#subregions-table').hide();
        $('#countries-table').hide();
        $('#states-table').show();
        $('#cities-table').hide();
        $('.active-tab').removeClass('active-tab');
        $('#states-tab').addClass('active-tab');
    });
    $('#cities-tab').on('click', function() {
        $('#regions-table').hide();
        $('#subregions-table').hide();
        $('#countries-table').hide();
        $('#states-table').hide();
        $('#cities-table').show();
        $('.active-tab').removeClass('active-tab');
        $('#cities-tab').addClass('active-tab');
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