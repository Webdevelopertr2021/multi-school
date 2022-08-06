<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\Classes;
use App\Models\Section;
use App\Models\TeacherRating;
use App\Models\User;
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

            $total = 0;
            $totalPoint = 0;
            $totalRating = 0;
            foreach($row->rating as $rating)
            {
                $total += 5;
                $totalPoint += $rating->rate1;
                $totalPoint += $rating->rate2;
                $totalPoint += $rating->rate3;
                $totalPoint += $rating->rate4;
                $totalPoint += $rating->rate5;
            }
            if($total > 0)
            {
                $totalRating = $totalPoint/$total;
            }
            return $totalRating . " <i class='fas fa-star text-warning'></i>";
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
        ->rawColumns(["ratings","supervisors","action"])
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
