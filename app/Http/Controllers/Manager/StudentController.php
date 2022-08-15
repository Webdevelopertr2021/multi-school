<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\Student;
use App\Models\User;
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
}
