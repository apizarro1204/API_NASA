<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NasaController;

Route::get('/instruments', [NasaController::class, 'getInstruments']);
Route::get('/activity-ids', [NasaController::class, 'getActivityIDs']);
Route::get('/', function () {
    return view('welcome');
});
