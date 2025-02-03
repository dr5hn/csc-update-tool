<table class="min-w-full divide-y divide-gray-200" id="table">
                            <thead class="bg-gray-50">
                                <tr>
                                    @foreach ($countryHeaders as $header)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                                @foreach ($countryData as $country)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->iso3 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->numeric_code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->iso2 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->phonecode }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->capital }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->currency }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->currency_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->currency_symbol }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->tld }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->native }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->region }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->region_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->subregion }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->subregion_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->nationality }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->timezones }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->translations }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->latitude }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->longitude }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->emoji }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->emojiU }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->created_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->updated_at }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->flag }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $country->wikiDataId }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>