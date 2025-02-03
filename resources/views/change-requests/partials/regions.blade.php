<table class="min-w-full divide-y divide-gray-200" id="table">
    <thead class="bg-gray-50">
        <tr>
            @foreach ($regionHeaders as $header)
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200" id="table-body">
        @foreach ($regionData as $region)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $region->id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $region->name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $region->translations }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $region->created_at }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $region->updated_at }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $region->flag }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $region->wikiDataId }}</td>
        </tr>
        @endforeach
    </tbody>
</table>