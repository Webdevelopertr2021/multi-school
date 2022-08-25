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
        ->get();

        foreach($users as $user)
        {
            $now = Carbon::now();
            $last = Carbon::now()->subMonth(1);

            $currentMonth = TeacherRating::where("teacher_id",$user->id)->whereMonth("created_at",$now)->get();
            $totalRating = 0;
            $i = 0;
            if(count($currentMonth) > 0)
            {
                foreach($currentMonth as $rate)
                {
                    $i++;
                    $totalRating += $rate->total;
                }
                $totalRating = round($totalRating/$i,1);
            }

            $lastMonth = TeacherRating::where("teacher_id",$user->id)->whereMonth("created_at",$last)->get();
            $lastRating = 0;
            $l = 0;
            if(count($lastMonth) > 0)
            {
                foreach($lastMonth as $rate)
                {
                    $l++;
                    $lastRating += $rate->total;
                }
                $lastRating = round($lastRating/$l,1);
            }

            $ratingStat = "";

            if($lastRating > 0)
            {
                if($totalRating > $lastRating)
                {
                    $ratingStat = '<span title="Improved | Last month : '.$lastRating.'" class="text-success"><i class="fas fa-arrow-up"></i></span>';
                }
                else if($totalRating < $lastRating)
                {
                    $ratingStat = '<span title="Decreased | Last month : '.$lastRating.'" class="text-danger"><i class="fas fa-arrow-down"></i></span>';
                }
                else if($totalRating == $lastRating)
                {
                    $ratingStat = '<span title="Unchanged | Last month : '.$lastRating.'" class="text-warning"><i class="fas fa-circle"></i></span>';
                }
            }
            $user->rating_stat = $ratingStat;
            $user->rating_this_month = $totalRating;

        }


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
