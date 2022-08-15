<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\Student;
use App\Models\TeacherRating;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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

    public function getTeacherData(Request $req)
    {
        if($teacher = User::with("classes")->with("section")->with("school")->find($req->teacherId))
        {
            return [
                "status" => "ok",
                "teacher" => $teacher
            ];
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }

    public function getStudentByTeacher(Request $req)
    {
        if($req->teacherId != "")
        {
            $me = User::find($req->teacherId);

            $mySchool = $me->school_id;
            $myClass = $me->class_id;
            $mySection = $me->section_id;

            $students = Student::where("school_id",$mySchool)
            ->where("class_id",$myClass)->where("section_id",$mySection)
            ->with("school")->with("class")->with("section")->orderBy("name","asc");

            return DataTables::of($students)
            ->addColumn("school",function($row){
                return $row->school->name;
            })
            ->addColumn("class",function($row){
                return $row->class->name;
            })
            ->addColumn("section",function($row){
                return $row->section->name;
            })
            ->addColumn("ratings",function($row){
                $ratings = $row->rating;

                $ratings = $row->rating;

                $totalRating = 0;
                if(count($ratings) > 0)
                {
                    $total = 0;
                    $totalPoint = 0;
                    foreach($ratings as $rating)
                    {
                        $total += 5;
                        $totalPoint += $rating->rate1;
                        $totalPoint += $rating->rate2;
                        $totalPoint += $rating->rate3;
                        $totalPoint += $rating->rate4;
                        $totalPoint += $rating->rate5;
                    }
                    $totalRating = $totalPoint/$total;
                }
                else
                {
                    $totalRating = 0;
                }
                return $totalRating . "&nbsp; <i class='fas fa-star text-warning'></i>";
            })
            ->addColumn("action",function($row){
                $html = '
                <button data-ratings="'.$row->id.'" class="btn btn-sm btn-warning text-white">See ratings <i class="fas fa-star"></i></button>
                ';
                return $html;
            })
            ->rawColumns(["action","ratings"])
            ->make(true);
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }

    public function checkUser(Request $req)
    {
        if($req->userId != "")
        {
            if($user = User::find($req->userId,["id","name","photo","photo_url"]))
            {
                $rv = null;
                $reviewFound = false;
                if($review = TeacherRating::where("teacher_id",$user->id)->where("supervisor_id",auth("supervisor")->user()->id)
                ->whereMonth("created_at","=",Carbon::now())
                ->get())
                {
                    $rv = $review;
                    if(count($review) >= 4)
                    {
                        $reviewFound = true;
                    }
                }

                $i = 0;
                $monthlyPoint = 0;
                foreach($rv as $rev)
                {
                    $i++;
                    $monthlyPoint += $rev->total;
                }

                if($i > 0)
                {
                    $monthlyPoint = round($monthlyPoint/$i,1);
                }

                return [
                    "status" => "ok",
                    "user" => $user,
                    "review" => $rv,
                    "reviewFound" => $reviewFound,
                    "monthlyPoint" => $monthlyPoint,
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
