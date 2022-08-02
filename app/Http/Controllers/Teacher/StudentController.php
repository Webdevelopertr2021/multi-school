<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentReview;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:teacher");
    }

    public function getStudents()
    {
        $me = auth("teacher")->user();

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

    public function checkStudent(Request $req)
    {
        if($req->studentId != "")
        {
            if($student = Student::find($req->studentId))
            {
                $rv = null;
                $reviewFound = false;
                if($review = StudentReview::where("student_id",$student->id)->where("teacher_id",auth("teacher")->user()->id)->first())
                {
                    $rv = $review;
                    $reviewFound = true;
                }
                return [
                    "status" => "ok",
                    "student" => $student,
                    "review" => $rv,
                    "reviewFound" => $reviewFound,
                ];
            }
            else
            {
                return [
                    "status" => "fail",
                    "msg" => "Student not found"
                ];    
            }
        }
        else
        {
            return [
                "status" => "fail",
                "msg" => "Invalid input",
                "id" => $req->all()
            ];
        }
    }

    public function submitReview(Request $req)
    {
        $this->validate($req,[
            "studentId" => "required|numeric|exists:users,id",
            "rate1" => "required|min:1|max:10",
            "rate2" => "required|min:1|max:10",
            "rate3" => "required|min:1|max:10",
            "rate4" => "required|min:1|max:10",
            "rate5" => "required|min:1|max:10"
        ]);

        $review = new StudentReview();
        $review->rate1 = $req->rate1;
        $review->rate2 = $req->rate2;
        $review->rate3 = $req->rate3;
        $review->rate4 = $req->rate4;
        $review->rate5 = $req->rate5;
        $review->student_id = $req->studentId;
        $review->teacher_id = auth("teacher")->user()->id;
        $review->save();
        
        return [
            "status" => "ok",
            "msg" => "Your review has been submitted"
        ];

    }

    public function getRatings(Request $req)
    {
        if($student = Student::with("school:id,name")->find($req->studentId,["id","name","school_id","photo","photo_url","class_id","section_id"]))
        {
            $ratings = StudentReview::where("student_id",$student->id)->with("rater:id,name,photo,photo_url")->paginate(6);

            $totalPoints = 0;
            $totalRates = 0;
            foreach($ratings as $rate)
            {
                $totalRates += 5;
                $totalPoints += $rate->rate1;
                $totalPoints += $rate->rate2;
                $totalPoints += $rate->rate3;
                $totalPoints += $rate->rate4;
                $totalPoints += $rate->rate5;
            }

            return [
                "status" => "ok",
                "ratings" => $ratings,
                "teacherData" => $student,
                "totalPoint" => $totalPoints/$totalRates,
            ];
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }
}
