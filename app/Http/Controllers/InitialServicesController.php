<?php

namespace App\Http\Controllers;

use App\Models\InitialServices;
use Illuminate\Http\Request;

class InitialServicesController extends Controller
{
    public function index($globalserviceId = null){
        $services = InitialServices::find((int) $globalserviceId);
        return response()->json([$services]);
    }
}
