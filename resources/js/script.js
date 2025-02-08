import $ from "jquery";

$(function () {
    // Cache DOM elements
    const $tables = {
        regions: $("#regions-table"),
        subregions: $("#subregions-table"),
        countries: $("#countries-table"),
        states: $("#states-table"),
        cities: $("#cities-table")
    };

    const $dropdowns = {
        countries: $("#countries-dropdown"),
        states: $("#states-dropdown")
    };

    const $searchInput = $("#search-input");
    const $tableTabs = $("#table-tabs button");
    const $formTitle = $("#form-title");
    const $actions = $("#actions");
    const $addRowBtn = $("#add-row-btn");
    const $submitBtn = $("#change-request-submit");

    // Initial state
    const initialState = {
        hiddenElements: [
            $tables.subregions,
            $tables.countries,
            $tables.states,
            $tables.cities,
            ...Object.values($dropdowns),
            $actions
        ],
        activeTable: $tables.regions,
        activeTab: "regions-tab"
    };

    // Hide non-region elements initially
    initialState.hiddenElements.forEach($el => $el.hide());

    // Show regions table and set active tab
    $tables.regions.show();
    $(`#${initialState.activeTab}`).addClass("active-tab");

    function switchTab(tabId, tableId) {
        Object.values($tables).forEach($table => $table.hide());
        $(tableId).show();

        $(".active-tab").removeClass("active-tab");
        $(`#${tabId}`).addClass("active-tab");

        $(".search-input").attr("id", `search-input-${tabId}`);

        const tabTitle = $(`#${tabId}`).text();
        $formTitle.text(`Database Changes Request - ${tabTitle}`);

        // Store original data for the newly loaded table and then load saved changes
        storeOriginalData();
        loadSavedData();
    }

    // Table tab click handler
    $tableTabs.on("click", function() {
        const $this = $(this);
        switchTab($this.attr("id"), $this.attr("data-table"));

        // Handle dropdowns visibility
        const isStatesTab = $this.is("#states-tab");
        const isCitiesTab = $this.is("#cities-tab");

        $dropdowns.countries.toggle(isStatesTab || isCitiesTab);
        $dropdowns.states.toggle(isCitiesTab);

        // Reset dropdowns
        $("#countries-select, #states-select").prop("selectedIndex", 0);

        if (isStatesTab) {
            $tables.states.show();
        } else if (isCitiesTab) {
            $tables.cities.show();
        }
    });

    // Search input handler
    $searchInput.on("input", function() {
        const searchText = $(this).val().toLowerCase();
        const activeTab = $(".active-tab").attr("id").replace("-tab", "");
        const params = new URLSearchParams({ search: searchText });

        switch(activeTab) {
            case "regions":
                $tables.regions.load(`/regions?${params}`, storeOriginalData);
                break;
            case "subregions":
                $tables.subregions.load(`/subregions?${params}`, storeOriginalData);
                break;
            case "countries":
                $tables.countries.load(`/countries?${params}`, storeOriginalData);
                break;
            case "states": {
                const countryId = $("#countries-select").val();
                if (countryId) {
                    params.append("country_id", countryId);
                }
                $tables.states.load(`/states?${params}`, storeOriginalData);
                break;
            }
            case "cities": {
                const countryId = $("#countries-select").val();
                const stateId = $("#states-select").val();

                if (stateId) {
                    $tables.cities.load(`/cities-by-state?state_id=${stateId}&${params}`, storeOriginalData);
                } else if (countryId) {
                    $tables.cities.load(`/cities-by-country?country_id=${countryId}&${params}`, storeOriginalData);
                }
                break;
            }
        }
    });

    // Country dropdown handler
    $("#countries-select").on("change", function() {
        const countryId = $(this).val();
        const searchText = $searchInput.val().toLowerCase();

        if ($("#states-tab").hasClass("active-tab")) {
            $tables.states.show().load(`/states?country_id=${countryId}&search=${searchText}`, function() {
                storeOriginalData();
                loadSavedData();
            });
        } else if ($("#cities-tab").hasClass("active-tab")) {
            $("#states-select").prop("selectedIndex", 0);

            // Update states dropdown
            $.get(`/states-dropdown?country_id=${countryId}`, (data) => {
                $("#states-dropdown").html(data);
                attachStateDropdownHandler();
            });

            // Load cities for country
            $tables.cities.show().load(`/cities-by-country?country_id=${countryId}&search=${searchText}`, function() {
                storeOriginalData();
                loadSavedData();
            });
        }
    });

    // Attach state dropdown handler
    function attachStateDropdownHandler() {
        $("#states-select").off("change").on("change", function() {
            if ($("#cities-tab").hasClass("active-tab")) {
                const stateId = $(this).val();
                const searchText = $searchInput.val().toLowerCase();
                $tables.cities.show().load(`/cities-by-state?state_id=${stateId}&search=${searchText}`, function() {
                    storeOriginalData();
                    loadSavedData();
                });
            }
        });
    }

    // Row management functions
    const RowManager = {
        store: sessionStorage,

        storeOriginal(row) {
            const rowData = {};
            $(row).find("input").each(function() {
                rowData[$(this).attr("name")] = $(this).val();
            });
            $(row).data("original", rowData);
        },

        hasChanged(row) {
            const originalData = $(row).data("original");
            if (!originalData) return false;

            return Array.from($(row).find("input")).some(input => {
                const $input = $(input);
                return originalData[$input.attr("name")] !== $input.val();
            });
        },

        isEmpty(row) {
            return Array.from($(row).find('input[type="text"]'))
                .every(input => $(input).val().trim() === "");
        },

        save(row) {
            const $row = $(row);
            const id = $row.data("id");
            const data = {};

            $row.find("input").each(function() {
                data[$(this).attr("name")] = $(this).val();
            });

            if (!id.startsWith("added-") && this.hasChanged($row)) {
                this.store.setItem(id, JSON.stringify(data));
                $row.addClass("changed-row");
            }

            if (id.startsWith("added-") && !this.isEmpty($row)) {
                this.store.setItem(id, JSON.stringify(data));
                $row.addClass("added-row");
            }
        },

        delete(row) {
            const $row = $(row);
            const id = $row.data("id");

            if (id.startsWith("added-")) {
                $row.remove();
                this.store.removeItem(id);
                return;
            }

            this.store.setItem(`deleted-${id}`, "true");
            $row.addClass("deleted-row")
                .find(".delete-btn, .edit-btn").hide()
                .end()
                .find(".undo-btn").show()
                .end()
                .find(".save-btn").addClass("hidden");
        },

        restore(row) {
            const $row = $(row);
            const id = $row.data("id");

            this.store.removeItem(`deleted-${id}`);
            $row.removeClass("deleted-row")
                .find(".undo-btn").hide()
                .end()
                .find(".delete-btn, .edit-btn").show();
        }
    };

    // Initialize row data
    function storeOriginalData() {
        $("table tr").each(function() {
            RowManager.storeOriginal(this);
        });
    }

    // Load saved data
    function loadSavedData() {
        const tables = ["region", "subregion", "country", "state", "city"];

        for (let i = 0; i < sessionStorage.length; i++) {
            const key = sessionStorage.key(i);
            const value = sessionStorage.getItem(key);

            tables.forEach(table => {
                if (key.startsWith(`${table}_`)) {
                    const $row = $(`tr[data-id="${key}"]`);
                    if ($row.length) {
                        $row.addClass("changed-row");
                        $row.find(".edit-btn").addClass("hidden");
                        $row.find(".save-btn").removeClass("hidden");
                        Object.entries(JSON.parse(value)).forEach(([name, value]) => {
                            $row.find(`input[name="${name}"]`).val(value);
                        });
                    }
                }

                if (key.startsWith("deleted-")) {
                    const id = key.split("-")[1];
                    const $row = $(`tr[data-id="${id}"]`);
                    if ($row.length) {
                        $row.addClass("deleted-row");
                        $row.find(".delete-btn, .edit-btn").hide();
                        $row.find(".undo-btn").show();
                        $row.find(".save-btn").addClass("hidden");
                    }
                }

                if (key.startsWith("added-")) {
                    const id = key;  // Use full key as the data-id
                    const $existingRow = $(`tr[data-id="${id}"]`);

                    if (!$existingRow.length && table === key.split("-")[1].split("_")[0]) {
                        const rowId = key.split("_")[1];
                        addNewRow(rowId);
                        const $newRow = $(`tr[data-id="${id}"]`);

                        if ($newRow.length) {
                            $newRow.addClass("added-row");
                            Object.entries(JSON.parse(value)).forEach(([name, value]) => {
                                $newRow.find(`input[name="${name}"]`).val(value);
                            });
                        }
                    }
                }
            });
        }
    }

    // Add new row functionality
    function addNewRow(newId = false) {
        const activeTable = $(".active-tab").attr("data-table");
        const $tableBody = $(activeTable).find("#table-body");
        let tableType = $(".active-tab").attr("id").replace("-tab", "").replace("s", "");
        if (tableType === "citie") {
            tableType = "city";
        }

        if (!newId) {
            let maxId = 0;
            $tableBody.find("tr").each(function() {
                const rowId = $(this).data("id");
                if (rowId) {
                    const id = parseInt(rowId.split("_")[1]);
                    maxId = Math.max(maxId, id);
                }
            });
            newId = maxId + 1;
        }

        const $newRow = $("<tr>")
            .addClass("added-row")
            .attr("data-id", `added-${tableType}_${newId}`);

        $newRow.append(`
            <td class="text-xs text-center">${newId}
                <input type="hidden" name="id" value="${newId}">
            </td>
        `);

        const $headers = $(activeTable).find("thead th");
        $headers.each(function(index) {
            if (index > 0 && index < $headers.length - 1) {
                $newRow.append(`
                    <td class="px-1 py-1">
                        <input type="text" name="${$(this).text().toLowerCase().replace(/\s+/g, "_")}"
                               value="" disabled>
                    </td>
                `);
            }
        });

        $newRow.append(`
            <td class="text-center">
                <button class="edit-btn text-blue-500 hover:text-blue-700">Edit</button>
                <button class="save-btn text-green-500 hover:text-green-700 hidden">Save</button>
                <button class="delete-btn text-red-500 hover:text-red-700">Delete</button>
                <button class="undo-btn text-gray-500 hover:text-gray-700 hidden">Undo</button>
            </td>
        `);

        $tableBody.append($newRow);
    }

    // Row action button handlers
    $("body").on("click", ".edit-btn, .save-btn, .delete-btn, .undo-btn", function() {
        const $btn = $(this);
        const $row = $btn.closest("tr");

        if ($btn.hasClass("edit-btn")) {
            const $inputs = $row.find("input");
            $inputs.prop("disabled", false);

            $inputs.on("input", () => {
                $row.toggleClass("changed-row", RowManager.hasChanged($row));
            });

            $btn.addClass("hidden");
            $row.find(".save-btn").removeClass("hidden");
        }
        else if ($btn.hasClass("save-btn")) {
            const $inputs = $row.find("input");
            $inputs.prop("disabled", true).off("input");

            if (!RowManager.hasChanged($row)) {
                $row.removeClass("changed-row");
            }

            RowManager.save($row);

            $btn.addClass("hidden");
            $row.find(".edit-btn").removeClass("hidden");
        }
        else if ($btn.hasClass("delete-btn")) {
            RowManager.delete($row);
        }
        else if ($btn.hasClass("undo-btn")) {
            RowManager.restore($row);
        }
    });

    // Add new row button handler
    $addRowBtn.on("click", () => addNewRow());

    // Form submission handler
    $submitBtn.on("click", function(e) {
        e.preventDefault();

        const title = $("#request_title").val();
        const description = $("#request_description").val();

        if (!title || !description) {
            alert("Please fill in both title and description");
            return;
        }

        const changes = {
            modifications: {},
            deletions: [],
            additions: {}
        };

        Array.from({ length: sessionStorage.length }, (_, i) => {
            const key = sessionStorage.key(i);
            const value = sessionStorage.getItem(key);

            if (key.startsWith("deleted-")) {
                changes.deletions.push(key.replace("deleted-", ""));
            }
            else if (key.startsWith("added-")) {
                changes.additions[key] = JSON.parse(value);
            }
            else {
                const [table] = key.split("_");
                if (["region", "subregion", "country", "state", "city"].includes(table)) {
                    changes.modifications[table] = changes.modifications[table] || {};
                    changes.modifications[table][key] = JSON.parse(value);
                }
            }
        });

        $("#new_data").val(JSON.stringify(changes));
        $("#change-request-form").trigger("submit");
    });

    // Initialize
    storeOriginalData();
    loadSavedData();
    attachStateDropdownHandler();
});
