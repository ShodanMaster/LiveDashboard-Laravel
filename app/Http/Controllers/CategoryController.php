<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(){
        return view('category');
    }

    public function getCategories(){
        $categories = Category::all();

        if($categories->isEmpty()){
            return response()->json([
                'status' => false,
                'message' => 'No categories found',
                'categories' => []
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'categories' => $categories
        ], 200);
    }

    public function show(Category $category){
        if(!$category){
            return response()->json([
                'status' => false,
                'message' => 'category not found',
                'categories' => []
            ], 404);
        }

        Cache::flush();

        return response()->json([
            'status' => true,
            'message' => 'Category retrieved successfully',
            'category' => $category
        ], 200);
    }

    public function store(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'categories' => $validator->errors()
            ], 422);
        }

        $categories = Category::create([
            'name' => $request->name,
        ]);

        Cache::flush();
        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'categories' => $categories
        ], 201);
    }

    public function update(Request $request, Category $category){

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'categories' => $validator->errors()
            ], 422);
        }

        if (!$category->update($request->all())) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update category',
                'categories' => []
            ], 500);
        }

        Cache::flush();
        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'categories' => $category
        ], 200);
    }

    public function destroy(Category $category){
        if(!$category){
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
                'categories' => []
            ], 404);
        }

        if($category->delete()){

            Cache::flush();
            return response()->json([
                'status' => true,
                'message' => 'Category deleted successfully',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Failed to delete category',
        ], 500);

    }


}
