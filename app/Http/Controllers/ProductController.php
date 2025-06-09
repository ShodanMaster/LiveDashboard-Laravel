<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(){
        return view('product');
    }

    public function getProducts(){
        $products = Product::all();

        if($products->isEmpty()){
            return response()->json([
                'status' => false,
                'message' => 'No products found',
                'products' => []
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Products retrieved successfully',
            'products' => $products->map(function($p){
                return [
                    'id' => $p->id,
                    'category_id' => $p->category->id,
                    'category' => $p->category->name,
                    'name' => $p->name,
                ];
            })
        ], 200);
    }

    public function show(Product $product){
        if(!$product){
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'products' => []
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product retrieved successfully',
            'products' => $product
        ], 200);
    }

    public function store(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'products' => $validator->errors()
            ], 422);
        }

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'products' => $product
        ], 201);
    }

    public function update(Request $request, Product $product){

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'products' => $validator->errors()
            ], 422);
        }

        if (!$product->update($request->all())) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update product',
                'products' => []
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'products' => $product
        ], 200);
    }

    public function destroy(Product $product){
        if(!$product){
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'products' => []
            ], 404);
        }

        if($product->delete()){

            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Failed to delete product',
        ], 500);


    }


}
