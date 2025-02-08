<table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md" id="table">
    <thead class="bg-gray-100">
        <tr>
            @foreach ($subregionHeaders as $header)
            <th class="px-1 py-1 text-xs font-medium text-gray-500">{{ $header }}</th>
            @endforeach
            <th class="px-1 py-1 text-xs font-medium text-gray-500">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-x divide-y divide-gray-200" id="table-body">
        @foreach ($subregionData as $subregion)
        <tr class="" data-id="subregion_{{ $subregion->id }}">
            <td class="text-xs text-center">{{ $subregion->id }}
                <input type="hidden" name="id" value="{{ $subregion->id }}">
            </td>
            <td class="px-1 py-1">
                <input type="text" name="name" value="{{ $subregion->name }}" disabled>
            </td>
            <td class="px-1 py-1">
                <input type="text" name="translations"
                       value="{{ is_array($subregion->translations) ? json_encode($subregion->translations) : $subregion->translations }}"
                       disabled>
            </td>
            <td class="px-1 py-1">
                <input type="text" name="region_id" value="{{ $subregion->region_id }}" disabled>
            </td>
            <td class="px-1 py-1">
                <input type="text" name="wikiDataId" value="{{ $subregion->wikiDataId ?? '' }}" disabled>
            </td>
            @include('change-requests.partials.action-button')
        </tr>
        @endforeach
    </tbody>
</table>
