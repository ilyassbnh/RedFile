<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;

// Route::middleware('auth:sanctum')->group(function() {
//     // Document Routes
//     Route::get('/documents', [DocumentController::class, 'index']);
//     Route::post('/documents', [DocumentController::class, 'store']);
//     Route::get('/documents/{id}', [DocumentController::class, 'show']);
//     Route::put('/documents/{id}', [DocumentController::class, 'update']);
//     Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);

//     // Category Routes
//     Route::get('/categories', [CategorieController::class, 'index']);
//     Route::post('/categories', [CategorieController::class, 'store']);
//     Route::get('/categories/{id}', [CategorieController::class, 'show']);
//     Route::put('/categories/{id}', [CategorieController::class, 'update']);
//     Route::delete('/categories/{id}', [CategorieController::class, 'destroy']);
// });

Route::post('/document', [DocumentController::class, 'store']);
Route::get('/categorie', [CategorieController::class, 'index']);

// // Document routes
// Route::get('/documents', [DocumentController::class, 'index']);
// Route::get('/documents/{document}', [DocumentController::class, 'show']);
// Route::put('/documents/{document}', [DocumentController::class, 'update']);
// Route::post('/documents', [DocumentController::class, 'store']);
// Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
// Route::get('/documents/{document}/download', [DocumentController::class, 'download']); // Added a download route

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// User management routes
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);
Route::get('/roles', [UserController::class, 'getRoles']);

