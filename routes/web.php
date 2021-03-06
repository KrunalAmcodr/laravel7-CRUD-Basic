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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::resource('items','ItemController')->middleware('auth');
Route::get('ajaxitems/items_excelexport','ItemajaxController@items_excel_export')->name('ajaxitems.excelexport');
Route::get('ajaxitems/items_pdfexport','ItemajaxController@items_pdf_export')->name('ajaxitems.pdfexport');
Route::resource('ajaxitems','ItemajaxController')->middleware('auth');
