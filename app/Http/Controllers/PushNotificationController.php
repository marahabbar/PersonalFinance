<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Debt;
use App\Models\Expense;
use App\Models\Income;
use App\Models\PushNotification;
use App\Models\User;

use Illuminate\Support\Facades\DB;

class PushNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function user_notification(int $id){
        return PushNotification::where('user_id',$id)->select(
              'id','title','body','type','user_id','date') ->orderBy('id','desc')->get();
  
  
       }
    public function send($notifi)
    {
        $notification = PushNotification::Create([
            'title' => $notifi->get('title'),
            'body' => $notifi->get('body'),
            'type' => $notifi->get('type'),
            'date' => Carbon::now(),
            'user_id'=>$notifi->get('user_id')
        ]);

        $url = 'https://fcm.googleapis.com/fcm/send';
        $dataArr = array('click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'id' => 1, 'status' => "done"//,'type' => $notifi->get('type')
     );
        $msg= array('title' => $notifi->get('title'), 'text' => $notifi->get('body'), 'sound' => 'default', 'badge' => '1','type'=>$notifi->get('type'));
        $arrayToSend = array('to' => "/topics/all"//User::where('id', $notifi->get('user_id'))->pluck('FCM_token')[0]
        , 'notification' => $msg, 'data' => $dataArr,
         'priority' => 'high');
       
        $fields = json_encode($arrayToSend);
        $headers = array(
            'Authorization: key=' . "server key",
            'Content-Type: application/json'
        );

      
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);
       
        //var_dump($result);
        curl_close($ch);

        echo $result;
        //return redirect()->back()->with('success', 'Notification Send successfully');
    }
    public function category_notif()
    {
        $users = User::all();
        foreach ($users as $user) {
            $categories = DB::table('users')->join('categories', 'users.id', '=', 'categories.user_id')
                ->where('family', $user->family)
                ->select(
                    'categories.id',
                    'categories.name',
                    'max_amount',
                    DB::raw('sum(current_amount) AS current_amount'),
                    'type',
                    'users.id'
                )->groupBy('categories.name')->get();

            foreach ($categories as $category) {
                if (  $category->current_amount <= $category->max_amount*0.2) {

                    $this->send(collect([
                        "title" => 'category max amount',
                        "body" => $category->name . ': you are  vary close to the Max amount of spending  ',
                        "type" => "remider",
                        "user_id" => $user->id

                    ]));
                }
            }
        }
    }
    public function balance_notif(int $id)
    {
            $user=User::find($id);
            $total_income = DB::table('users')->join('incomes', 'users.id', '=', 'incomes.user_id')->where('family', $user->family)->whereMonth('date', now()->month())->sum("amount");
            $total_exp = DB::table('users')->join('expenses', 'users.id', '=', 'expenses.user_id')->where('family', $user->family)->whereMonth('date', now()->month)->sum("amount");
            $total_saving= User::where('family', $user->family)->sum("total_saving_amount");
            $balance=$total_income - $total_exp;
            if ($balance <=  $total_exp*0.2) {
                $this->send(collect([
                    "title" => 'low balance'. $balance,
                    "body" =>  'saving box :'. $total_saving ,
                    "type" => "remider",
                    "user_id" => $user->id

                ]));
            }
   
    }
    public function trans_notif(int $Transaction_id,String $type,String $frq){
        if($type=="expense")
        $Transaction=Expense::find($Transaction_id);
        else
        $Transaction=Income::find($Transaction_id);
        
        
            $this->send(collect([
                "title" => $frq.' '.$type,
                "body" => $Transaction->id.': '. $Transaction->description.','.$Transaction->amount ,
                "type" => "monthly",
                "user_id" => $Transaction->user_id
        
            ]));    
        
        
        }
        


public function debt_notif(){
    $users = User::all();
    foreach ($users as $user) {
        $familyIDS = User::where('family', $user->family)->pluck('id')->toArray();
     $weekafter=now()->modify('+1 week');
      $debts=Debt::where('user_id',$user->id)->where('date','=',$weekafter)->select('debts.id',
      'description',
      'amount',
      'rewind_amount',
      'creditor',
      'debtor',
      'debt_date',
      'due_date',
      'user_id')->get();
     
    foreach($debts as $debt){
    foreach($familyIDS as $memberID) {   
    $this->send(collect([
        "title" =>'one week to debt due date ',
        "body" => $debt->description.': '.$debt->amount ,
        "img" => "default",
        "user_id" => $memberID
    ]));    }
       }

    }


}


public function family_notif(int $user_id,String $familyHead){
    $this->send(collect([
        "title" =>'family membership request ',
        "body" => $familyHead.' : join our family ' ,
        "type" => "request",
        "user_id" =>  $user_id
    ]));


}


 
}
