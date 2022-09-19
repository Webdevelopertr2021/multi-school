<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use App\Models\TeacherRating;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function list(Request $req)
    {
        $teachers = User::where("role","teacher")
        ->with("school:id,name")
        ->with("classes:id,name")
        ->with("section:id,name")
        ->with("rating")
        ->with("supervisor:id,supervisor_id,teacher_id")
        ->orderBy("name","desc");

        // return $teachers;

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
            return $star . "&nbsp; <i class='fas fa-star text-warning'></i>";
        })
        ->addColumn("supervisors",function($row){
            $html = "";
            foreach($row->supervisor as $super)
            {
                $html .= "<span class='badge badge-success badge-pill mb-2'>".$super->user["name"]."</span>";
            }
            
            return $html;
        })
        ->addColumn("salary",function($row){
            return $row->salary." $";
        })
        ->addColumn("bonus_money",function($row){

            $lastMonth = Carbon::now();
            $salaryData = Helper::getTeacherExtraSalary($row->id,$lastMonth->format("m"));
            return "<span class='c-hand' title='Rating Bonus'>".$salaryData["salary_rating"]."</span>" . "$ + <span class='c-hand' title='No leave'>".$salaryData["salary_no_leave"]."</span>$";
        })
        ->addColumn("total_salary",function($row){
            $lastMonth = Carbon::now();
            $salaryData = Helper::getTeacherExtraSalary($row->id,$lastMonth->format("m"));
            return ($row->salary + $salaryData["salary_rating"] + $salaryData["salary_no_leave"])." $";
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
            return "<a href='/admin/teacher/$row->id/studetn-list'><u>$students</u></a>";
        })
        ->rawColumns(["ratings","supervisors","action","total_students","stars","bonus_money"])
        ->make(true);

    }

    public function delete(Request $req)
    {
        $this->validate($req,[
            "teacherId" => "required|exists:users,id"
        ]);

        $teacher = User::find($req->teacherId);

        if($teacher->photo != "")
        {
            if(Storage::disk("UserPhotos")->exists($teacher->photo))
            {
                Storage::disk("UserPhotos")->delete($teacher->photo);
            }
        }

        $teacher->delete();

        return [
            "status" => "ok",
            "msg" => "Teacher was deleted successfully"
        ];
    }

    public function getTeacherInfo(Request $req)
    {
        if($data = User::with("school:id,name")->with("classes:id,name")->with("section:id,name")->find($req->teacherId))
        {
            $class = Classes::where('school_id',$data->school_id)->get(["id","name"]);
            $sections = Section::where("class_id",$data->class_id)->get(["id","name"]);
            $supervisors = User::where("school_id",$data->school_id)->where("role","supervisor")->get(["id","name"]);
            $selectedSuperVisors = AssignedSupervisor::where("teacher_id",$data->id)->with("user:id,name")->get()->pluck("user")->toArray();
            return [
                "status" => "ok",
                "selectedSchool" => $data->school,
                "teacher" => $data,
                "classes" => $class,
                "selectedClass" => $data->classes,
                "sections" => $sections,
                "selectedSection" => $data->section,
                "supervisors" => $supervisors,
                "selectedSupervisors" => $selectedSuperVisors,
            ];
        }
        else
        {
            return [
                "status" => "fail"
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
                return "<a href='/admin/teacher/$row->id/studetn-list'><u>$students</u></a>";
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
}
