<table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md" id="table">
    <thead class="bg-gray-100">
        <tr>
            @foreach ($cityHeaders as $header)
            <th class="px-1 py-1 text-xs font-medium text-gray-500">{{ $header }}</th>
            @endforeach
            <th class="px-1 py-1 text-xs font-medium text-gray-500">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-x divide-y divide-gray-200" id="table-body">
        @foreach ($cityData as $city)
        <tr class="" data-id="city_{{ $city->id }}">
            <td class="text-xs text-center">{{ $city->id }} <input type="hidden" name="id" value="{{ $city->id }}"></td>
            <td class="px-1 py-1"><input type="text" name="name" value="{{ $city->name }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="state_id" value="{{ $city->state_id }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="state_code" value="{{ $city->state_code }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="country_id" value="{{ $city->country_id }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="country_code" value="{{ $city->country_code }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="latitude" value="{{ $city->latitude }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="longitude" value="{{ $city->longitude }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="wikiDataId" value="{{ $city->wikiDataId }}" disabled></td>
            @include('change-requests.partials.action-button')
        </tr>
        @endforeach
    </tbody>
</table>
