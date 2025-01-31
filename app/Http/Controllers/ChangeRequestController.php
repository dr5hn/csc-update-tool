<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Region;
use App\Models\Subregion;

class ChangeRequestController extends Controller
{
    public function changeRequest()
    {
        $regionHeaders = Region::getTableHeaders();
        $regionData = Region::getTableData();
        $subregionHeaders = Subregion::getTableHeaders();
        $subregionData = Subregion::getTableData();
        $countryHeaders = Country::getTableHeaders();
        $countryData = Country::getTableData();
        $stateHeaders = State::getTableHeaders();
        $stateData = State::getTableData();
        $stateDropdownData = State::getDropdownData();
        $cityHeaders = City::getTableHeaders();
        $cityData = City::getTableData();
        return view('change-requests.new', [
            'regionHeaders' => $regionHeaders,
            'regionData' => $regionData,
            'subregionHeaders' => $subregionHeaders,
            'subregionData' => $subregionData,
            'countryHeaders' => $countryHeaders,
            'countryData' => $countryData,
            'stateHeaders' => $stateHeaders,
            'stateData' => $stateData,
            'stateDropdownData' => $stateDropdownData,
            'cityHeaders' => $cityHeaders,
            'cityData' => $cityData,
        ]);
    }

    public function getCountries()
    {
        $countries = Country::all();
        return response()->json($countries);
    }

    public function getStates(Request $request)
    {
        $stateHeaders = State::getTableHeaders();
        $country_id = $request->input('country_id');
        $states = State::with('country')->where('country_id', $country_id)->get();
        return view('change-requests.partials.states', ['states' => $states, 'stateHeaders' => $stateHeaders]);
    }

    public function getStatesDropdown(Request $request)
    {
        $country_id = $request->input('country_id');
        $states = State::with('country')->where('country_id', $country_id)->get();
        return view('change-requests.partials.states-dropdown', ['states' => $states]);
    }

    public function getCitiesByCountry(Request $request)
    {
        $country_id = $request->input('country_id');
        $cities = City::with('state')->where('country_id', $country_id)->get();
        $cityHeaders = City::getTableHeaders();
        return view('change-requests.partials.cities', ['cities' => $cities, 'cityHeaders' => $cityHeaders]);
    }

    public function getCitiesByState(Request $request)
    {
        $cityHeaders = City::getTableHeaders();
        $state_id = $request->input('state_id');
        $cities = City::with('state')->where('state_id', $state_id)->get();
        return view('change-requests.partials.cities', ['cities' => $cities, 'cityHeaders' => $cityHeaders]);
    }

    public function getRegions()
    {
        $regions = Region::with('country', 'subregion')->get();
        return response()->json($regions);
    }

    public function getSubregions()
    {
        $subregions = Subregion::with('country', 'region.country')->get();
        return response()->json($subregions);
    }
}
