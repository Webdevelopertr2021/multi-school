<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:student");
    }

    public function myProfile(Request $req)
    {
        if($user = Student::find(auth("student")->user()->id))
        {
            return [
                "status" => "ok",
                "user" => $user,
            ];
        }
        else
        {
            return [
                "status" => "fail",
            ];
        }
    }

    public function updateMyProfile(Request $req)
    {
        $me = auth("student")->user();

        $this->validate($req,[
            "name" => "required",
            "phone" => "required|unique:students,phone,$me->id,id",
            "email" => "nullable|unique:students,email,$me->id,id",
            "password" => "nullable|min:8",
            "photo" => "nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG",
        ]);

        $user = Student::find($me->id);
        $user->name = $req->name;
        $user->phone = $req->phone;
        $user->email = $req->email;
        
        if($req->password != "")
        {
            $user->password = bcrypt($req->password);
        }

        if($req->hasFile("photo"))
        {
            if($user->photo != "")
            {
                if(Storage::disk("StudentPhotos")->exists($user->photo))
                {
                    Storage::disk("StudentPhotos")->delete($user->photo);
                }
            }
            $file = $req->file("photo");
            $newName = rand()."_".time().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$newName,"StudentPhotos");
            $user->photo = $newName;
            $user->photo_url = route("student-photo",["filename" => $newName]);
        }

        $user->save();
        return [
            "status" => "ok",
            "msg" => "Profile was updated"
        ];
    }

    public function getDashboardData(Request $req)
    {
        $profile = Student::with(["school","class","section"])->find(auth("student")->user()->id);
        
        return [
            "profile" => $profile,
        ];
    }
}
