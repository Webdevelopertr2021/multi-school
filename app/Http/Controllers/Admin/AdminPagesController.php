<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignedSupervisor;
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

    public function myProfile(Request $req)
    {
        if($user = User::find(auth()->user()->id))
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
        $me = auth()->user();

        $this->validate($req,[
            "name" => "required",
            "phone" => "required|unique:users,phone,$me->id,id",
            "email" => "nullable|unique:users,email,$me->id,id",
            "password" => "nullable|min:8",
            "photo" => "nullable|mimes:jpg,jpeg,png,JPG,JPEG,PNG",
        ]);

        $user = User::find($me->id);
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
                if(Storage::disk("UserPhotos")->exists($user->photo))
                {
                    Storage::disk("UserPhotos")->delete($user->photo);
                }
            }
            $file = $req->file("photo");
            $newName = rand()."_".time().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$newName,"UserPhotos");
            $user->photo = $newName;
            $user->photo_url = route("user-photo",["filename" => $newName]);
        }

        $user->save();
        return [
            "status" => "ok",
            "msg" => "Profile was updated"
        ];
    }
    public function managerList(Request $req)
    {
        $admins = User::where("role","manager")
        ->where("id","!=",auth()->user()->id)
        ->get();

        return response()->json($admins);
    }

    public function createManager(Request $req)
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
        $admin->role = "manager";
        $admin->password = bcrypt($req->password);

        if($req->accessToLeave == true)
        {
            $admin->teacher_application_permission = 1;
        }
        else
        {
            $admin->teacher_application_permission = null;
        }

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
            "msg" => "Manager created"
        ];
    }

    public function updateManager(Request $req)
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
        $admin->role = "manager";

        if($req->password != "")
        {
            $admin->password = bcrypt($req->password);
        }

        if($req->accessToLeave == true)
        {
            $admin->teacher_application_permission = 1;
        }
        else
        {
            $admin->teacher_application_permission = null;
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
            "msg" => "Manager updated"
        ];
    }


    public function getTreeData()
    {
        $managers = User::where("role","manager")->get(["id","name","photo","photo_url"]);

        foreach($managers as $manager)
        {
            $supervisors = User::where("role","supervisor")->where("manager_id",$manager->id)
            ->get(["id","name","photo","photo_url"]);

            foreach($supervisors as $super)
            {
                $assigned = AssignedSupervisor::where("supervisor_id",$super->id)->pluck("teacher_id")->toArray();
                $teachers = User::where("role","teacher")->whereIn("id",$assigned)->get(["id","name","photo","photo_url"]);
                $super->teachers = $teachers;
            }

            $manager->supervisors = $supervisors;
        }

        return response()->json($managers);
    }
    
}
