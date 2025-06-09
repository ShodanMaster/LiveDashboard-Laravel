<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(){
        return view('category');
    }

    public function getCategories(Request $request){

        $categories = Category::select('id', 'name');
        if($request->ajax()){
            return DataTables::of($categories)
            ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '<a href="javascript:void(0)" class="btn btn-info btn-sm editButton" data-id='. encrypt($row->id).' data-name="' . $row->name .'"  data-bs-toggle="modal" data-bs-target="#createModal">Edit</a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm deleteButton" data-id="'. encrypt($row->id) .'" data-name="'. $row->name .'">Delete</a>
                    ';
                })
                // ->rowColumn(['action'])
                ->make(true);
        }
    }

    public function store(Request $request){
        // dd($request->all());

        $request->validate([
            'name' => 'required|string',
        ]);


        if ($request->has('nameId') && $request->nameId !== null) {
            $category = Category::find(decrypt($request->nameId));

            if($category){
                $category->update([
                    'name' => $request->name,
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Category Updated Successfully!',
                ], 200);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'Category Not Found!',
                ], 404);
            }
        }

        Category::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Category Saved Successfully',
        ], 200);
    }

    public function delete(Request $request){
        // dd($request->all());

        $category = Category::find(decrypt($request->id));

        if($category){
            $category->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Category Deleted Successfully!',
            ], 200);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Category Not Found!',
            ], 404);
        }
    }
}
