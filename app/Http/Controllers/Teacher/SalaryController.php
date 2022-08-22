<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use App\Models\User;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:teacher");
    }

    public function getPaymentHistory()
    {
        $wallet = auth("teacher")->user()->wallet;

        $history = Payments::where("teacher_id",auth("teacher")->user()->id)->orderBy("id","desc")->paginate(15);

        return [
            "wallet" => $wallet,
            "history" => $history,
        ];
    }

    public function acceptPayment(Request $req)
    {
        $payment = Payments::find($req->id);
        if($payment->status == "pending")
        {
            $payment->status = "accepted";
            $payment->save();

            $user = User::find($payment->teacher_id);
            $user->wallet -= $payment->amount;
            $user->save();

            return [
                "status" => "ok",
                "amount" => $payment->amount,
            ];
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
        
    }
}
