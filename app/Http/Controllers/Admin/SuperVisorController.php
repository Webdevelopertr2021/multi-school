<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
use App\Models\Student;
use App\Models\StudentReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class SuperVisorController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function addSupervisor(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "email" => "nullable|unique:users,email",
            "phone" => "required|unique:users,phone",
            "password" => "required|min:8",
            "pp" => "nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG",
        ]);

        $user = new User();
        $user->name = $req->name;
        $user->email = $req->email;
        $user->phone = $req->phone;
        $user->role = $req->role;
        $user->school_id = $req->school;
        $user->password = bcrypt($req->password);
        if($req->accessToLeave == true)
        {
            $user->teacher_application_permission = 1;
        }
        else
        {
            $user->teacher_application_permission = null;
        }
        if($req->hasFile("pp"))
        {
            $userPhoto = $req->file("pp");
            $new_name = "user_".$req->name."_".time().".".$userPhoto->getClientOriginalExtension();
            $file_path = $userPhoto->storeAs("",$new_name,"UserPhotos");
            $photo_url = route("user-photo",["filename" => $new_name]);
            $user->photo = $new_name;
            $user->photo_url = $photo_url;
        }
        $user->save();
        return [
            "status" => "ok",
            "msg" => "New supervisor user has been created"
        ];
    }

    public function updateSupervisor(Request $req)
    {
        $this->validate($req,[
            "school" => "required|numeric|exists:schools,id",
            "name" => "required",
            "email" => "nullable|unique:users,email,$req->userId,id",
            "phone" => "required|unique:users,phone,$req->userId,id",
            "password" => "nullable|min:8",
            "pp" => "nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG",
        ]);

        $user = User::find($req->userId);
        $user->name = $req->name;
        $user->email = $req->email;
        $user->phone = $req->phone;
        $user->role = $req->role;
        $user->school_id = $req->school;
        if($req->accessToLeave == true)
        {
            $user->teacher_application_permission = 1;
        }
        else
        {
            $user->teacher_application_permission = null;
        }
        if($req->password!='')
        {
            $user->password = bcrypt($req->password);
        }
        $newPhotoUrl = null;
        if($req->hasFile("pp"))
        {
            // Delete old file
            if($user->photo != "")
            {
                if(Storage::disk("UserPhotos")->exists($user->photo))
                {
                    Storage::disk("UserPhotos")->delete($user->photo);
                }
            }
            // End

            $userPhoto = $req->file("pp");
            $new_name = "user_".$req->name."_".time().".".$userPhoto->getClientOriginalExtension();
            $file_path = $userPhoto->storeAs("",$new_name,"UserPhotos");
            $photo_url = route("user-photo",["filename" => $new_name]);
            $user->photo = $new_name;
            $user->photo_url = $photo_url;
            $newPhotoUrl=  $photo_url;
        }
        $user->save();
        return [
            "status" => "ok",
            "msg" => "Profile has been updated",
            "photo" => $newPhotoUrl,
        ];
    }

    public function deleteSupervisor(Request $req)
    {
        $this->validate($req,[
            "userId" => "required|exists:users,id"
        ]);

        $user = User::find($req->userId);
        if($user->role == "supervisor")
        {
            if($user->photo != "")
            {
                if(Storage::disk("UserPhotos")->exists($user->photo))
                {
                    Storage::disk("UserPhotos")->delete($user->photo);
                }
            }
            AssignedSupervisor::where("supervisor_id",$user->id)->delete();

            $user->delete();
            return [
                "status" => "ok",
                "msg" => "Supervisor deleted"
            ];
        }
        else
        {
            return [
                "status" => "fail",
                "msg" => "Illegal operations"
            ];
        }
    }

    public function get(Request $req)
    {
        $school = $req->schoolId;
        $data = User::where("role","supervisor");
        // if($school != "")
        // {
        //     $data->where("school_id",$school);
        // }
        $data = $data->get(['id',"name"]);

        return response()->json($data);
    }

    public function addTeacher(Request $req)
    {
        $this->validate($req,[
            "school" => "required|numeric|exists:schools,id",
            "name" => "required",
            "email" => "nullable|unique:users,email",
            "phone" => "required|unique:users,phone",
            "password" => "required|min:8",
            "pp" => "nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG",
            "superVisors" => "required",
            "classId" => "required|exists:classes,id",
            "sectionId" => "required|exists:sections,id"
        ]);

        $user = new User();
        $user->name = $req->name;
        $user->email = $req->email;
        $user->phone = $req->phone;
        $user->role = "teacher";
        $user->school_id = $req->school;
        $user->password = bcrypt($req->password);
        $user->class_id = $req->classId;
        $user->section_id = $req->sectionId;
        $user->salary = $req->salary;
        if($req->credit_without_salary != "" )
        {
            $user->credit_without_salary = $req->credit_without_salary;
        }

        if($req->credit_time != "")
        {
            $user->credit_time = $req->credit_time;
        }
        

        if($req->hasFile("pp"))
        {
            $userPhoto = $req->file("pp");
            $new_name = "user_".$req->name."_".time().".".$userPhoto->getClientOriginalExtension();
            $file_path = $userPhoto->storeAs("",$new_name,"UserPhotos");
            $photo_url = route("user-photo",["filename" => $new_name]);
            $user->photo = $new_name;
            $user->photo_url = $photo_url;
        }
        $user->save();

        $supervisors = json_decode($req->superVisors,true);

        foreach($supervisors as $superv)
        {
            AssignedSupervisor::insert([
                "teacher_id" => $user->id,
                "supervisor_id" => $superv["id"],
            ]);
        }
        return [
            "status" => "ok",
            "msg" => "New teacher has been created",
        ];
    }

    public function updateTeacher(Request $req)
    {
        $this->validate($req,[
            "teacherId" => "required|exists:users,id",
            "schoolId" => "required|numeric|exists:schools,id",
            "name" => "required",
            "email" => "nullable|unique:users,email,$req->teacherId,id",
            "phone" => "required|unique:users,phone,$req->teacherId,id",
            "password" => "nullable|min:8",
            "pp" => "nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG",
            "superVisors" => "required",
            "classId" => "required|exists:classes,id",
            "sectionId" => "required|exists:sections,id"
        ]);


        $user = User::find($req->teacherId);
        $user->name = $req->name;
        $user->email = $req->email;
        $user->phone = $req->phone;
        $user->school_id = $req->schoolId;

        if($req->password != "")
        {
            $user->password = bcrypt($req->password);
        }

        $user->class_id = $req->classId;
        $user->section_id = $req->sectionId;
        if($req->hasFile("pp"))
        {
            if($user->photo != "")
            {
                if(Storage::disk("UserPhotos")->exists($user->photo))
                {
                    Storage::disk("UserPhotos")->delete($user->photo);
                }
            }

            $userPhoto = $req->file("pp");
            $new_name = "user_".$req->name."_".time().".".$userPhoto->getClientOriginalExtension();
            $file_path = $userPhoto->storeAs("",$new_name,"UserPhotos");
            $photo_url = route("user-photo",["filename" => $new_name]);
            $user->photo = $new_name;
            $user->photo_url = $photo_url;
        }
        $user->save();

        $supervisors = json_decode($req->superVisors,true);

        $record = AssignedSupervisor::where("teacher_id",$user->id)->delete();
        foreach($supervisors as $superv)
        {
            AssignedSupervisor::insert([
                "teacher_id" => $user->id,
                "supervisor_id" => $superv["id"],
            ]);
        }
        return [
            "status" => "ok",
            "msg" => "Teacher data has been updated",
        ];
    }

    public function getList(Request $req)
    {
        $search = $req->search;

        $data = User::where("role","supervisor")->orderBy("name","asc");
        if($search != "")
        {
            $data->where("name","like","%$search%")->orWhere("phone",$search)->orWhere("email",$search);
        }
        $data = $data->paginate(15);

        return response()->json($data);
    }

    public function getUser(Request $req)
    {
        if($req->userId != "")
        {
            if($user = User::find($req->userId))
            {
                return [
                    "status" => "ok",
                    "user" => $user
                ];
            }
            else
            {
                return [
                    "status" => "fail",
                    "msg" => "User not found"
                ];
            }
        }
        else
        {
            return response("User not found",404);
        }
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
                $totalRating = 0;
                $lastMonthRating = 0;

                $i = 0;
                $l = 0;

                $now = Carbon::now();
                $last = Carbon::now()->subMonth(1);

                $currentMonth = StudentReview::where("student_id",$row->id)->whereMonth("created_at",$now)->get();

                $lastMonth = StudentReview::where("student_id",$row->id)->whereMonth("created_at",$last)->get();

                // Current month
                if(count($currentMonth) > 0)
                {
                    foreach($currentMonth as $rate)
                    {
                        $i++;
                        $totalRating += $rate->total;
                    }
                    $totalRating = round($totalRating/$i,1);
                }
                // End

                // Last month
                if(count($lastMonth) > 0)
                {
                    foreach($lastMonth as $rate)
                    {
                        $l++;
                        $lastMonthRating += $rate->total;
                    }
                    $lastMonthRating = round($lastMonthRating/$l,1);
                }
                // End

                $ratingStat = "";

                if($lastMonthRating > 0)
                {
                    if($totalRating > $lastMonthRating)
                    {
                        $ratingStat = '<span title="Improved | Last month : '.$lastMonthRating.'" class="text-success"><i class="fas fa-arrow-up"></i></span>';
                    }
                    else if($totalRating < $lastMonthRating)
                    {
                        $ratingStat = '<span title="Decreased | Last month : '.$lastMonthRating.'" class="text-danger"><i class="fas fa-arrow-down"></i></span>';
                    }
                    else if($totalRating == $lastMonthRating)
                    {
                        $ratingStat = '<span title="Unchanged | Last month : '.$lastMonthRating.'" class="text-warning"><i class="fas fa-circle"></i></span>';
                    }
                }
                return $totalRating . " point &nbsp; &nbsp;" . $ratingStat;
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
