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
            'cityHeaders' => $cityHeaders,
            'cityData' => $cityData,
        ]);
    }

    public function getCountries()
    {
        $countries = Country::all();
        return response()->json($countries);
    }

    public function getStates()
    {
        $states = State::with('country')->get();
        return response()->json($states);
    }

    public function getCities()
    {
        $cities = City::with('state', 'state.country')->get();
        return response()->json($cities);
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
