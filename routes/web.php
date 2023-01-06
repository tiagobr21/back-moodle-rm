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

Route::get('/cadastrar',[App\Http\Controllers\ApiRmController::class,'cadastrar']);

Route::get('/consultar',[App\Http\Controllers\ApiRmController::class,'consultar']);

Route::get('/excluir/{id}',[App\Http\Controllers\ApiRmController::class,'excluir']);

Route::get('/criarcurso',[App\Http\Controllers\ApiRmController::class,'criarcurso']);

Route::get('/grades',[App\Http\Controllers\GradesMoodleRm::class,'sendGrades']);


