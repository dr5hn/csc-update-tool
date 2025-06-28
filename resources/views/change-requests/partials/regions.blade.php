<!-- Desktop Table View -->
<div class="responsive-table-container">
    <table class="responsive-table table-compact" id="table">
        <thead>
            <tr>
                @foreach ($regionHeaders as $header)
                <th>{{ $header }}</th>
                @endforeach
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach ($regionData as $region)
            <tr data-id="region_{{ $region->id }}">
                <td class="text-center">
                    {{ $region->id }}
                    <input type="hidden" name="id" value="{{ $region->id }}">
                </td>
                <td>
                    <input type="text" name="name" value="{{ $region->name }}" class="table-input" disabled>
                </td>
                <td>
                    <input type="text" name="translations"
                           value="{{ is_array($region->translations) ? json_encode($region->translations) : $region->translations }}"
                           class="table-input" disabled>
                </td>
                <td>
                    <input type="text" name="wikiDataId" value="{{ $region->wikiDataId ?? '' }}" class="table-input" disabled>
                </td>
                @include('change-requests.partials.action-button')
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Mobile Card View -->
<div class="md:hidden">
    @foreach ($regionData as $region)
    <div class="mobile-table-card" data-id="region_{{ $region->id }}">
        <div class="table-row">
            <div class="table-cell">
                <span class="cell-label">ID</span>
                <span class="cell-value">
                    {{ $region->id }}
                    <input type="hidden" name="id" value="{{ $region->id }}">
                </span>
            </div>
            <div class="table-cell">
                <span class="cell-label">Name</span>
                <div class="cell-value">
                    <input type="text" name="name" value="{{ $region->name }}" class="table-input" disabled>
                </div>
            </div>
            <div class="table-cell">
                <span class="cell-label">Translations</span>
                <div class="cell-value">
                    <input type="text" name="translations"
                           value="{{ is_array($region->translations) ? json_encode($region->translations) : $region->translations }}"
                           class="table-input" disabled>
                </div>
            </div>
            <div class="table-cell">
                <span class="cell-label">Wiki Data ID</span>
                <div class="cell-value">
                    <input type="text" name="wikiDataId" value="{{ $region->wikiDataId ?? '' }}" class="table-input" disabled>
                </div>
            </div>
            <div class="table-cell">
                <span class="cell-label">Actions</span>
                <div class="cell-value">
                    <div class="action-buttons">
                        <button class="action-btn action-btn-edit edit-btn">Edit</button>
                        <button class="action-btn action-btn-save save-btn hidden">Save</button>
                        <button class="action-btn action-btn-delete delete-btn">Delete</button>
                        <button class="action-btn action-btn-undo undo-btn hidden">Undo</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
