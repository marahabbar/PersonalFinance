<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Category;
use App\Http\Resources\IncomeRresource;
use Illuminate\Http\Request;
use  App\Http\Controllers;
use App\Models\Income;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function income_show(int $id){
   
     return Income::where('id',$id)
     ->select(
        'id',
        'description',
        'amount',
        'saving_amount',
        'monthly',
        'user_id',
        'date'
     )->get();
   
    }
    
// all incomes
    public function incomes_show(int $user_id){
   
      return  Income::where('user_id',$user_id)
      ->select(
         'id',
         'description',
         'amount',
         'saving_amount',
         'monthly',
         'date'
      )->get();
    
     }
     public function incomes_show_month(int $user_id,string $date){
   
      return Income::where('user_id',$user_id)->whereMonth('date',$date)
      ->select(
         'id',
         'description',
         'amount',
         'saving_amount',
         'monthly',
         'date',     
      )->get();
    
     }
     public function incomes_show_day(int $user_id,string $date){
   
      return Income::where('user_id',$user_id)->whereDay('date',$date)
      ->select(
         'id',
         'description',
         'amount',
         'saving_amount',
         'monthly',
         'date'
      )->get();
    
     }

public function income_update(int $id,Request $request){

  $old_income= Income::find($id);
  $toltal_incomes_difference=$request->amount - $old_income->amount;
  $total_saving_amount_difference=$request->saving_amount-$old_income->saving_amount;
  $balance_difference=$toltal_incomes_difference- $total_saving_amount_difference;

  Income::where('id',$id)
            ->update(['description'=>$request->description,
                      'amount'=>$request->amount,
                      'saving_amount'=>$request->saving_amount,
                      'monthly'=>$request->monthly,
                      'user_id'=>$request->user_id,
                      'category_id'=>$request->category_id+(24*($request->user_id-1)),
                      'date'=>$request->year.'-'.$request->month.'-'.$request->day.' '.$request->time,
                      ]);

// update balance && saving box
User::find($request->user_id)->increment('balance',$balance_difference);
User::find($request->user_id)->increment('total_saving_amount',$total_saving_amount_difference);
User::find($request->user_id)->increment('total_incomes',$toltal_incomes_difference);

//update catogry
Category::find($old_income->category_id)->decrement('current_amount',$old_income->amount);
Category::find($request->category_id+(24*($request->user_id-1)))->increment('current_amount',$request->amount);


//return  new balance  ,saving amount......
  
    return response()->json(["income"=>$this->income_show($id) ,"user"=>
    User::where('id',$request->user_id)->select('balance','total_saving_amount','total_incomes')->get(),
    "category"=> Category::where('id',$request->category_id)->select('current_amount')->get()]);
     

}





    public function income_store(Request $request,){
     
     $income= Income::Create([
          'description'=>$request->description,
          'amount'=>$request->amount,
          'saving_amount'=>$request->saving_amount,
          'monthly'=>$request->monthly,
          'user_id'=>$request->user_id,
          'date'=>$request->year.'-'.$request->month.'-'.$request->day.' '.$request->time,,
          'category_id'=>$request->category_id+(24*($request->user_id-1)),
       ]);
   $increase=$income->amount-$income->saving_amount;
//update balance && saving box

User::find($request->user_id)->increment('balance',$increase);
User::find($request->user_id)->increment('total_saving_amount',$income->saving_amount);
User::find($request->user_id)->increment('total_incomes',$income->amount);

 //update category
 Category::find($income->category_id)->increment('current_amount',$request->amount);

// return new balance.........                    
    return response()->json(["income"=>new  IncomeRresource($income) ,"user"=>User::where('id',$income->user_id)->select('balance','total_saving_amount','total_incomes')->get(),
    "category"=>Category::where('id',$income->category_id)->select('current_amount')->get()]);
      }





   public function income_delete(int $id){
        $deleted_income= Income::find($id);
        $increase=$deleted_income->amount-$deleted_income->saving_amount;

      
//update user.. balance && saving box
     User::find($deleted_income->user_id)->decrement('balance',$increase);
     User::find($deleted_income->user_id)->decrement('total_saving_amount',$deleted_income->saving_amount);
     User::find($deleted_income->user_id)->decrement('total_incomes',$deleted_income->amount);

//update category
Category::find($deleted_income->category_id)->decrement('current_amount',$deleted_income->amount);
   

// delete 
  Income::where('id',$id)
        ->delete();
        



        return response()->json([ "user"=>User::where('id',$deleted_income->user_id)->select('balance','total_saving_amount','total_incomes')->get(),
        "category"=>Category::where('id',$deleted_income->category_id)->select('current_amount')->get()]);
     
      
      }
      


      public function income_add(int $id){
        $monthly_exp= Income::find($id);
       
        $income= Income::Create([
          'description'=>$monthly_exp->description,
          'amount'=>$monthly_exp->amount,
          'saving_amount'=>$monthly_exp->saving_amount,
          'monthly'=>$monthly_exp->monthly,
          'user_id'=>$monthly_exp->user_id,
          'date'=>Carbon::now(),
          'category_id'=>$monthly_exp->category_id,
       ]);
      $increase=$income->amount-$income->saving_amount;
     //update balance && saving box

User::find($monthly_exp->user_id)->increment('balance',$increase);
User::find($monthly_exp->user_id)->increment('total_saving_amount',$income->saving_amount);
User::find($monthly_exp->user_id)->increment('total_incomes',$income->amount);

 //update category
 Category::find($monthly_exp->category_id)->increment('current_amount',$monthly_exp->amount);

// return new balance.........                    
    return response()->json([new  IncomeRresource($income),User::where('id',$income->user_id)->select('balance','total_saving_amount','total_incomes')->get(),Category::where('id',$income->category_id)->select('current_amount')->get()]);
      
  }



    
   
    
}
