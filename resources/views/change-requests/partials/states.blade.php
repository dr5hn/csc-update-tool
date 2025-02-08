<table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md" id="table">
    <thead class="bg-gray-100">
        <tr>
            @foreach ($stateHeaders as $header)
            <th class="px-1 py-1 text-xs font-medium text-gray-500">{{ $header }}</th>
            @endforeach
            <th class="px-1 py-1 text-xs font-medium text-gray-500">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-x divide-y divide-gray-200" id="table-body">
        @foreach ($stateData as $state)
        <tr class="" data-id="state_{{ $state->id }}">
            <td class="text-xs text-center">{{ $state->id }} <input type="hidden" name="id" value="{{ $state->id }}"></td>
            <td class="px-1 py-1"><input type="text" name="name" value="{{ $state->name }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="country_id" value="{{ $state->country_id }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="country_code" value="{{ $state->country_code }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="fips_code" value="{{ $state->fips_code }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="iso2" value="{{ $state->iso2 }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="type" value="{{ $state->type }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="latitude" value="{{ $state->latitude }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="longitude" value="{{ $state->longitude }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="wikiDataId" value="{{ $state->wikiDataId }}" disabled></td>
            @include('change-requests.partials.action-button')
        </tr>
        @endforeach
    </tbody>
</table>
