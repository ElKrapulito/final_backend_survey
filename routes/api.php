<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/register', [LoginController::class, 'register']);
Route::get('/survey/{id}', [SurveyController::class, 'show']);
Route::post('/answer', [AnswerController::class, 'store']);
Route::get('/stats/{id}', [SurveyController::class, 'stats']);
Route::get('/stats/total/{id}', [SurveyController::class, 'statsQuestion']);

Route::middleware('auth:api')->group(function () {
    Route::get('/survey', [SurveyController::class, 'index']);
    Route::post('/survey', [SurveyController::class, 'store']);
    Route::put('/survey/{id}', [SurveyController::class, 'update']);
    Route::delete('/survey/{id}', [SurveyController::class, 'destroy']);
    Route::delete('/question/{id}', [QuestionController::class, 'destroy']);
    Route::delete('/input/{id}', [InputController::class, 'destroy']);
    Route::put('/answer/{id}', [AnswerController::class, 'update']);
    Route::get('/answer/{id}', [AnswerController::class, 'show']);
    Route::delete('/answer/{id}', [AnswerController::class, 'destroy']);
    Route::get('/answer', [AnswerController::class, 'index']);
    Route::get('/user/survey/{id}', [UserController::class, 'mySurveys']);
    Route::get('/user/answer/{id}', [UserController::class, 'myAnswers']);
});