<?php
use Illuminate\Http\Request;
use App\Http\Controllers\AdminDoc;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Routes for Admin
    Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
        // Admin Document Routes
        Route::get('/Admin-documents', [AdminDoc::class, 'index']);
        Route::post('/Admin-documents', [AdminDoc::class, 'store']);
        Route::get('/Admin-documents/{id}', [AdminDoc::class, 'show']);
        Route::post('/Admin-documents/{id}', [AdminDoc::class, 'update']);
        Route::delete('/Admin-documents/{id}', [AdminDoc::class, 'destroy']);

        // Admin Category Routes
        Route::get('/categories', [CategorieController::class, 'index']);
        Route::post('/categories', [CategorieController::class, 'store']);
        Route::get('/categories/{id}', [CategorieController::class, 'show']);
        Route::put('/categories/{id}', [CategorieController::class, 'update']);
        Route::delete('/categories/{id}', [CategorieController::class, 'destroy']);

        // Admin User management routes
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::get('/roles', [UserController::class, 'getRoles']);
    });

    // Routes for User
    Route::group(['middleware' => ['auth:sanctum', 'role:user']], function () {
        // User-specific document routes
        Route::get('/user/documents', [DocumentController::class, 'getUserDocuments']);
        Route::delete('/document/{id}', [DocumentController::class, 'deleteDocument']);
        Route::post('/document', [DocumentController::class, 'store']);
    });

    // Shared Routes (Both Admin and User can access these)
    Route::get('/categorie', [CategorieController::class, 'index']);
});
