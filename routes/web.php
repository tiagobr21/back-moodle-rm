<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/',function(){
   return view('welcome');
});

Route::get('/apirm',[App\Http\Controllers\ApiRmController::class,'index']);


Route::get('/apimoodle',[App\Http\Controllers\ApiMoodleController::class,'mood']);


Route::get('/grades',[App\Http\Controllers\GradesMoodleRm::class,'sendGrades']);


