<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TodoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// RESTful API Routes for Notes
Route::apiResource('notes', NoteController::class);

// RESTful API Routes for Todos
Route::apiResource('todos', TodoController::class);
Route::patch('todos/{todo}/toggle', [TodoController::class, 'toggle']);
