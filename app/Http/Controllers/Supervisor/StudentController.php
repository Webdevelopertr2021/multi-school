<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentReview;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:supervisor");
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
