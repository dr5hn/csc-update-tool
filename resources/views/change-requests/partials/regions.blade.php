<table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md" id="table">
    <thead class="bg-gray-100">
        <tr>
            @foreach ($regionHeaders as $header)
            <th class="px-1 py-1 text-xs font-medium text-gray-500 ">{{ $header }}</th>
            @endforeach
            <th class="px-1 py-1 text-xs font-medium text-gray-500">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-x divide-y divide-gray-200" id="table-body">
        @foreach ($regionData as $region)
        <tr class=""  data-id="{{ $region->id }}">
            <td class="text-xs text-center">{{ $region->id }}
                <input type="hidden" name="id" value="{{ $region->id }}">
            </td>
            <td class="px-1 py-1"><input disabled type="text" name="name" value="{{ $region->name }}"></td>
            <td class=""><input type="text" name="translations" value="{{ $region->translations }}" disabled></td>
            <td class=""><input type="text" name="wikiDataId" value="{{ $region->wikiDataId }}" disabled></td>
            @include('change-requests.partials.action-button')
        </tr>
        @endforeach
    </tbody>
</table>