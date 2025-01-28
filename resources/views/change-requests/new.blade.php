<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>change request</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Add jQuery CDN -->
</head>

<body>
    <div class="min-h-full w-full" id="artifacts-component-root-react">
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4">
                <!-- Add the action attribute with the URL where the form should be submitted -->
                <form class="bg-white rounded-lg shadow-lg p-6" action="" method="POST">
                    @csrf
                    <h1 class="text-2xl font-bold text-gray-900 mb-6" id="form-title">Database Changes Request - Countries</h1>

                    <!-- Search Bar -->
                    <div class="mb-6">
                        <input
                            type="text"
                            id="search-input"
                            class="w-full p-2 border rounded-md"
                            placeholder="Search..."
                        />


                    </div>

                    <div class="flex border-b border-gray-200 mb-6">
                        <button type="button" class="py-2 px-4 border-b-2 font-medium border-blue-500 text-blue-600" id="countries-tab">Countries</button>
                        <button type="button" class="py-2 px-4 border-b-2 font-medium border-transparent text-gray-500 hover:text-gray-700" id="states-tab">States</button>
                        <button type="button" class="py-2 px-4 border-b-2 font-medium border-transparent text-gray-500 hover:text-gray-700" id="cities-tab">Cities</button>
                        <button type="button" class="py-2 px-4 border-b-2 font-medium border-transparent text-gray-500 hover:text-gray-700" id="regions-tab">Regions</button>
                        <button type="button" class="py-2 px-4 border-b-2 font-medium border-transparent text-gray-500 hover:text-gray-700" id="subregions-tab">Subregions</button>
                    </div>

                    <!-- Add Row Button -->
                    <div class="flex justify-start mb-4">
                        <button type="button" id="add-row-btn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Add Row</button>
                    </div>

                    <div class="overflow-x-auto mt-8">
                        <table class="min-w-full divide-y divide-gray-200" id="table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" id="name-header">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" id="code-header">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" id="extra-header">Extra</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                <!-- Dynamic rows will be inserted here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Submit Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            // Table data for each tab
            const countryColumns = ['Country Name', 'ISO2', 'ISO3', 'Phone Code', 'Currency', 'Region'];
            const stateColumns = ['State Name', 'Abbreviation', 'Country', 'Region'];
            const cityColumns = ['City Name', 'State', 'Population', 'Area'];
            const regionColumns = ['Region Name', 'Country', 'Subregion'];
            const subregionColumns = ['Subregion Name', 'Region', 'Country'];

            // Function to switch table columns based on selected category
            function switchTableColumns(category) {
                let columns, title, headers;
                
                if (category === 'countries') {
                    columns = countryColumns;
                    title = 'Database Changes Request - Countries';
                    headers = ['Name', 'ISO2', 'ISO3', 'Phone Code', 'Currency', 'Region'];
                } else if (category === 'states') {
                    columns = stateColumns;
                    title = 'Database Changes Request - States';
                    headers = ['State Name', 'Abbreviation', 'Country', 'Region'];
                } else if (category === 'cities') {
                    columns = cityColumns;
                    title = 'Database Changes Request - Cities';
                    headers = ['City Name', 'State', 'Population', 'Area'];
                } else if (category === 'regions') {
                    columns = regionColumns;
                    title = 'Database Changes Request - Regions';
                    headers = ['Region Name', 'Country', 'Subregion'];
                } else if (category === 'subregions') {
                    columns = subregionColumns;
                    title = 'Database Changes Request - Subregions';
                    headers = ['Subregion Name', 'Region', 'Country'];
                }

                // Set the form title
                $('#form-title').text(title);

                // Set headers
                $('#table thead').html(`
                    <tr>
                        ${headers.map(header => `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">${header}</th>`).join('')}
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                `);

                // Adjust input fields for adding rows
                $('#add-row-btn').off('click').on('click', function () {
                    let newRow = `
                        <tr>
                            ${columns.map(col => `
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    <input type="text" class="w-full p-2 border rounded-md" placeholder="${col}">
                                </td>
                            `).join('')}
                            <td class="px-6 py-4 text-right">
                                <button type="button" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 save-btn">Save</button>
                                <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-800 delete-btn mt-2">Delete</button>
                            </td>
                        </tr>
                    `;
                    $('#table-body').append(newRow);
                });
            }

            // Set default to 'countries' tab
            switchTableColumns('countries');

            // Tab button click listeners
            $('#countries-tab').click(function () {
                switchTableColumns('countries');
                activateTab('countries');
            });

            $('#states-tab').click(function () {
                switchTableColumns('states');
                activateTab('states');
            });

            $('#cities-tab').click(function () {
                switchTableColumns('cities');
                activateTab('cities');
            });

            $('#regions-tab').click(function () {
                switchTableColumns('regions');
                activateTab('regions');
            });

            $('#subregions-tab').click(function () {
                switchTableColumns('subregions');
                activateTab('subregions');
            });

            // Function to highlight active tab
            function activateTab(category) {
                $('#countries-tab').removeClass('border-blue-500 text-blue-600').addClass('border-transparent text-gray-500');
                $('#states-tab').removeClass('border-blue-500 text-blue-600').addClass('border-transparent text-gray-500');
                $('#cities-tab').removeClass('border-blue-500 text-blue-600').addClass('border-transparent text-gray-500');
                $('#regions-tab').removeClass('border-blue-500 text-blue-600').addClass('border-transparent text-gray-500');
                $('#subregions-tab').removeClass('border-blue-500 text-blue-600').addClass('border-transparent text-gray-500');


                if (category === 'countries') {
                    $('#countries-tab').removeClass('border-transparent text-gray-500').addClass('border-blue-500 text-blue-600');
                } else if (category === 'states') {
                    $('#states-tab').removeClass('border-transparent text-gray-500').addClass('border-blue-500 text-blue-600');
                } else if (category === 'cities') {
                    $('#cities-tab').removeClass('border-transparent text-gray-500').addClass('border-blue-500 text-blue-600');
                } else if (category === 'regions') {
                    $('#regions-tab').removeClass('border-transparent text-gray-500').addClass('border-blue-500 text-blue-600');
                } else if (category === 'subregions') {
                    $('#subregions-tab').removeClass('border-transparent text-gray-500').addClass('border-blue-500 text-blue-600');
                }
            }

            // Function to delete a row
            $(document).on('click', '.delete-btn', function () {
                $(this).closest('tr').remove();
            });

            // Function to save the edited row
            $(document).on('click', '.save-btn', function () {
                var $row = $(this).closest('tr');
                $row.find('td').each(function (index) {
                    if (index < $row.find('td').length - 1) { // Exclude actions cell
                        var inputValue = $(this).find('input').val();
                        $(this).text(inputValue);  // Replace input field with value
                    }
                });

                // Change the "Save" button to "Edit"
                $(this).text('Edit').removeClass('save-btn').addClass('edit-btn');
            });

            // Function to edit the row (revert it back to input fields)
            $(document).on('click', '.edit-btn', function () {
                var $row = $(this).closest('tr');
                $row.find('td').each(function (index) {
                    if (index < $row.find('td').length - 1) { // Exclude actions cell
                        var currentValue = $(this).text();
                        $(this).html(`<input type="text" class="w-full p-2 border rounded-md" value="${currentValue}">`);
                    }
                });

                // Change the "Edit" button to "Save"
                $(this).text('Save').removeClass('edit-btn').addClass('save-btn');
            });

            // Function to filter the table rows based on the search input
            $('#search-input').on('input', function () {
                var searchText = $(this).val().toLowerCase();
                $('#table-body tr').each(function () {
                    var rowText = $(this).text().toLowerCase();
                    if (rowText.indexOf(searchText) !== -1) {
                        $(this).show(); // Show row if it matches
                    } else {
                        $(this).hide(); // Hide row if it doesn't match
                    }
                });
            });
        });
    </script>
</body>

</html>
