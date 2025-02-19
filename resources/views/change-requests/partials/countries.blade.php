<table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md" id="table">
    <thead class="bg-gray-100">
        <tr>
            @foreach ($countryHeaders as $header)
            <th class="px-1 py-1 text-xs font-medium text-gray-500">{{ $header }}</th>
            @endforeach
            <th class="px-1 py-1 text-xs font-medium text-gray-500">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-x divide-y divide-gray-200" id="table-body">
        @foreach ($countryData as $country)
        <tr class="" data-id="country_{{ $country->id }}">
            <td class="text-xs text-center">{{ $country->id }} <input type="hidden" name="id" value="{{ $country->id }}"></td>
            <td class="px-1 py-1"><input type="text" name="name" value="{{ $country->name }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="iso3" value="{{ $country->iso3 }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="numeric_code" value="{{ $country->numeric_code }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="iso2" value="{{ $country->iso2 }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="phonecode" value="{{ $country->phonecode }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="capital" value="{{ $country->capital }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="currency" value="{{ $country->currency }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="currency_name" value="{{ $country->currency_name }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="currency_symbol" value="{{ $country->currency_symbol }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="tld" value="{{ $country->tld }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="native" value="{{ $country->native }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="region" value="{{ $country->region }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="region_id" value="{{ $country->region_id }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="subregion" value="{{ $country->subregion }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="subregion_id" value="{{ $country->subregion_id }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="nationality" value="{{ $country->nationality }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="timezones" value="{{ is_array($country->timezones) ? json_encode($country->timezones) : $country->timezones }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="translations" value="{{ is_array($country->translations) ? json_encode($country->translations) : $country->translations }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="latitude" value="{{ $country->latitude }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="longitude" value="{{ $country->longitude }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="emoji" value="{{ $country->emoji }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="emojiU" value="{{ $country->emojiU }}" disabled></td>
            <td class="px-1 py-1"><input type="text" name="wikiDataId" value="{{ $country->wikiDataId }}" disabled></td>
            @include('change-requests.partials.action-button')
        </tr>
        @endforeach
    </tbody>
</table>
