<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\Student;
use App\Models\User;
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
}
