<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestApproval;
use Illuminate\Http\Request;
use App\Models\Notification as CustomNotification;
use Carbon\Carbon;
use DataTables;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function getList(Request $req)
    {
        $leaves = LeaveRequestApproval::with("teacher:id,name,photo,photo_url")->with("leave");

        return DataTables::of($leaves)
        ->addColumn("photo",function($row){
            if($row->teacher->photo == "")
            {
                $html = '<img class="user-thumb-40" src="/image/portrait-placeholder.png" alt="">';
            }
            else
            {
                $html = '<img class="user-thumb-40" src="'.$row->teacher->photo_url.'" alt="">';
            }
            return $html; 
        })
        
        ->addColumn("request_by",function($row){
          return $row->teacher->name;      
        })
        ->addColumn("subject",function($row){
            return $row->leave->subject;
        })
        ->addColumn("vacation_type",function($row){
            return $row->leave->vacation_type;
        })
        ->addColumn("date_range",function($row){
            $text = Carbon::parse($row->leave->from_date)->format("d M Y"). " - " . Carbon::parse($row->leave->to_date)->format("d M Y");
            return $text;
        })
        ->addColumn("total_day",function($row){
            return $row->leave->total_days . " days";
        })
        ->addColumn("application_date",function($row){
            return Carbon::parse($row->leave->created_at)->format("d M Y - H:i A");
        })
        ->addColumn("status",function($row){
            if($row->leave->status == "pending")
            {
                $html = '<span class="bage badge-pill badge-warning">Pending</span>';
            }
            else if($row->leave->status == "rejected")
            {
                $html = '<span class="bage badge-pill badge-danger">Rejected</span>';
            }
            else if($row->leave->status == "approved")
            {
                $html = '<span class="bage badge-pill badge-success">Approved</span>';
            }
            return $html;
        })
        // ->addColumn("action",function($row){
        //     $html = '<button class="btn btn-primary btn-sm">Take action <i class="fas fa-arrow-right"></i></button>';
        //     return $html;
        // })
        ->rawColumns(["photo","status"])
        ->make(true);
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

            

            if($status == "approved")
            {
                $notify = new CustomNotification();
                $notify->user_id = $mainLeaveReq->teacher_id;
                $notify->type = "application_approve";
                $notify->msg = "Your application for leave request ($mainLeaveReq->subject) was approved";
                $notify->save();
            }
            else if($status == "rejected")
            {
                $notify = new CustomNotification();
                $notify->user_id = $mainLeaveReq->teacher_id;
                $notify->type = "application_approve";
                $notify->msg = "Your application for leave request ($mainLeaveReq->subject) was denied. Please apply again with proper explanation";
                $notify->save();
            }

            return [ 
                "status" => "ok",
                "action" => $req->status,
                "msg" => "Application Status updated successfully",
            ];
        }
    }
}
