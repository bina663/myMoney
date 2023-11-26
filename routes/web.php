<?php

use App\Http\Controllers\EventContoller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/",[EventContoller::class,"index"]);
Route::get("/export",[EventContoller::class,"export"]);
Route::get("/deleteAll",[EventContoller::class,"deleteAll"]);
Route::post("/delete/{id}",[EventContoller::class,"delete"]);
Route::post("/edit",[EventContoller::class,"edit"]);
Route::post("/save",[EventContoller::class,"save"]);
Route::post("/import",[EventContoller::class,"import"]);