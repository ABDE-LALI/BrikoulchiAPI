<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceReview;
use Illuminate\Http\Request;

class servicesController extends Controller
{
    public function index()
    {
        $services = Service::with('category')->with('user')->with('reviews')->get();
        return response()->json($services);
    }
    public function getReviews($id)
    {
        $reviews = ServiceReview::with('user')->where('service_id', $id)->get();
        return response()->json($reviews);
    }
}
