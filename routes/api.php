<?php

use Illuminate\Http\Request;
use App\Http\Controllers\NasaController;
use Illuminate\Support\Facades\Route;

Route::get('/instruments', [NasaController::class, 'getInstruments']);
Route::get('/activity-ids', [NasaController::class, 'getLinkedEvents']);
Route::get('/instruments-usage', [NasaController::class, 'getInstrumentsUsage']);
Route::post('/instrument-activity', [NasaController::class, 'getInstrumentActivityUsage']);
