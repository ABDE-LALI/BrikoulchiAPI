<?php

namespace App\Http\Controllers;

use App\Models\InitialServices;
use Illuminate\Http\Request;

class InitialServicesController extends Controller
{
    public function getInitialServices($globalserviceId = null)
    {
        $services = InitialServices::where('global_service_id', (int) $globalserviceId)->get();
        return response()->json($services);
    }
}
