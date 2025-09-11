<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
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
