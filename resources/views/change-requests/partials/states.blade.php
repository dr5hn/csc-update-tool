<table class="min-w-full divide-y divide-gray-200" id="table">
    <thead class="bg-gray-50">
        <tr>
            @foreach ($stateHeaders as $header)
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200" id="table-body">
        @foreach ($states as $state)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{$state->country_id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->country_code }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->fips_code }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->iso2 }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->type }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->latitude }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->longitude }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->created_at }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->updated_at }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->flag }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $state->wikiDataId }}</td>
        </tr>
        @endforeach
    </tbody>
</table>