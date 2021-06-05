<?php

namespace App\Http\Controllers;

use App\Http\Resources\SavingGoalsRresource;
use App\Models\SavingGoal;
use App\Models\User;
use Illuminate\Http\Request;

class SavingGoalController extends Controller
{
    public function saving_goal_show(int $id){
   
        return SavingGoal::where('id',$id)
        ->select(
           'id',
           'description',
           'target_amount',
           'current_amount',
           'start_date',
           'end_date',
           'user_id',
        
        )->get();
       
      
       }
       public function saving_goals_show(int $user_id){
   
        return SavingGoal::where('user_id',$user_id)
        ->select(
           'id',
           'description',
           'target_amount',
           'current_amount',
           'start_date',
           'end_date',
           'user_id',
        
        )->get();
      
       }
       public function saving_goal_update(int $id,Request $request){
        $old_saving_goal=SavingGoal::find($id);
        $amount_difference=$request->current_amount- $old_saving_goal->current_amount;
        SavingGoal::where('id',$id)
                  ->update(['description'=>$request->description,
                            'target_amount'=>$request->target_amount,
                           // 'user_id'=>$request->user_id,
                            'end_date'=>$request->end_date,
                            'start_date'=>$request->start_date
                            ]); 
                            
 // update total saving amount

 if (User::find($request->user_id)->total_saving_amount-$amount_difference>=0) {
    User::find($request->user_id)->decrement('total_saving_amount',$amount_difference);
    SavingGoal::where('id',$id)
                  ->update(['current_amount'=>$request->current_amount]); 
 return response()->json([$this->saving_goal_show( $id),
                  User::where('id', $request->user_id)->select('total_saving_amount')->get()]);
 } else {
    return response()->json([$this->saving_goal_show( $id)])  ;   
 }
 


                       
}


public function saving_goal_store(Request $request){
    $amount=0;
    if (User::find($request->user_id)->total_saving_amount-$request->current_amount>=0){
        $amount=$request->current_amount;
    }
    $goal= SavingGoal::Create([
        'description'=>$request->description,
        'target_amount'=>$request->target_amount,
        'current_amount'=>$amount,
        'user_id'=>$request->user_id,
        'end_date'=>$request->end_date,
        'start_date'=>$request->start_date
      ]);

        User::find($request->user_id)->decrement('total_saving_amount',$amount);
     
      return response()->json([new SavingGoalsRresource($request),
      User::where('id', $goal->user_id)->select('total_saving_amount')->get()]);
}

public function saving_goal_delete(int $id){
    $deleted_saving_goal=SavingGoal::find($id);
    User::find($deleted_saving_goal->user_id)->increment('total_saving_amount',$deleted_saving_goal->current_amount);
    SavingGoal::where('id',$id)
    ->delete();
    

// update 

      return response()->json([ User::where('id', $deleted_saving_goal->user_id)->select('total_saving_amount')->get()]);
  

}
}