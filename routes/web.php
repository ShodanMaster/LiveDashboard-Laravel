<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/',[IndexController::class, 'index'])->name('index');
Route::get('/admin',[IndexController::class, 'admin'])->name('admin');
Route::get('/category',[CategoryController::class, 'index'])->name('category');
Route::get('get-categories', [CategoryController::class, 'getCategories'])->name('getcategories');
Route::post('store', [CategoryController::class, 'store'])->name('storecategory');
Route::post('udpate', [CategoryController::class, 'udpate'])->name('updatecategory');
Route::post('delete', [CategoryController::class, 'delete'])->name('deletecategory');
