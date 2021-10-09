<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use App\Models\Debt;
use App\Models\PushNotification;
use App\Models\SavingGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function deleteAccount(Request $request)
    {
        Expense::where('user_id', $request->id)->delete();
        Income::where('user_id', $request->id)->delete();
        Debt::where('user_id', $request->id)->delete();
        SavingGoal::where('user_id', $request->id)->delete();
        PushNotification::where('user_id', $request->id)->delete();
        Category::where('user_id', $request->id)->delete();
        User::find($request->id)->delete();
    }
    public function Transactions_show(int $user_id, string $date)
    {


        return ['Transactions' => $this->Transactions($user_id, $date)];
    }

    public function  Transactions(int $user_id, string $date)
    {
        $user_info = User::find($user_id);
        $familyIDS = User::where('family', $user_info->family)->pluck('id')->toArray();
        $i = Income::wherein('user_id', $familyIDS)->whereMonth('date', $date)->select(
            'id',
            'description',
            'amount',
            'saving_amount',
            'monthly',
            'user_id',
            DB::raw('YEAR(date) AS year'),
            DB::raw('MONTH(date) AS month'),
            DB::raw('DAY(date) AS day'),
            DB::raw('TIME(date) AS time'),
            DB::raw('category_id-24*(user_id-1) AS category_id'),
        );
        $e = Expense::wherein('user_id', $familyIDS)->whereMonth('date', $date)->select(
            'id',
            'description',
            'amount',
            'monthly',
            'user_id',
            'user_id',
            DB::raw('YEAR(date) AS year'),
            DB::raw('MONTH(date) AS month'),
            DB::raw('DAY(date) AS day'),
            DB::raw('TIME(date) AS time'),
            DB::raw('(category_id-24*(user_id-1)) AS category_id')
        );

        $tran = $i->unionAll($e);

        return $tran->select(
            'id',
            'description',
            'amount',
            'saving_amount',
            'monthly',
            'user_id',
            DB::raw('YEAR(date) AS year'),
            DB::raw('MONTH(date) AS month'),
            DB::raw('DAY(date) AS day'),
            DB::raw('TIME(date) AS time'),
            DB::raw(' (category_id-24*(user_id-1)) AS category_id ')
        )->get();
    }
    public function getUser(int $id, string $date)
    {

        //$user = new UserController;
        $user_info = User::find($id);
      
        $familyMember=User::where('family', $user_info->family)->select('email','name','total_expenses')->get();
        $debts = new DebtsController;
        $s_goal = new SavingGoalController;
        $cat=new CategoryController;

        $total_income = DB::table('users')->join('incomes', 'users.id', '=', 'incomes.user_id')->where('family', $user_info->family)->whereMonth('date', $date)->sum("amount");
        $total_exp = DB::table('users')->join('expenses', 'users.id', '=', 'expenses.user_id')->where('family', $user_info->family)->whereMonth('date', $date)->sum("amount");
        $total_lent = DB::table('users')->join('debts', 'users.id', '=', 'debts.user_id')->where('family', $user_info->family)->where('creditor', $user_info->email)->sum("amount");
        $total_b = DB::table('users')->join('debts', 'users.id', '=', 'debts.user_id')->where('family', $user_info->family)->where('debtor', $user_info->email)->sum("amount");
        $total_saving_amount = User::where('family', $user_info->family)->sum("total_saving_amount");

      
       
        $response = [
            'id' => $user_info->id,
            'name' => $user_info->name,
            'email' => $user_info->email,
            'total_saving_amount' => $total_saving_amount,
            'balance' => ($total_income - $total_exp),
            'total_incomes' => $total_income,
            'total_expenses' => $total_exp,
            'lent' => $total_lent,
            'borrowed' => $total_b,
            'family_members'=>$familyMember,
            'Category' =>  $cat->categories_show($id),
            'Transactions' => $this->Transactions($id, $date),
            'Debts' => $debts->debts_show($id),
            'Saving goals' => $s_goal->saving_goals_show($id)

        ];
       

        return response()->json($response);
    }
    public function user_show(User $user, string $date)
    {

        $cat = new CategoryController;

        $debts = new DebtsController;
        $s_goal = new SavingGoalController;

        $tran = [$this->Transactions($user->id, $date)];
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
        $u = [
            $user->id,
            'Category' =>  $cat->categories_show($user->id),
            'Transactions' => $tran,
            'Debts' => $debts->debts_show($user->id),
            'Saving goals' => $s_goal->saving_goals_show($user->id)
        ];
        return $u;
    }
    public function Reset()
    {

        $result = User::select('id', 'balance', 'total_incomes', 'total_expenses')->get();
        foreach ($result as $row) {

            User::find($row->id)->increment('total_saving_amount', $row->balance);
            User::where('id', $row->id)->update([
                'balance' => 0,
                'total_incomes' => 0,
                'total_expenses' => 0
            ]);

            Category::where('user_id', $row->id)
                ->update([
                    'max_amount' => 0,
                    'current_amount' => 0,
                ]);
        }
    }
    public function MonthlyExpence()
    {
        $users = User::select('id')->get();
        foreach ($users as $row) {
            $expenses = Expense::where('user_id', $row->id)->select('id', 'monthly')->get();
            foreach ($expenses as $e) {
                if ($e->monthly == 1) {
                    $notif = new PushNotificationController;
                    $notif->trans_notif($e->id, "expense", $e->monthly);
                }
            }
        }
    }

    public function MonthlyIncome()
    {
        $users = User::select('id')->get();

        foreach ($users as $row) {
            $incomes = Income::where('user_id', $row->id)->select('id', 'monthly')->get();
            foreach ($incomes as $i) {
                if ($i->monthly == 1) {

                    $notif = new PushNotificationController;
                    $notif->trans_notif($i->id, "income", $i->monthly);
                }
            }
        }
    }


    public function DailyExpence()
    {
        $users = User::select('id')->get();

        foreach ($users as $row) {
            $expenses = Expense::where('user_id', $row->id)->select('id', 'monthly')->get();
            foreach ($expenses as $e) {
                if ($e->monthly == 3) {
                    $notif = new PushNotificationController;
                    $notif->trans_notif($e->id, "expense", $e->monthly);
                }
            }
        }
    }

    public function DielyIncome()
    {
        $users = User::select('id')->get();

        foreach ($users as $row) {
            $incomes = Income::where('user_id', $row->id)->select('id', 'monthly')->get();
            foreach ($incomes as $i) {
                if ($i->monthly == 3) {

                    $notif = new PushNotificationController;
                    $notif->trans_notif($i->id, "income", $i->monthly);
                }
            }
        }
    }
    public function WeeklyExpence()
    {
        $users = User::select('id')->get();

        foreach ($users as $row) {
            $expenses = Expense::where('user_id', $row->id)->select('id', 'monthly')->get();
            foreach ($expenses as $e) {
                if ($e->monthly == 2) {

                    $notif = new PushNotificationController;
                    $notif->trans_notif($e->id, "expense", $e->monthly);
                }
            }
        }
    }

    public function WeeklyIncome()
    {
        $users = User::select('id')->get();

        foreach ($users as $row) {
            $incomes = Income::where('user_id', $row->id)->select('id', 'monthly')->get();
            foreach ($incomes as $i) {
                if ($i->monthly == 2) {

                    $notif = new PushNotificationController;
                    $notif->trans_notif($i->id, "income", $i->monthly);
                }
            }
        }
    }

    public function frqTran(Request $request)
    {
        if ($request->type == "income") {
            $inc = new IncomeController;
            $inc->income_add($request->id);
        } else {
            $exp = new ExpenseController;
            $exp->expense_add($request->id);
        }
    }




    public function AddFamilyMemberRequest(Request $request)
    {
        $user_name = User::where('email', $request->email)->pluck('name')->first();
        $user_id = User::where('email', $request->email)->pluck('id')->first();
        $familyHead = User::where('id', $request->id)->pluck('email')->first();

        if ($user_name == null)
            return "user not found";
        else {
            $notif = new PushNotificationController;
            $notif->family_notif($user_id, $familyHead);


            return "waiting for  " . $user_name . " approval";
        }
    }
    public function AddFamilyMember(Request $request)
    {
        $family = User::where('email', $request->head)->pluck('family')->first();

        User::where('id', $request->id)->update(['family' => $family]);
    }



    public function FCM(Request $request)
    {

        User::where('id', $request->id)->update(['FCM_token' => $request->FCM_token]);
    }
}
