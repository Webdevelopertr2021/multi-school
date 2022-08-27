<?php

namespace App\Http\Controllers;

use App\Models\Notification as CustomNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getList(Request $req)
    {
        $authenticated = false;
        $userId = null;

        if(auth()->check())
        {
            $authenticated = true;
            $userId = auth()->user()->id;

        }
        else if(auth("supervisor")->check())
        {
            $authenticated = true;
            $userId = auth("supervisor")->user()->id;

        }
        else if(auth("teacher")->check())
        {
            $authenticated = true;
            $userId = auth("teacher")->user()->id;
        }
        if($authenticated == true)
        {
            if($req->limit != "")
            {
                $notifications = CustomNotification::where("user_id",$userId)->orderBy("id","desc")->limit($req->limit);
                $unseen = count($notifications->where("is_seen","unseen")->get());
                $notifications = $notifications->get();
                return [
                    "status" => "ok",
                    "data" => $notifications,
                    "unseen" => $unseen,
                ];
            }
            else
            {
                CustomNotification::where("user_id",$userId)->update(["is_seen"=>"seen"]);
                $notifications = CustomNotification::where("user_id",$userId)->orderBy('id',"desc")->paginate(15);
                return [
                    "status" => "ok",
                    "data" => $notifications
                ];
            }
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }
}
