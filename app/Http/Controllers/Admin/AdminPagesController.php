<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPagesController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function index()
    {
        return view("backend.admin.index");
    }

    public function getDashboardData()
    {
        $students = Student::count();

        $teachers = User::where("role","teacher")->count();

        $supervisor = User::where("role","supervisor")->count();

        $admins = User::where("role","super")->count();

        $schools = School::count();

        return [
            "totalStudent" => $students,
            "totalTeacher" => $teachers,
            "totalSuper" => $supervisor,
            "totalAdmin" => $admins,
            "totalSchool" => $schools
        ];
    }

    public function adminList(Request $req)
    {
        $admins = User::where("role","super")
        ->where("id","!=",auth()->user()->id)
        ->get();

        return response()->json($admins);
    }

    public function createAdmin(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "phone" => "required|unique:users,phone",
            "email" => "nullable|unique:users,email",
            "password" => "required|min:8",
            "photo" => "nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG",
        ]);

        $admin = new User();
        $admin->name = $req->name;
        $admin->phone = $req->phone;
        $admin->email = $req->email;
        $admin->role = "super";
        $admin->password = bcrypt($req->password);

        if($req->hasFile("photo"))
        {
            $file = $req->file("photo");
            $newName = rand()."_".time().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$newName,"UserPhotos");
            $admin->photo = $newName;
            $admin->photo_url = route("user-photo",["filename" => $newName]);
        }

        $admin->save();

        return [
            "status" => "ok",
            "msg" => "Super admin created"
        ];
    }

    public function deleteAdmin(Request $req)
    {
        $this->validate($req,[
            "adminId" => "required|exists:users,id"
        ]);

        $admin = User::find($req->adminId);

        if($admin->role == "super")
        {
            if($admin->photo != "")
            {
                if(Storage::disk("UserPhotos")->exists($admin->photo))
                {
                    Storage::disk("UserPhotos")->delete($admin->photo);
                }
            }
            $admin->delete();
            return [
                "status" => "ok",
                "msg" => "Admin was deleted"
            ];
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }

    public function getAdminData(Request $req)
    {
        if($req->adminId != "")
        {
            if($admin = User::find($req->adminId))
            {
                return [
                    "status" => "ok",
                    "user" => $admin
                ];
            }
            else
            {
                return [
                    "status" => "fail",
                    "msg" => "ok"
                ];
            }
            
        }
        else
        {
            return [
                "status" => "fail",
            ];
        }
    }

    public function updateAdmin(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "phone" => "required|unique:users,phone,$req->adminId,id",
            "email" => "nullable|unique:users,email,$req->adminId,id",
            "password" => "nullable|min:8",
            "photo" => "nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG",
        ]);

        $admin = User::find($req->adminId);
        $admin->name = $req->name;
        $admin->phone = $req->phone;
        $admin->email = $req->email;
        $admin->role = "super";

        if($req->password != "")
        {
            $admin->password = bcrypt($req->password);
        }

        if($req->hasFile("photo"))
        {
            if($admin->photo != "")
            {
                if(Storage::disk("UserPhotos")->exists($admin->photo))
                {
                    Storage::disk("UserPhotos")->delete($admin->photo);
                }
            }
            $file = $req->file("photo");
            $newName = rand()."_".time().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$newName,"UserPhotos");
            $admin->photo = $newName;
            $admin->photo_url = route("user-photo",["filename" => $newName]);
        }

        $admin->save();

        return [
            "status" => "ok",
            "msg" => "Super admin updated"
        ];
    }
}
