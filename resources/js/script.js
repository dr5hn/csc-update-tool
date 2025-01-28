$(document).ready(function () {

    // Function to add a new row with input fields in the table
    $('#add-row-btn').click(function () {
        var newRow = `
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    <input type="text" class="w-full p-2 border rounded-md" placeholder="Country Name">
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    <input type="text" class="w-full p-2 border rounded-md" placeholder="ISO2">
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    <input type="text" class="w-full p-2 border rounded-md" placeholder="ISO3">
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    <input type="text" class="w-full p-2 border rounded-md" placeholder="Phone Code">
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    <input type="text" class="w-full p-2 border rounded-md" placeholder="Currency">
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                    <input type="text" class="w-full p-2 border rounded-md" placeholder="Region">
                </td>
                <td class="px-6 py-4 text-right">
                    <button type="button" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 save-btn">Save</button>
                    <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-800 delete-btn mt-2">Delete</button>
                </td>
            </tr>
        `;
        $('#countries-table-body').append(newRow);
    });

    // Function to delete a row
    $(document).on('click', '.delete-btn', function () {
        $(this).closest('tr').remove();
    });

    // Function to save the edited row
    $(document).on('click', '.save-btn', function () {
        var $row = $(this).closest('tr');
        $row.find('td').each(function (index) {
            if (index < 6) { // Only modify the first 6 cells (input fields)
                var inputValue = $(this).find('input').val();
                $(this).text(inputValue);  // Replace input field with value
            }
        });

        // Change the "Save" button to "Edit"
        $(this).text('Edit').removeClass('save-btn').addClass('edit-btn');

        // Enable the "Delete" button after saving
        $row.find('.delete-btn').prop('disabled', false);
    });

    // Function to edit the row (revert it back to input fields)
    $(document).on('click', '.edit-btn', function () {
        var $row = $(this).closest('tr');
        $row.find('td').each(function (index) {
            if (index < 6) { // Only modify the first 6 cells (input fields)
                var currentValue = $(this).text();
                $(this).html(`<input type="text" class="w-full p-2 border rounded-md" value="${currentValue}">`);
            }
        });

        // Change the "Edit" button to "Save"
        $(this).text('Save').removeClass('edit-btn').addClass('save-btn');

        // Disable delete button while editing
        $row.find('.delete-btn').prop('disabled', true);
    });

    // Function to filter the table rows based on the search input
    $('#search-input').on('input', function () {
        var searchText = $(this).val().toLowerCase();
        $('#countries-table-body tr').each(function () {
            var rowText = $(this).text().toLowerCase();
            if (rowText.indexOf(searchText) !== -1) {
                $(this).show(); // Show row if it matches
            } else {
                $(this).hide(); // Hide row if it doesn't match
            }
        });
    });
});
