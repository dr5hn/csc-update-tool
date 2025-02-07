<table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md" id="table">
    <thead class="bg-gray-50">
        <tr>
            @foreach ($subregionHeaders as $header)
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 bg-gray-100 uppercase tracking-wider">{{ $header }}</th>
            @endforeach
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 bg-gray-100 uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200" id="table-body">
        @foreach ($subregionData as $subregion)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->id }}
                <input type="hidden" name="id" value="{{ $subregion->id }}">
            </td>
            <td class="px-6 py-4 whitespace-nowrap"><input type="text" name="name" value="{{ $subregion->name }}" disabled></td>
            <td class="px-6 py-4 whitespace-nowrap"><input type="text" name="translations" value="{{ $subregion->translations }}" disabled></td>
            <td class="px-6 py-4 whitespace-nowrap"><input type="text" name="region_id" value="{{ $subregion->region_id }}" disabled></td>
            <td class="px-6 py-4 whitespace-nowrap"><input type="text" name="wikiDataId" value="{{ $subregion->wikiDataId }}" disabled></td>
            @include('change-requests.partials.action-button')
        </tr>
        @endforeach
    </tbody>
</table>