<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Category;
use App\Http\Resources\ExpenseRresource;

use  App\Http\Controllers;
use App\Models\Expense;
use Illuminate\Support\Carbon;

class ExpenseController extends Controller
{
    
    public function expense_show(int $id){
   
        return Expense::where('id',$id)
         ->select(
           'id',
           'description',
           'amount',
           'monthly',
           'user_id',
           'date',
           'category_id'
        )->get();
      
       }
   //all xpenses
       public function expenses_show(int $user_id){
   
        $data=Expense::where('user_id',$user_id)
        ->select(
           'id',
           'description',
           'amount',
           'monthly',
           'user_id',
           'date',
           'category_id'
        )->get();
      
        return $data;
       }

       public function expenses_show_month(int $user_id,string $date){
   
        return Expense::where('user_id',$user_id)->whereMonth('date',$date)
        ->select(
            'id',
            'description',
            'amount',
            'monthly',
            'user_id',
            'date',
            'category_id'
        )->get();
      
       }
    
       public function expenses_show_day(int $user_id,string $date){
   
        return Expense::where('user_id',$user_id)->whereDay('date',$date)
        ->select(
            'id',
            'description',
            'amount',
            'monthly',
            'user_id',
            'date',
            'category_id'
        )->get();
      
       }
    
   public function expense_update(int $id,Request $request){
    $old_expense= Expense::find($id);
  $difference=$old_expense->amount- $request->amount;

     
    Expense::where('id',$id)
               ->update(['description'=>$request->description,
                         'amount'=>$request->amount,
                         'monthly'=>$request->monthly,
                         'user_id'=>$request->user_id,
                         'date'=>$request->year.'-'.$request->month.'-'.$request->day.' '.$request->time,
                         'category_id'=>$request->category_id+(24*($request->user_id-1)),
                         ]);
   
   // update balance && total expenses
   
    User::find($request->user_id)->increment('balance',$difference);
    User::find($request->user_id)->decrement('total_expenses',$difference);
//update catogry
Category::find($old_expense->category_id)->decrement('current_amount',$old_expense->amount);
Category::find($request->category_id+(24*($request->user_id-1)))->increment('current_amount',$request->amount);

//return balance && total expenses.........
      
       return response()->json([$this->expense_show($id) ,
       User::where('id',$request->user_id)->select('balance','total_expenses')->get(),
       Category::where('id',$request->category_id)->select('current_amount')->get()]);
    
   
   }
   
  public function expense_store(Request $request){
       
    $expense= Expense::Create([
             'description'=>$request->description,
             'amount'=>$request->amount,
             'monthly'=>$request->monthly,
             'user_id'=>$request->user_id,
             'date'=>$request->year.'-'.$request->month.'-'.$request->day.' '.$request->time,
             'category_id'=>$request->category_id+(24*($request->user_id-1)),
             ]);
   
 //update balance 
    User::find($request->user_id)->decrement('balance',$request->amount);
    User::find($request->user_id)->increment('total_expenses',$expense->amount);
 
 //current_amount// category
    Category::find($request->category_id+(24*($request->user_id-1)))->increment('current_amount',$request->amount);
// return new balance........
     
     return response()->json([new ExpenseRresource($expense) ,
     User::where('id',$expense->user_id)->select('balance','total_expenses')->get(),
     Category::where('id',$expense->category_id)->select('current_amount')->get()]);
    
         }
   
   
   
   
   
  public function expense_delete(int $id){
      $deleted_expense=Expense::find($id);
// update balance && saving box
     User::find($deleted_expense->user_id)->increment('balance',$deleted_expense->amount);
     User::find($deleted_expense->user_id)->decrement('total_expenses',$deleted_expense->amount);

//update category
    Category::find( $deleted_expense->category_id)->decrement('current_amount', $deleted_expense->amount);
   

// delete 
    Expense::where('id',$id)
           ->delete();
           
   
  
       return $this->expense_show($id);
       return response()->json([ User::where('id',$deleted_expense->user_id)->select('balance','total_expenses')->get(),
       Category::where('id',$deleted_expense->category_id)->select('current_amount')->get()]);
     
         }



         public function expense_add(int $id){
            $monthly_exp= Expense::find($id);
            $expense= Expense::Create([
                     'description'=>$monthly_exp->description,
                     'amount'=>$monthly_exp->amount,
                     'monthly'=>$monthly_exp->monthly,
                     'user_id'=>$monthly_exp->user_id,
                     'date'=>Carbon::now(),
                     'category_id'=>$monthly_exp->category_id,
                     ]);
           
         //update balance 
            User::find($monthly_exp->user_id)->decrement('balance', $expense->amount);
            User::find($monthly_exp->user_id)->increment('total_expenses',$expense->amount);
         
         //current_amount// category
            Category::find($monthly_exp->category_id)->increment('current_amount',$expense->amount);
        // return new balance........
             
             return response()->json([new ExpenseRresource($expense) ,
             User::where('id',$expense->user_id)->select('balance','total_expenses')->get(),
             Category::where('id',$expense->category_id)->select('current_amount')->get()]);
            
                 }   
         
}
