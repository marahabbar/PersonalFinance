<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    
    public function Transactions_show(int $user_id,string $date){
  
   
        return['Transactions'=> $this->Transactions($user_id,$date)];
      
       }

    public function  Transactions(int $user_id,string $date){
        $i=Income::where('user_id',$user_id)->whereMonth('date',$date)->select('id',
        'description',
        'amount',
        'saving_amount',
        'monthly',
        DB::raw('YEAR(date) AS year'),
        DB::raw('MONTH(date) AS month'),
        DB::raw('DAY(date) AS day'),
        DB::raw('TIME(date) AS time'),
        DB::raw('category_id-24*(user_id-1) AS category_id'),
        );
        $e=Expense::where('user_id',$user_id)->whereMonth('date',$date)->select( 'id',
        'description',
        'amount',
        'monthly',
        'user_id',
        DB::raw('YEAR(date) AS year'),
        DB::raw('MONTH(date) AS month'),
        DB::raw('DAY(date) AS day'),
        DB::raw('TIME(date) AS time'),
        DB::raw('(category_id-24*(user_id-1)) AS category_id')
       );
        
        $tran=$i->unionAll($e);

       return $tran->select('id',
       'description',
       'amount',
       'saving_amount',
       'monthly',
       DB::raw('YEAR(date) AS year'),
       DB::raw('MONTH(date) AS month'),
       DB::raw( 'DAY(date) AS day'),
       DB::raw('TIME(date) AS time'),
       DB::raw(' (category_id-24*(user_id-1)) AS category_id ')
       )->get();
    }

    public function user_show(User $user,string $date){
        
        $cat=new CategoryController;
        
        $debts=new DebtsController;
        $s_goal= new SavingGoalController;
       
        $tran=[$this->Transactions($user->id,$date)];
        $user
        ->select(
           'id',
           'name',
           'email',
           'total_saving_amount',
           'balance',
           'total_incomes',
           'total_expenses',
                   )->get();
        $u=[ $user->id,
           'Category'=>  $cat->categories_show($user->id),
           'Transactions'=>$tran,
           'Debts'=>$debts->debts_show($user->id),
           'Saving goals'=>$s_goal->saving_goals_show($user->id) ];
        return $u;
      
       }
  public function Reset(){
        
        $result=User::select('id','balance','total_incomes','total_expenses')->get();
        foreach($result as $row){
            
         User::find($row->id)->increment('total_saving_amount',$row->balance);
         User::where('id',$row->id)->update([
         'balance'=>0,
         'total_incomes'=>0,
         'total_expenses'=>0]);

         Category::where('user_id',$row->id)
                ->update([
                    'max_amount'=>0,
                    'current_amount'=>0,
                ]);

        }
    }
        public function MonthlyExpence(){
            $users=User::select('id')->get();
            $exp=new ExpenseController;
            foreach($users as $row){
                $expenses= Expense::where('user_id',$row->id)->select('id','monthly')->get();
                foreach($expenses as $e){
                 if( $e->monthly==1)
                  { $exp->expense_add($e->id);
                      
                  }
                }
            }
        }
     
        public function MonthlyIncome(){
            $users=User::select('id')->get();
            $inc=new IncomeController;
            foreach($users as $row){
                $incomes= Income::where('user_id',$row->id)->select('id','monthly')->get();
                foreach($incomes as $i){
                 if( $i->monthly==1)
                  { 
                    $inc->income_add($i->id);
                      }
                }
            }
        }


        public function DailyExpence(){
            $users=User::select('id')->get();
            $exp=new ExpenseController;
            foreach($users as $row){
                $expenses= Expense::where('user_id',$row->id)->select('id','monthly')->get();
                foreach($expenses as $e){
                 if( $e->monthly==3)
                  { $exp->expense_add($e->id);
                      
                  }
                }
            }
        }
     
        public function DielyIncome(){
            $users=User::select('id')->get();
            $inc=new IncomeController;
            foreach($users as $row){
                $incomes= Income::where('user_id',$row->id)->select('id','monthly')->get();
                foreach($incomes as $i){
                 if( $i->monthly==3)
                  { 
                    $inc->income_add($i->id);
                      }
                }
            }
        }
        public function WeeklyExpence(){
            $users=User::select('id')->get();
            $exp=new ExpenseController;
            foreach($users as $row){
                $expenses= Expense::where('user_id',$row->id)->select('id','monthly')->get();
                foreach($expenses as $e){
                 if( $e->monthly==2)
                  { $exp->expense_add($e->id);
                      
                  }
                }
            }
        }
     
        public function WeeklyIncome(){
            $users=User::select('id')->get();
            $inc=new IncomeController;
            foreach($users as $row){
                $incomes= Income::where('user_id',$row->id)->select('id','monthly')->get();
                foreach($incomes as $i){
                 if( $i->monthly==2)
                  { 
                    $inc->income_add($i->id);
                      }
                }
            }
        }
        
}
