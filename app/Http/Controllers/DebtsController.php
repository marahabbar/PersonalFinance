<?php

namespace App\Http\Controllers;

use App\Http\Resources\DebtsRresource;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Http\Request;

class DebtsController extends Controller
{
    public function debt_show(int $id){
   
        return Debt::where('id',$id)
        ->select(
           'id',
           'description',
           'amount',
           'rewind_amount',
           'creditor',
           'debtor',
           'debt_date',
           'due_date',
           'user_id'
        )->get();
      
       }
       public function debts_show(int $user_id){
   
        return Debt::where('user_id',$user_id)
        ->select(
           'id',
           'description',
           'amount',
           'rewind_amount',
           'creditor',
           'debtor',
           'debt_date',
           'due_date',
           'user_id'
        )->get();
      
       }

       public function debt_update(int $id,Request $request){
        $old_debt= Debt::find($id);
        $difference=$old_debt->amount- $request->amount;
        $rewind_difference=$request->rewind_amount-$old_debt->rewind_amount;
     
        Debt::where('id',$id)
                   ->update(['description'=>$request->description,
                             'amount'=>$request->amount,
                             'rewind_amount'=>$request->rewind_amount,
                             'creditor'=>$request->creditor,
                             'debtor'=>$request->debtor,
                             'user_id'=>$request->user_id,
                             'due_date'=>$request->due_date,
                             'debt_date'=>$request->debt_date,
                            
                             ]);
       
       // update balance 
       $user=User::find($request->user_id) ;
       if ($request->creditor==$user->name) {
           //amount
        User::find($request->user_id)->increment('balance',$difference);
        //rewind amount
        User::find($request->user_id)->increment('balance',$rewind_difference);
       } else {
        User::find($request->user_id)->decrement('balance',$difference);
        //rewind amount
        User::find($request->user_id)->decrement('balance',$rewind_difference);
       }
         
           return response()->json([$this->debt_show($id) ,
           User::where('id',$request->user_id)->select('balance')->get()]);
          
       }

       public function debt_store(Request $request){
        
         $debt= Debt::Create([
            'description'=>$request->description,
            'amount'=>$request->amount,
            'rewind_amount'=>$request->rewind_amount,
            'creditor'=>$request->creditor,
            'debtor'=>$request->debtor,
            'user_id'=>$request->user_id,
            'due_date'=>$request->due_date,
            'debt_date'=>$request->debt_date,
           ]);

       //update balance
       $user=User::find($request->user_id) ;
       if ($request->creditor==$user->name) {
        User::find($request->user_id)->decrement('balance',$request->amount-$request->rewind_amount);
       } else {
        User::find($request->user_id)->increment('balance',$request->amount-$request->rewind_amount);
       }
       
       
       //return  new balance
       
       return response()->json([new DebtsRresource($request) ,
       User::where('id',$request->user_id)->select('balance')->get()]);    
        }


        public function debt_delete(int $id){

           // update balance   
            $deleted_debt=Debt::find( $id);
            $user=User::find($deleted_debt->user_id) ;

            if ($deleted_debt->creditor==$user->name) {
             User::find($deleted_debt->user_id)->increment('balance',$deleted_debt->amount-$deleted_debt->rewind_amount);
            } else {
             User::find($deleted_debt->user_id)->decrement('balance',$deleted_debt->amount-$deleted_debt->rewind_amount);
            }
        // delete
            Debt::where('id',$id)
           ->delete();
           
   
   // return new balance
   
             return $this->debt_show($id);
             return response()->json([ User::where('id',$deleted_debt->user_id)->select('balance')->get()]); 
         
         }
}
