<?php

namespace App\Helper;

use App\Models\LeaveRequest;
use App\Models\TeacherRating;
use App\Models\User;
use Carbon\Carbon;
use App\Models\GeneralSetting;

class Helper {
    
    
    public static function getTeacherExtraSalary($teacherId,$month)
    {
        $setting = GeneralSetting::find(1);

        $month = Carbon::parse(date("Y")."/$month/02");
        $ratings = TeacherRating::where("teacher_id",$teacherId)->whereMonth("created_at",$month)->get();

        $salaryRating = 0;
        $salaryNoLeave = 0;
        $star = 0;

        if(count($ratings) > 0)
        {
            $total = 0;
            $i=0;
            foreach($ratings as $rating)
            {
                $i++;
                $total += $rating->total;
            }
            $total = round($total/$i);
            $star = self::getStars($total);
            $salaryRating = $star * $setting->bonus_per_star;
        }
        else
        {
            $salaryRating = 0;
        }

        // Leaves
        $from = Carbon::parse($month)->subMonth(4);
        $to = $month;

        $leaves = LeaveRequest::where("teacher_id",$teacherId)->whereMonth("created_at",">=",$from)
        ->whereMonth("created_at","<=",$to)->where("status","approved")
        ->where("approved_by_manager",1)->get();
        
        if(count($leaves) > 0)
        {
            $salaryNoLeave = 0;
        }
        else
        {
            $salaryNoLeave = $setting->bonus_no_leave;
        }
        // End
        

        return [
            "salary_rating" => $salaryRating,
            "rating_count" => count($ratings),
            "salary_no_leave" => $salaryNoLeave,
            "rating_stars" => 1,
            "star" => $star,
            "total_leaves" => count($leaves),

        ];
    }

    public static function getStars($score)
    {
        if($score < 50)
        {
            return 0;
        }
        else if($score < 55)
        {
            return 0.5;
        }
        else if($score < 60)
        {
            return 1;
        }
        else if($score < 65)
        {
            return 1.5;
        }
        else if( $score < 70)
        {
            return 2;
        }
        else if($score < 75)
        {
            return 2.5;
        }
        else if($score < 80)
        {
            return 3;
        }
        else if($score < 85)
        {
            return 3.5;
        }
        else if($score < 90)
        {
            return 4;
        }
        else if($score < 95)
        {
            return 4.5;
        }
        else if($score <= 100)
        {
            return 5;
        }
    }

}