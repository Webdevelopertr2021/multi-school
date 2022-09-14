<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestApproval;
use App\Models\Notification as CustomNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:teacher");
    }

    public function submit(Request $req)
    {
        $this->validate($req,[
            "fromDate" => "required|after_or_equal:today",
            "toDate" => "required",
            "subject" => "required",
            "desc" => "required",
            "reason" => "required",
        ],[
            "fromDate.required" => "Select the date from you want to take leave",
            "fromDate.after_or_equal" => "You can't select past dates",
            "toDate.required" => "Select the date till you want the leave",
            "toDate.after" => "You must select date after $req->fromDate",
            "subject.required" => "Please write the subject about your leave",
            "desc.required" => "Please explain the reason why you are taking leave",
            "reason.required" => "Please specify the vacation type"
        ]);
        $me = auth("teacher")->user();

        if($req->reason == "Ordinary without salary")
        {
            if($me->credit_without_salary < 1)
            {
                return [
                    "status" => "fail",
                    "msg" => "You've already requested maximum time for this vacation type"
                ];
            }
        }

        if($req->reason == "1 Hour")
        {
            if($me->credit_time < 1)
            {
                return [
                    "status" => "fail",
                    "msg" => "You've already requested maximum time for this vacation type"
                ];
            }
        }

        $superVisors = AssignedSupervisor::where('teacher_id',auth("teacher")->user()->id)
        ->with("user:id,teacher_application_permission")
        ->get(["id","supervisor_id"]);

        $fromDate = Carbon::parse($req->fromDate);
        $toDate = $fromDate->addDays($req->toDate);

        $totalDay = $fromDate->diffInDays($toDate);
        $teacherId = $me->id;

        $leave = new LeaveRequest();
        $leave->from_date = $req->fromDate;
        $leave->to_date = $toDate;
        $leave->teacher_id = $teacherId;
        $leave->total_days = $totalDay;
        $leave->subject = $req->subject;
        $leave->vacation_type = $req->reason;
        $leave->desc = $req->desc;
        $leave->save();

        foreach($superVisors as $superv)
        {
            if($superv->user->teacher_application_permission == 1)
            {
                LeaveRequestApproval::insert([
                    "leave_requestId" => $leave->id,
                    "supervisor_id" => $superv->supervisor_id,
                    "teacher_id" => $teacherId,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
                
                CustomNotification::insert([
                    "user_id" => $superv->supervisor_id,
                    "type" => "new_leave_request",
                    "msg" => $me->name." has requested leave | Type : $req->reason",
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);

            }
        }

        return [
            "status" => "ok",
            "msg" => "Your leave request has been submitted and now waiting for the approval for your all supervisors.
                        You will be notified once your request has been approved by all supervisors"
        ];

    }

    public function getData(Request $req)
    {
        $teacherId = auth("teacher")->user()->id;

        $leaves = LeaveRequest::where("teacher_id",$teacherId)
        ->with("approval.superv:id,name")
        ->orderBy("id","desc")->paginate(5);

        return [
            "data" => $leaves,
            "credit_without_salary" => auth("teacher")->user()->credit_without_salary,
            "credit_time" => auth("teacher")->user()->credit_time,
        ];

    }
}
