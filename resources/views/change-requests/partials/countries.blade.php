<table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md" id="table">
                            <thead class="bg-gray-50">
                                <tr>
                                    @foreach ($countryHeaders as $header)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 bg-gray-100 uppercase tracking-wider">{{ $header }}</th>
                                    @endforeach
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 bg-gray-100 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                @foreach ($countryData as $country)
                                <tr>
                                    <td class="px-1 py-1 whitespace-nowrap">{{ $country->id }}</td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="" value="{{ $country->name }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="iso3" value="{{ $country->iso3 }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="numeric_code" value="{{ $country->numeric_code }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="iso2" value="{{ $country->iso2 }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="phonecode" value="{{ $country->phonecode }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="capital" value="{{ $country->capital }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="currency" value="{{ $country->currency }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="currency_name" value="{{ $country->currency_name }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="currency_symbol" value="{{ $country->currency_symbol }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="tId" value="{{ $country->tld }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="native" value="{{ $country->native }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="region" value="{{ $country->region }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="region_id" value="{{ $country->region_id }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="subregion" value="{{ $country->subregion }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="subregion_id" value="{{ $country->subregion_id }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="nationality" value="{{ $country->nationality }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap">{{ $country->timezones }}</td>
                                    <td class="px-1 py-1 whitespace-nowrap">{{ $country->translations }}</td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="latitude" value="{{ $country->latitude }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="longitude" value="{{ $country->longitude }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="emoji" value="{{ $country->emoji }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="emojiU" value="{{ $country->emojiU }}" disabled></td>
                                    <td class="px-1 py-1 whitespace-nowrap"><input type="text" name="wikiDataId" value="{{ $country->wikiDataId }}" disabled></td>
                                    @include('change-requests.partials.action-button')
                                </tr>
                                @endforeach
                            </tbody>
                        </table>