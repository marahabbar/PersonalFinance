<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Controllers\CategoryController;
// use App\Http\Controllers\DebtsController;
// use App\Http\Controllers\SavingGoalController;
// use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {

            return response(['errors' => $validator->errors()->all()], 422);
        }

        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);

        $user = User::create($request->toArray());
        
        User::where('id', $user->id)->update(['family' => $user->id]);

        $token = $user->createToken('Laravel8PassportAuth')->accessToken;

        $exp_categories = array(
            "Food", "Health", "Kids", "pet", "Rent", "Bills", "transportaion",
            "Clothes", "Education", "Entertainment", "Insurance",
            "Beauty and Personal care", "Holidays", "sport", "Gift",
            "Household", "Technology", "Other"
        );
        $income_categories = array("salary", "Award", "Rental", "Part-time work", "Investments", "Other");
        $CategoryCon = new CategoryController;
        foreach ($exp_categories as $Category) {
            $CategoryCon->category_store($Category, $user->id, 1);
        }
        foreach ($income_categories as $Category) {
            $CategoryCon->category_store($Category, $user->id, 2);
        }
        $response = ['token' => $token, 'id' => $user->id];
        return response()->json($response);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel8PassportAuth')->accessToken;

                $response = [
                    'token' => $token,
                    'id' => $user->id,
                    'email' => $user->email
                ];
                return response()->json($response);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }
    }
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

}
