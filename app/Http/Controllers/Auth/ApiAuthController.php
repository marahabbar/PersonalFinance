<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DebtsController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\SavingGoalController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use PhpParser\Node\Stmt\While_;

class ApiAuthController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {

            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        
        $user = User::create($request->toArray());

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        
        $exp_categories=array("Food","Health","Kids","pet","Rent","Bills","transportaion",
                              "Clothes","Education","Entertainment","Insurance",
                               "Beauty and Personal care","Holidays","sport","Gift",
                               "Household","Technology","Other");
        $income_categories=array("salary","Award","Rental","Part-time work","Investments","Other");
        $CategoryCon=new CategoryController;
        foreach( $exp_categories as $Category  ){
            $CategoryCon->category_store($Category,$user->id,1);
         
        }
        foreach( $income_categories as $Category  ){
            $CategoryCon->category_store($Category,$user->id,2);

        }
        $response = ['token' => $token,'user ID'=> $user->id ,'Category data'=> $CategoryCon->categories_show($user->id)];
        return response()->json($response);
    }
    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
       
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                
                $response = ['token' => $token,
                            'data'=>$this->getUser($user->id)];
                return response()->json($response);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }
    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

public function change_password(Request $request,$email)
{
    $input = $request->all();
    $validator = Validator::make($input,[
        'password' => 'required',
        'confirm_password' => 'required|same:password',
        'current_password' => 'required',
    ]);
    if ($validator->fails()) {
        return $this->sendErorr('Validation error',$validator->errors());
    }
    $user=user::select('email')->where('email',$email)->get();
     print($user);
     if($user=='[]'){
         return $this->sendError('Please check your Auth',['error=>Unauthorised']);
     }
     $user_id=user::where('email',$email)->first()->id;
     $user_password=user::find($user_id)->password;
     if(Hash::check($input["current_password"],$user_password))
     {
         if(Hash::check($input["password"], $user_password)){
             return $this->sendError('Validation error',"Password already used,Enter a new password");
         }
     }
 

   
}

public function getUser(int $id){
 $user=new UserController;
 $user_info=User::find($id);
 $cat=new CategoryController;
        
 $debts=new DebtsController;
 $s_goal= new SavingGoalController;

 $tran=$user->Transactions($id);

 $response=[
    'id'=>$user_info->id,
    'name'=>$user_info->name,
    'email'=>$user_info->email,
    'total_saving_amount'=>$user_info->total_saving_amount,
    'balance'=>$user_info->balance,
    'total_incomes'=>$user_info->total_incomes,
    'total_expenses'=>$user_info->total_expenses,
    'Category'=>  $cat->categories_show($id),
    'Transactions'=>$tran,
    'Debts'=>$debts->debts_show($id),
    'Saving goals'=>$s_goal->saving_goals_show($id) ];

 //$response = [];
        return response()->json($response);
}

   
}
