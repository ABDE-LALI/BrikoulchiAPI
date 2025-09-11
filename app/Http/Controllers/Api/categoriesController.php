<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class categoriesController extends Controller
{
    public function index(bool $withGlobalServices = false)
    {
        try {
            $categories = Category::when($withGlobalServices, function ($query) {
                $query->with('globalservices'); // Optional: Add nested relations here
            })->get();

            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error while fatching the categories'], 500);
        }
    }
}
