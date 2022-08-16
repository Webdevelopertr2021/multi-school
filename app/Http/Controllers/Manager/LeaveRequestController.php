<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestApproval;
use App\Models\User;
use App\Models\Notification as CustomNotification;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:manager");
    }

    public function getList(Request $req)
    {
        $user = auth("manager")->user();

        $supervisors = User::where("manager_id",$user->id)->pluck("id")->toArray();

        if($user->teacher_application_permission == 1)
        {
            $leaves = LeaveRequestApproval::whereIn("supervisor_id",$supervisors)
            ->with("teacher:id,name,photo,photo_url")->with("leave")->paginate(10);

            return [
                "status" => "ok",
                "leaves" => $leaves,
            ];
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }

    public function updateStatus(Request $req)
    {
        $mainLeaveReq = LeaveRequest::find($req->reqId);
        $status = "";
        if($req->status == "pending")
        {
            $status = 0;
        }
        else
        {
            $status = 1;
        }
        $mainLeaveReq->approved_by_manager = $status;
        $mainLeaveReq->manager_msg = $req->msg;
        $mainLeaveReq->save();

        

        if($status == 1)
        {
            $notify = new CustomNotification();
            $notify->user_id = $mainLeaveReq->teacher_id;
            $notify->type = "application_approve";
            $notify->msg = "Your application for leave request ($mainLeaveReq->subject) was approved";
            $notify->save();
        }
        else if($status == 0)
        {
            $notify = new CustomNotification();
            $notify->user_id = $mainLeaveReq->teacher_id;
            $notify->type = "application_approve";
            $notify->msg = "Your application for leave request ($mainLeaveReq->subject) was denied. Please apply again with proper explanation";
            $notify->save();
        }

        return [ 
            "status" => "ok",
            "action" => $req->status=="pending"?0:1,
            "msg" => "Application Status updated successfully",
        ];
    }
}
