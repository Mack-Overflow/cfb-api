<?php

use App\Http\Controllers\AuthEmailController;
use App\Http\Controllers\BallotController;
// use App\Http\Controllers\Api\ResultsController;
use App\Http\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/teams', [TeamController::class,'index']);
// Route::get('/results', [ResultsController::class,'index']);

Route::post('/auth/email/request', [AuthEmailController::class, 'requestCode']);
Route::post('/auth/email/verify',  [AuthEmailController::class, 'verifyCode']);

Route::post('/auth/profile', function (Request $r) {
    $r->validate(['name'=>'required|string|max:120']);
    $u = $r->user(); $u->name = $r->name; $u->save();
    return response()->json(['ok'=>true, 'user'=>$u]);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/ballot', [BallotController::class,'show']);
Route::middleware('auth:sanctum')->post('/ballot', [BallotController::class,'store']);

// Route::get('/auth/google/redirect', [AuthGoogleController::class, 'redirect']);
// Route::get('/auth/google/callback', [AuthGoogleController::class, 'callback']);

// Route::post('/auth/logout', [AuthEmailController::class,'logout']);