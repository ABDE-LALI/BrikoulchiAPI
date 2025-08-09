<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceReview;
use App\Models\User;
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
        $reviews = ServiceReview::with('user')->with('likedByUsers')->where('service_id', $id)->get();
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
    public function RemouveReview($reviewId){
        $review = ServiceReview::findOrFail($reviewId);
        $review->delete();
        return response()->json([
                'message' => 'Your review has been remouved successfully.',
                'review'  => $review
            ]);
    }
    public function ReactWithLike(Request $request, $reviewId)
    {
        // Validate the request
        $request->validate([
            'like' => 'required|boolean',
            'userId' => 'required|integer'
        ]);
        // Get authenticated user (more secure than getting user from request)
        $user = User::findOrFail($request->userId);
        
        // Find the review or fail
        $review = ServiceReview::findOrFail($reviewId);
        
        try {
            if ($request->like) {
                // Check if already liked to prevent duplicates
                if (!$user->likedReviews()->where('review_id', $reviewId)->exists()) {
                    // return response('test');
                    $user->likedReviews()->attach($reviewId);

                    // Optional: You might want to increment a like_count on the review
                    $review->increment('like_count');

                    return response()->json([
                        'success' => true,
                        'message' => 'You have liked the review',
                        'like_count' => $review->fresh()->like_count // Return updated count
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'You already liked this review',
                    'like_count' => $review->like_count
                ], 400);
            } else {
                // Check if actually liked before removing
                if ($user->likedReviews()->where('review_id', $reviewId)->exists()) {
                    $user->likedReviews()->detach($reviewId);

                    // Optional: Decrement like_count
                    $review->decrement('like_count');

                    return response()->json([
                        'success' => true,
                        'message' => 'You have removed your like from the review',
                        'like_count' => $review->fresh()->like_count
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'You had not liked this fdsf review',
                    'like_count' => $review->like_count
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
