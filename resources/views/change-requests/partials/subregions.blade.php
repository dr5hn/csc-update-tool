<table class="min-w-full divide-y divide-gray-200" id="table">
    <thead class="bg-gray-50">
        <tr>
            @foreach ($subregionHeaders as $header)
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200" id="table-body">
        @foreach ($subregionData as $subregion)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->translations }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->region_id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->created_at }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->updated_at }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->flag }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $subregion->wikiDataId }}</td>
        </tr>
        @endforeach
    </tbody>
</table>