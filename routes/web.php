<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return view('tasks');
});
Route::post('/api/tasks', [TaskController::class, 'store']);
Route::get('/tasks', [TaskController::class, 'index']);
