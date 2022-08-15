<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware("auth:manager");
    }

    public function getTeacher()
    {
        $id = auth("manager")->user()->id;
        $supervisors = User::where("role","supervisor")->where("manager_id",$id)->pluck("id")->toArray();

        $teachers = AssignedSupervisor::whereIn("supervisor_id",$supervisors)->pluck("teacher_id")->toArray();

        $users = User::whereIn("id",$teachers)->with("supervisor:id,supervisor_id,teacher_id")
        ->with("rating")->withCount("rating")
        ->get();


        return response()->json($users);
    }
}
