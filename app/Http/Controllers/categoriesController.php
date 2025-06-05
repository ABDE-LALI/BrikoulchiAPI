<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class categoriesController extends Controller
{
    function index(){
        $categories = Category::all();
        return $categories;
    }
}
