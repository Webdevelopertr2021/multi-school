<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\TeacherRating;
use App\Models\User;
use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:supervisor");
    }

    public function index()
    {
        return view("backend.supervisor.index");
    }

    public function getTeacher()
    {
        $id = auth("supervisor")->user()->id;
        $teachers = AssignedSupervisor::where("supervisor_id",$id)->pluck("teacher_id")->toArray();

        $users = User::whereIn("id",$teachers)->with("supervisor:id,supervisor_id,teacher_id")
        ->with("rating")->withCount("rating")
        ->get();


        return response()->json($users);
    }

    public function checkUser(Request $req)
    {
        if($req->userId != "")
        {
            if($user = User::find($req->userId,["id","name","photo","photo_url"]))
            {
                $rv = null;
                $reviewFound = false;
                if($review = TeacherRating::where("teacher_id",$user->id)->where("supervisor_id",auth("supervisor")->user()->id)->first())
                {
                    $rv = $review;
                    $reviewFound = true;
                }
                return [
                    "status" => "ok",
                    "user" => $user,
                    "review" => $rv,
                    "reviewFound" => $reviewFound,
                ];
            }
            else
            {
                return [
                    "status" => "fail",
                    "msg" => "User not found"
                ];    
            }
        }
        else
        {
            return [
                "status" => "fail",
                "msg" => "Invalid input"
            ];
        }
    }
}
