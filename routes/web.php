<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/',[IndexController::class, 'index'])->name('index');
Route::get('/admin',[IndexController::class, 'admin'])->name('admin');

Route::resource('category', CategoryController::class);
Route::get('get-categories', [CategoryController::class, 'getCategories'])->name('getcategories');
