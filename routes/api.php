

<?php

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

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('App\Http\Controllers')->group(function () {
Route::apiResource('incomes','IncomeController');
Route::get('income_show/{id}', 'IncomeController@income_show');
Route::get('incomes_show/{user_id}', 'IncomeController@incomes_show');
Route::get('incomes_show_day/{user_id}/{date}', 'IncomeController@incomes_show_day');
Route::get('incomes_show_month/{user_id}/{date}', 'IncomeController@incomes_show_month');
Route::post('income_store', 'IncomeController@income_store');
Route::put('income_update/{id}', 'IncomeController@income_update');
Route::delete('income_delete/{id}', 'IncomeController@income_delete');

Route::apiResource('expenses','ExpenseController');
Route::get('expense_show/{id}', 'ExpenseController@expense_show');
Route::get('expenses_show/{user_id}', 'ExpenseController@expenses_show');
Route::get('expenses_show_month/{user_id}/{date}', 'ExpenseController@expenses_show_month');
Route::get('expenses_show_day/{user_id}/{date}', 'ExpenseController@expenses_show_day');
Route::post('expense_store', 'ExpenseController@expense_store');
Route::put('expense_update/{id}', 'ExpenseController@expense_update');
Route::delete('expense_delete/{id}', 'ExpenseController@expense_delete');

Route::apiResource('debts','DebtController');
Route::get('debt_show/{id}', 'DebtController@debt_show');
Route::get('debts_show/{user_id}', 'DebtController@debts_show');
Route::post('debt_store', 'DebtController@debt_store');
Route::put('debt_update/{id}', 'DebtController@debt_update');
Route::delete('debt_delete/{id}', 'DebtController@debt_delete');

Route::apiResource('saving_goals','SavingGoalController');
Route::get('saving_goal_show/{id}', 'SavingGoalController@saving_goal_show');
Route::get('saving_goals_show/{user_id}', 'SavingGoalController@saving_goals_show');
Route::post('saving_goal_store', 'SavingGoalController@saving_goal_store');
Route::put('saving_goal_update/{id}', 'SavingGoalController@saving_goal_update');
Route::delete('saving_goal_delete/{id}', 'SavingGoalController@saving_goal_delete');

Route::apiResource('categories','CategoryController');
Route::get('category_show/{id}', 'CategoryController@Category_show');
Route::get('categories_show/{user_id}', 'CategoryController@Categories_show');
Route::put('category_update/{id}/{user_id}', 'CategoryController@max_amount_update');

Route::get('getUser/{id}/{date}', 'Auth\ApiAuthController@getUser');
Route::get('Transactions_show/{id}', 'UserController@Transactions_show');


});
Route::group(['middleware' => ['cors', 'json.response']], function () {
    Route::namespace('App\Http\Controllers')->group(function () {
    Route::post('/login', 'Auth\ApiAuthController@login')->name('login.api');

    Route::post('/register', 'Auth\ApiAuthController@register')->name('register.api');
    
    });
});

Route::middleware('auth:api')->group(function () {

    Route::post('/logout', 'Auth\ApiAuthController@logout')->name('logout.api');

});

 Route::post('/change-password/{email}', 'Auth\ApiAuthController@change_password')->name('change-password.api');
