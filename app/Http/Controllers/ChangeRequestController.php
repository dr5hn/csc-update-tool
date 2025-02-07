<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Region;
use App\Models\Subregion;
use phpDocumentor\Reflection\Types\Nullable;
use Illuminate\Support\Facades\Auth;
use App\Models\ChangeRequest;


use function Pest\Laravel\json;

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

    public function getRegions(Request $request)
    {
        $regionHeaders = Region::getTableHeaders();
        $searchText = $request->input('search');
        if ($searchText) {
            $regions = Region::where('name', 'like', '%' . $searchText . '%')->get();
        }
        return view('change-requests.partials.regions', ['regionData' => $regions, 'regionHeaders' => $regionHeaders]);
    }

    public function getSubregions(Request $request)
    {
        $searchText = $request->input('search');
        $subregionHeaders = Subregion::getTableHeaders();
        if ($searchText) {
            $subregions = Subregion::where('name', 'like', '%' . $searchText . '%')->get();
        }
        return view('change-requests.partials.subregions', ['subregionData' => $subregions, 'subregionHeaders' => $subregionHeaders]);
    }

    public function getCountries(Request $request)
    {
        $countries = Country::all();
        $countryHeaders = Country::getTableHeaders();
        $searchText = $request->input('search');
        if ($searchText) {
            $countries = Country::where('name', 'like', '%' . $searchText . '%')->get();
        }
        return view('change-requests.partials.countries', ['countryData' => $countries, 'countryHeaders' => $countryHeaders]);
    }

    public function getStates(Request $request)
    {
        $stateHeaders = State::getTableHeaders();
        $country_id = $request->input('country_id');
        $searchText = $request->input('search');
        if ($country_id !== 'null' && $searchText){
            $states = State::with('country')->where('country_id', $country_id)->where('name', 'like', '%' . $searchText . '%')->get();
        }else if ($country_id !== 'null') {
            $states = State::with('country')->where('country_id', $country_id)->get();
        } else if ($searchText) {
            $states = State::with('country')->where('name', 'like', '%' . $searchText . '%')->get();
        } else {
            $states = State::with('country')->get();
        }
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
        $cityHeaders = City::getTableHeaders();
        $country_id = $request->input('country_id');
        $searchText = $request->input('search');
        if ($country_id !== 'null' && $searchText){
            $cities = City::with('state')->where('country_id', $country_id)->where('name', 'like', '%' . $searchText . '%')->get();
        }else if ($country_id !== 'null') {
            $cities = City::with('state')->where('country_id', $country_id)->get();
        } else if ($searchText) {
            $cities = City::with('state')->where('name', 'like', '%' . $searchText . '%')->get();
        } else {
            $cities = City::with('state')->get();
        }
        return view('change-requests.partials.cities', ['cities' => $cities, 'cityHeaders' => $cityHeaders]);
    }

    public function getCitiesByState(Request $request)
    {
        $cityHeaders = City::getTableHeaders();
        $state_id = $request->input('state_id');
        $searchText = $request->input('search');
        if ($state_id !== 'null' && $searchText){
            $cities = City::with('state')->where('state_id', $state_id)->where('name', 'like', '%' . $searchText . '%')->get();
        }else if ($state_id !== 'null') {
            $cities = City::with('state')->where('state_id', $state_id)->get();
        } else if ($searchText) {
            $cities = City::with('state')->where('name', 'like', '%' . $searchText . '%')->get();
        } else {
            $cities = City::with('state')->get();
        }
        return view('change-requests.partials.cities', ['cities' => $cities, 'cityHeaders' => $cityHeaders]);
    }

    public function changeRequestSave(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'new_data' => 'required|json'
            ]);

            // Create new change request
            $changeRequest = new ChangeRequest();
            $changeRequest->user_id = Auth::id();
            $changeRequest->title = $validated['title'];
            $changeRequest->description = $validated['description'];
            $changeRequest->new_data = $validated['new_data'];
            $changeRequest->status = 'pending';
            $changeRequest->save();

            return redirect('dashboard');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving change request: ' . $e->getMessage()
            ], 500);
        }
    }
}
