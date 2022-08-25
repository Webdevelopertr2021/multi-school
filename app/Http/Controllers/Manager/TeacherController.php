<?php

namespace App\Http\Controllers\Manager;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\Student;
use App\Models\TeacherRating;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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

        foreach($users as $user)
        {   
            $totalRate = 0;
            $i = 0;
            foreach($user->rating as $rate)
            {
                $i++;
                $totalRate += $rate->total;
            }
            if($i > 0)
            {
                $totalRate = round($totalRate/$i, 0);
            }
            $user->rate_point = $totalRate;
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

    public function getTeacherBySuperv(Request $req)
    {
        if($superv = User::find($req->supervisorId))
        {
            $teachers = AssignedSupervisor::where("supervisor_id",$req->supervisorId)->pluck("teacher_id")->toArray();

            $teachers = User::where("role","teacher")->whereIn("id",$teachers)
            ->with("school:id,name")
            ->with("classes:id,name")
            ->with("section:id,name")
            ->with("rating")
            ->with("supervisor:id,supervisor_id,teacher_id")
            ->orderBy("name","desc");

            return DataTables::of($teachers)
            ->addColumn("school",function($row){
                $school = $row->school->name;
                return $school;
            })
            ->addColumn("class",function($row){
                $class = $row->classes["name"]??"N/A";
                return $class;
            })
            ->addColumn("section",function($row){
                $section = $row->section->name??"N/A";
                return $section;
            })
            ->addColumn("ratings",function($row){

                $now = Carbon::now();
                $last = Carbon::now()->subMonth(1);

                $currentMonth = TeacherRating::where("teacher_id",$row->id)->whereMonth("created_at",$now)->get();
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

                $lastMonth = TeacherRating::where("teacher_id",$row->id)->whereMonth("created_at",$last)->get();
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
                return $totalRating . " point &nbsp; &nbsp;" . $ratingStat;
            })
            ->addColumn("stars",function($row){
                $now = Carbon::now();
                $currentMonth = TeacherRating::where("teacher_id",$row->id)->whereMonth("created_at",$now)->get();
                $totalRating = 0;
                $i = 0;
                $star = 0;
                if(count($currentMonth) > 0)
                {
                    foreach($currentMonth as $rate)
                    {
                        $i++;
                        $totalRating += $rate->total;
                    }
                    $totalRating = round($totalRating/$i,1);
                    $star = Helper::getStars($totalRating);
                }
                return $star. "&nbsp; <i class='fas fa-star text-warning'></i>";
            })
            ->addColumn("supervisors",function($row){
                $html = "";
                foreach($row->supervisor as $super)
                {
                    $html .= "<span class='badge badge-success badge-pill mb-2'>".$super->user["name"]."</span>";
                }
                
                return $html;
            })
            ->addColumn("action",function($row){
                $html = "
                <button data-edit-teacher='$row->id' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i></button>
                <button data-delete-teacher='$row->id' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i></button>
                <button data-rate-btn='$row->id' class='btn btn-sm btn-primary'><i class='fas fa-star'></i></button>
                
                ";
                return $html;
            })
            ->addColumn("total_students",function($row){
                $students = Student::where("class_id",$row->class_id)->where("section_id",$row->section_id)->count();
                return "<a href='/manager/teacher/".$row->id."/student-list'><u>$students</u></a>";
            })
            ->rawColumns(["ratings","supervisors","action","total_students","stars"])
            ->make(true);
        }
        else
        {
            return [
                "staus" => "fail"
            ];
        }

    }

    public function getTeacherRating(Request $req)
    {
        if($teacher = User::with("school:id,name")->find($req->userId,["id","name","school_id","photo","photo_url"]))
        {
            if($req->year == "" && $req->month == "")
            {
                $ratings = TeacherRating::where("teacher_id",$teacher->id)->with("rater:id,name,photo,photo_url");

                $reviewCount = $ratings->count();
                $totalPoints = 0;
                $i=0;
                foreach($ratings->get() as $rate)
                {
                    $i++;
                    $totalPoints += $rate->total;
                }
                
                $final = 0;
                $star = 0;
                if($totalPoints > 0)
                {
                    $final = round($totalPoints/$i,1);
                    $star = Helper::getStars($final);
                }

                $ratings = $ratings->paginate(10);

                return [
                    "status" => "ok",
                    "ratings" => $ratings,
                    "teacherData" => $teacher,
                    "totalPoint" => $final,
                    "reviewCount" => $reviewCount,
                    "star" => $star,
                ];
            }
            else
            {
                $date = $req->year."-".$req->month."-"."1";
                $date = Carbon::parse($date);

                $ratings = TeacherRating::whereYear("created_at","=",$date)->whereMonth("created_at","=",$date)
                ->where("teacher_id",$teacher->id)->with("rater:id,name,photo,photo_url")->get();


                $monthlyPoint = 0;
                $i = 0;
                $star = 0;
                foreach($ratings as $rate)
                {
                    $i++;
                    $monthlyPoint += $rate->total;
                }

                if($i > 0)
                {
                    $monthlyPoint = $monthlyPoint / $i;
                    $star = Helper::getStars($monthlyPoint);
                }

                return [
                    "status" => "ok",
                    "ratings" => $ratings,
                    "monthlyRate" => $monthlyPoint,
                    "month" => $date->format("F"),
                    "star" => $star,
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
