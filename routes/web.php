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

Route::get('try', function () {
    return view('try');
});
Route::get('/', function () {
    return view('welcome');
      }
    )->name("login");


Route::view('abbar', "marah",['con'=>'marah abbar' ]);


 Route::get('/user/{name}', function ($name) {
    
      return view("user",[
          'id'=>$name
          ]);     }
    );
Route::middleware(['auth'])->group(function () {
    Route::namespace('App\Http\Controllers\tests')->group(function () {
       Route::get('user', 'TestController@show');
     
    });
});
Route::namespace('App\Http\Controllers')->group(function () {
 // Route::get('income', 'IncomeController@index');
Route::get('reset', 'UserController@MonthlyIncome');

});

