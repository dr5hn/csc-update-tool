<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Country;
class ChangeRequestController extends Controller
{
    public function changeRequest()
    {
        return view('change-requests.new');
    }


}
