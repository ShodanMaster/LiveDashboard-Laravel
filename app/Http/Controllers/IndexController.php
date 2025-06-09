<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    public function index(){
        $categoryCount = Category::count();
        $productCount = Product::count();
        return view('index', compact('categoryCount', 'productCount'));
    }

    public function admin(){
        return view('admin');
    }

    public function sseDashboard(){
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        if(Cache::has('category') && Cache::has('product')){
            $eventData = [
                'category' => Cache::get('category'),
                'product' => Cache::get('product'),
                'randomNumber' => Cache::get('randomNumber'),
            ];

            echo "data: ".json_encode($eventData). "\n\n";
        }else{
            Cache::rememberForever('product', function () {
                return Product::count();
            });

            Cache::rememberForever('category', function () {
                return Category::count();
            });

            Cache::rememberForever('randomNumber', function(){
                return rand(0000, 99999999);
            });
        }

        ob_flush();
        flush();
    }
}
