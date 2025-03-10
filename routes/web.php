<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::post('/submit', [ProductController::class, 'submit'])->name('submit');
Route::post('/edit/{id}', [ProductController::class, 'edit'])->name('edit');