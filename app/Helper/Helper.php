<?php

namespace App\Helper;

use App\Models\TeacherRating;
use App\Models\User;

class Helper {
    
    public static function getTeacherExtraSalary($teacherId,$month)
    {
        $ratings = TeacherRating::where("teacher_id",$teacherId)->whereMonth("created_at",$month)->get();

        $salaryRating = 0;
        $salaryNoLeave = 0;
        $star = 0;

        if(count($ratings) > 3)
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
            $salaryRating = $star * 10;
        }
        else
        {
            $salaryRating = 0;
        }
        

        return [
            "salary_rating" => $salaryRating,
            "rating_count" => count($ratings),
            "salary_no_leave" => $salaryNoLeave,
            "rating_stars" => 1,
            "star" => $star,
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