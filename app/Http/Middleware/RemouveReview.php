<?php

namespace App\Http\Middleware;

use App\Models\ServiceReview;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemouveReview
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $reviewId = $request->route('reviewId');
        $review = ServiceReview::findOrFail($reviewId);
        if ($request->userId != $review->user_id) {
            return response()->json([
                'status' => false,
                'message' => 'you are not able to do this operation',
                419
            ]);
        }
        return $next($request);
    }
}
