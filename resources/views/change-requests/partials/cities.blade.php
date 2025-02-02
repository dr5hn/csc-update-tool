<table class="min-w-full divide-y divide-gray-200" id="table">
    <thead class="bg-gray-50">
        <tr>
            @foreach ($cityHeaders as $header)
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200" id="table-body">
        @foreach ($cities as $city)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->state_id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->state_code }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->country_id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->country_code }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->latitude }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->longitude }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->created_at }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->updated_at }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->flag }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $city->wikiDataId }}</td>
        </tr>
        @endforeach
    </tbody>
</table>