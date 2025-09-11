<?php

namespace App\Http\Middleware;

use App\Models\Service;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemouveService
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $serviceId = $request->route('Id');
        $service = Service::findOrFail($serviceId);
        if ($request->userId ==! $service->user_id)
        {
            return response()->json([
                'status' => false,
                'message' => 'you are not able to do this operation',
                419
            ]);
        }
        return $next($request);
    }
}
