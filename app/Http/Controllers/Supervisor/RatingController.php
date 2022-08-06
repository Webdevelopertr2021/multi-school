<?php

namespace App\Http\Controllers\SuperVisor;

use App\Http\Controllers\Controller;
use App\Models\TeacherRating;
use App\Models\User;
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
        $rating->total = ($req->rate1 + $req->rate2 + $req->rate3 + $req->rate4 + $req->rate5) / 5;
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
            $ratings = TeacherRating::where("teacher_id",$teacher->id)->with("rater:id,name,photo,photo_url")->paginate(6);

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
            
            $final = 0;
            if($totalPoints > 0)
            {
                $final = $totalPoints/$totalRates;
            }

            return [
                "status" => "ok",
                "ratings" => $ratings,
                "teacherData" => $teacher,
                "totalPoint" => $final,
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
