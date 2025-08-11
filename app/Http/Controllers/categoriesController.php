<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class categoriesController extends Controller
{
    public function index(bool $withServices = false)
    {
        try {
            $categories = Category::when($withServices, function ($query) {
                $query->with('services'); // Optional: Add nested relations here
            })->get();

            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database error while fatching the categories'], 500);
        }
    }
}
