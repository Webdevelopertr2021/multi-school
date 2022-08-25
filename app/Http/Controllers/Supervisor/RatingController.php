<?php

namespace App\Http\Controllers\SuperVisor;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\TeacherRating;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:supervisor");
    }

    public function submit(Request $req)
    {
        $this->validate($req,[
            "teacherId" => "required|numeric|exists:users,id",
            "rate1" => "required|min:1|max:10",
            "rate2" => "required|min:1|max:10",
            "rate3" => "required|min:1|max:10",
            "rate4" => "required|min:1|max:10",
            "rate5" => "required|min:1|max:10"
        ]);

        $rating = null;
        
        $msg = "Review submitted";
        $rating = new TeacherRating();
        $rating->teacher_id = $req->teacherId;
        $rating->supervisor_id = auth("supervisor")->user()->id;
        $rating->rate1 = $req->rate1;
        $rating->rate2 = $req->rate2;
        $rating->rate3 = $req->rate3;
        $rating->rate4 = $req->rate4;
        $rating->rate5 = $req->rate5;
        $rating->rate6 = $req->rate6;
        $rating->total = ($req->rate1 + $req->rate2 + $req->rate3 + $req->rate4 + $req->rate5 + $req->rate6);
        $rating->feedback = $req->feedback;
        $rating->save();

        return [
            "status" => "ok",
            "msg" => $msg,
        ];

        
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
