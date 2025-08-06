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
    public function createReview(Request $request)
    {
        try {
            $validate = $request->validate([
                'service_id' => 'required|integer',
                'user_id' => 'required|integer',
                'rating' => 'required|integer',
                'text' => 'required|string',
                'like' => 'required|boolean'
            ]);

            $review = ServiceReview::create(
                [
                    'service_id' => $request->service_id,
                    'user_id' => $request->user_id,
                    'rating' => $request->rating,
                    'text' => $request->text,
                    'like' => $request->like
                ]
            );
            return response()->json(['message'=>'your review is submitted succesfully', $review]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server haha',
                'error' => '$th->getMessage()'
            ], 500);
        }
    }
}
