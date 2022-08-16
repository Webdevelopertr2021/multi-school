<?php

namespace App\Http\Controllers\SuperVisor;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestApproval;
use App\Models\Notification as CustomNotification;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:supervisor");
    }

    public function getList(Request $req)
    {
        $user = auth("supervisor")->user();
        if($user->teacher_application_permission == 1)
        {
            $leaves = LeaveRequestApproval::where("supervisor_id",$user->id)
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
        $data = LeaveRequestApproval::find($req->id);

        $data->status = $req->status;

        if($req->msg != "")
        {
            $data->rejection_reason = $req->msg;
        }

        if($data->save())
        {
            $mainLeaveReq = LeaveRequest::find($req->reqId);
            $leaves = LeaveRequestApproval::where('leave_requestId',$req->reqId)->get();

            $status = "pending";

            foreach($leaves as $leave)
            {
                if($leave->status == "approved")
                {
                    $status = "approved";
                    continue;
                }
                else if($leave->status == "rejected")
                {
                    $status = "rejected";
                    break;
                }
                else
                {
                    $status = "pending";
                    break;
                }
            }
            $mainLeaveReq->status = $status;
            $mainLeaveReq->save();

            

            // if($status == "approved")
            // {
            //     $notify = new CustomNotification();
            //     $notify->user_id = $mainLeaveReq->teacher_id;
            //     $notify->type = "application_approve";
            //     $notify->msg = "Your application for leave request ($mainLeaveReq->subject) was approved";
            //     $notify->save();
            // }
            // else if($status == "rejected")
            // {
            //     $notify = new CustomNotification();
            //     $notify->user_id = $mainLeaveReq->teacher_id;
            //     $notify->type = "application_approve";
            //     $notify->msg = "Your application for leave request ($mainLeaveReq->subject) was denied. Please apply again with proper explanation";
            //     $notify->save();
            // }

            return [ 
                "status" => "ok",
                "action" => $req->status,
                "msg" => "Application Status updated successfully",
            ];
        }
    }
}
