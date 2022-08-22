<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Payments;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

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
            $now = Carbon::now();
            $salary = Payments::where("teacher_id",$user->id)->whereMonth("created_at",$now)->first();
            $paid = false;
            if(!empty($salary))
            {
                $paid = true;
            }
            return [
                "status" => "ok",
                "paid" => $paid,
                "salary" => $salary,
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

        $payment = new Payments();
        $payment->teacher_id = $req->teacherId;
        $payment->bank_name = $req->bankName;
        $payment->reciept_number = $req->number;
        $payment->amount = $req->amount;
        $payment->note = $req->note;

        if($req->hasFile("photo"))
        {
            $file = $req->file("photo");
            $newName = rand()."_".time().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$newName,"Bank");
            $payment->attachments = $newName;
        }

        $payment->save();

        $user = User::find($req->teacherId);
        $user->wallet += $req->amount;
        $user->save();


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
}
