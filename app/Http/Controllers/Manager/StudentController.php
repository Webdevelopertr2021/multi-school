<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\Student;
use App\Models\StudentReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:manager");
    }

    public function getStudents()
    {
        $manager = auth("manager")->user();

        $supervisors = User::where("role","supervisor")->where("manager_id",$manager->id)->pluck("id")->toArray();
        $teachers = AssignedSupervisor::whereIn("supervisor_id",$supervisors)->get();

        $mySchool = array();
        $myClass = array();
        $mySection = array();
        foreach($teachers as $teach)
        {
            array_push($myClass,User::find($teach->teacher_id)->class_id);
            array_push($mySection,User::find($teach->teacher_id)->section_id);
            array_push($mySchool,User::find($teach->teacher_id)->school_id);
        }
        $mySchool = array_unique($mySchool);
        $myClass = array_unique($myClass);
        $mySection = array_unique($mySection);

        $students = Student::whereIn("school_id",$mySchool)
        ->whereIn("class_id",$myClass)->whereIn("section_id",$mySection)
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
            $totalRating = 0;
            $lastMonthRating = 0;

            $i = 0;
            $l = 0;

            $now = Carbon::now();
            $last = Carbon::now()->subMonth(1);

            $currentMonth = StudentReview::where("student_id",$row->id)->whereMonth("created_at",$now)->get();

            $lastMonth = StudentReview::where("student_id",$row->id)->whereMonth("created_at",$last)->get();

            // Current month
            if(count($currentMonth) > 0)
            {
                foreach($currentMonth as $rate)
                {
                    $i++;
                    $totalRating += $rate->total;
                }
                $totalRating = round($totalRating/$i,1);
            }
            // End

            // Last month
            if(count($lastMonth) > 0)
            {
                foreach($lastMonth as $rate)
                {
                    $l++;
                    $lastMonthRating += $rate->total;
                }
                $lastMonthRating = round($lastMonthRating/$l,1);
            }
            // End

            $ratingStat = "";

            if($lastMonthRating > 0)
            {
                if($totalRating > $lastMonthRating)
                {
                    $ratingStat = '<span title="Improved | Last month : '.$lastMonthRating.'" class="text-success"><i class="fas fa-arrow-up"></i></span>';
                }
                else if($totalRating < $lastMonthRating)
                {
                    $ratingStat = '<span title="Decreased | Last month : '.$lastMonthRating.'" class="text-danger"><i class="fas fa-arrow-down"></i></span>';
                }
                else if($totalRating == $lastMonthRating)
                {
                    $ratingStat = '<span title="Unchanged | Last month : '.$lastMonthRating.'" class="text-warning"><i class="fas fa-circle"></i></span>';
                }
            }
            return $totalRating . " point &nbsp; &nbsp;" . $ratingStat;
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

    public function getRatings(Request $req)
    {
        if($student = Student::with("school:id,name")->find($req->studentId,["id","name","school_id","photo","photo_url","class_id","section_id"]))
        {
            if($req->year == "" && $req->month == "")
            {
                $ratings = StudentReview::where("student_id",$student->id)->with("rater:id,name,photo,photo_url");

                $totalPoints = 0;
                $i = 0;
                foreach($ratings->get() as $rate)
                {
                    $i ++;
                    $totalPoints += $rate->total;
                }

                $final = 0;
                $totalCount = $ratings->count();
                if($totalPoints > 0)
                {
                    $final = round($totalPoints/$i,1);
                }

                $ratings = $ratings->paginate(10);
                return [
                    "status" => "ok",
                    "ratings" => $ratings,
                    "teacherData" => $student,
                    "totalPoint" => $final,
                    "totalRatingCount" => $totalCount,
                ];
            }
            else
            {
                $date = Carbon::parse($req->year."-".$req->month."-"."1");
                $ratings = StudentReview::where("student_id",$student->id)
                ->whereYear("created_at","=",$date)->whereMonth("created_at","=",$date)
                ->with("rater:id,name,photo,photo_url")->get();

                $monthlyPoint = 0;
                $i = 0;
                foreach($ratings as $rate)
                {
                    $i++;
                    $monthlyPoint += $rate->total;
                }

                if($i > 0)
                {
                    $monthlyPoint = round($monthlyPoint/$i,1);
                }

                return [
                    "review" => $ratings,
                    "monthlyPoint" => $monthlyPoint,
                    "selectedMonth" => $date->format("F"),
                    "selectedYear" => $date->format("Y"),
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
