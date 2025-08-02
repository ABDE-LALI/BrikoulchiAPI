<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class servicesController extends Controller
{
    public function index()
    {
        $services = Service::with('category')->with('user')->with('reviews')->get();
        return response()->json($services);
    }
}
