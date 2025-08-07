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
            // Validate request
            $validated = $request->validate([
                'service_id' => 'required|integer|exists:services,id',
                'user_id'    => 'required|integer|exists:users,id',
                'rating'     => 'required|numeric|min:1|max:5',
                'text'       => 'required|string|max:1000',
            ]);

            // Create review
            $review = ServiceReview::create($validated);

            return response()->json([
                'message' => 'Your review has been submitted successfully.',
                'review'  => $review
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation error
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $th) {
            // General server error
            return response()->json([
                'status'  => false,
                'message' => 'Server error',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
}
