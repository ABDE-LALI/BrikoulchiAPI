<?php

namespace App\Http\Controllers\Api;

use App\Models\InitialServices;
use App\Models\Service;
use App\Models\ServiceReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;


class servicesController extends Controller
{
    public function index($userId = null)
    {
        $services = Service::with(['category', 'user', 'reviews'])
            ->when($userId, function ($query, $userId) {
                return $query->where('user_id', (int) $userId);
            })->get();

        return response()->json($services);
    }

    public function createService(Request $request)
    {

        try {
            $validateService = Validator::make(
                $request->all(),
                [
                    'title' => 'required|string|max:20',
                    'description' => 'required|string',
                    'workDays' => 'required|string',
                    'workHours' => 'required|string',
                    'status' => 'required|string',
                    'type' => 'required|string',
                    'category_id' => 'required|integer',
                    'global_service_id' => 'required|integer',
                    'initial_service_id' => 'required|integer',
                    'user_id' => 'required|integer',
                    'lat' => 'required|numeric',
                    'lng' => 'required|numeric',
                ]
            );

            if ($validateService->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateService->errors()
                ], 422); // 422 is more appropriate for validation errors
            }
            // return response($validateService->passes().'hahaha');

            $service = Service::create([
                'title' => $request->title,
                'description' => $request->description,
                'workDays' => $request->workDays,
                'workHours' => $request->workHours,
                'type' => $request->type,
                'status' => $request->status,
                'category_id' => $request->category_id,
                'global_service_id' => $request->global_service_id,
                'initial_service_id' => $request->initial_service_id,
                'user_id' => $request->user_id,
                'lat' => $request->lat,
                'lng' => $request->lng
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Service created successfully',
                'data' => [
                    'service' => $service
                ]
            ], 201); // 201 for resource creation
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function deleteService($id)
    {
        $service = Service::findOrFail($id);
        if ($service) {
            $service->delete();
            return response()->json(['message' => 'service deleted ']);
        }
        return response()->json(['message' => 'the service has\'t deleted ']);
    }
    public function editService(Request $request, $id)
    {
        $validatedServiceEdition = validator::make(
            $request->service,
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'workDays' => 'required|string',
                'workHours' => 'required|string',
                'status' => 'required|string',
                'type' => 'required|string',
                'category_id' => 'required|integer',
                'global_service_id' => 'required|integer',
                'initial_service_id' => 'required|integer',
                'user_id' => 'required|integer',
                'lat' => 'required|numeric',
                'lng' => 'required|numeric',
            ]
        );
        // return response()->json(['edition' => $request->service]);
        if ($validatedServiceEdition->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validatedServiceEdition->errors()
            ], 422);
        }
        $service = Service::findOrFail($id);
        // return response()->json(['service' => $service]);

        // $service->title = $request->service->title;
        // $service->description = $request->service->description;
        // $service->workDays = $request->service->workDays;
        // $service->workHours = $request->service->workHours;
        // $service->status = $request->service->status;
        // $service->type = $request->service->type;
        // $service->category_id = $request->service->category_id;
        // $service->global_service_id = $request->service->global_service_id;
        // $service->initial_service_id = $request->service->initial_service_id;
        // $service->user_id = $request->service->user_id;
        // $service->lat = $request->service->lat;
        // $service->lng = $request->service->lng;
        // $service->save();
        $service->update($request->service);
        return response()->json(['message' => 'the service has been edited']);
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
    public function RemouveReview($reviewId)
    {
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
