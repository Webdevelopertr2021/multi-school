<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Payments;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Models\Notification as CustomNotification;
use App\Helper\Helper;

class SalaryController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function getSalaryData(Request $req)
    {
        if($user = User::find($req->userId))
        {
            $salaryData = array();

            $salaryData["base_salary"] = $user->salary;

            $lastMonth = Carbon::now();

            $payment = Payments::where("teacher_id",$user->id)->whereMonth("paid_month",$lastMonth)->first();

            if(!empty($payment))
            {
                $salaryData["paid"] = true;
            }
            else
            {
                $salaryData["paid"] = false;
            }

            $salaryData["extra"] = Helper::getTeacherExtraSalary($user->id,$lastMonth->format("m"));

            $salaryData["month"] = $lastMonth->format("F");

            return [
                "status" => "ok",
                "salary_data" => $salaryData,
            ];
        }
        else
        {
            return [
                "status" => "fail",
            ];
        }
    }

    public function submitPayment(Request $req)
    {
        $this->validate($req,[
            "teacherId" => "required|exists:users,id",
            "amount" => "required",
            "number" => "required"
        ]);

        $user = User::find($req->teacherId);

        $payment = new Payments();
        $payment->teacher_id = $req->teacherId;
        $payment->bank_name = $req->bankName;
        $payment->reciept_number = $req->number;
        $payment->note = $req->note;

        $paidMonth = Carbon::parse("01-$req->month-".date("Y"));

        $payment->paid_month = $paidMonth;

        $extraData  = Helper::getTeacherExtraSalary($req->teacherId,$req->month);

        $amount = $user->salary + $extraData["salary_rating"] + $extraData["salary_no_leave"];

        $payment->amount = $amount;

        if($req->hasFile("photo"))
        {
            $file = $req->file("photo");
            $newName = rand()."_".time().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$newName,"Bank");
            $payment->attachments = $newName;
        }

        $payment->save();

        
        $user->wallet += $req->amount;
        $user->save();

        // Send notification
        $notify = new CustomNotification();
        $notify->user_id = $req->teacherId;
        $notify->type = "salary_deposit";
        $notify->msg = "You have recived a new payment of $req->amount $ | Reciept number : $req->number";
        $notify->save();
        // End


        return [
            "status" => "ok",
            "msg" => "Payment was sent"
        ];
    }

    public function getPaymentList(Request $req)
    {
        $list = Payments::orderBy("id","desc");

        return DataTables::of($list)
        ->addColumn("date",function($row){
            return $row->created_at->format("Y-m-d");
        })
        ->addColumn("paid_to",function($row){
            return "<b>".User::find($row->teacher_id)->name."</b>";
        })
        ->addColumn("amount",function($row){
            return $row->amount." $";
        })
        ->addColumn("number",function($row){
            return $row->reciept_number;
        })
        ->addColumn("paid_month",function($row){
            return Carbon::parse($row->paid_month)->format("Y, F");
        })
        ->addColumn("attachment",function($row){
            if($row->attachments != "")
            {
                return "<a target='_blank' href='".route("bank-attachment",["filename" => $row->attachments])."'><i class='fas fa-download'></i> View attachment</a>";
            }
            else
            {
                return "N/A";
            }
            
        })
        ->addColumn("status",function($row){
            if($row->status == "pending") {
                return "<span class='badge badge-pill badge-warning'>Waiting for teacher to recieve</span>";
            }
            else {
                return "<span class='badge badge-pill badge-success'>Payment recieved</span>";
            }
        })
        ->rawColumns(["status","paid_to","attachment"])->make(true);
    }

    public function filterSalaryData(Request $req)
    {
        if($user = User::find($req->userId))
        {
            $salaryData = array();

            $salaryData["base_salary"] = $user->salary;

            $selectedMonth = Carbon::parse(date("Y")."/$req->month/3");

            $payment = Payments::where("teacher_id",$user->id)->whereMonth("paid_month",$selectedMonth)->first();

            if(!empty($payment))
            {
                $salaryData["paid"] = true;
            }
            else
            {
                $salaryData["paid"] = false;
            }


            $salaryData["extra"] = Helper::getTeacherExtraSalary($user->id,$req->month);

            $salaryData["month"] = $selectedMonth->format("F");

            return [
                "status" => "ok",
                "salary_data" => $salaryData,
                "s" => $payment,
            ];
        }
        else
        {
            return [
                "status" => "fail",
            ];
        }
    }
}
