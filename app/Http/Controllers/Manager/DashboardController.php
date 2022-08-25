<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:manager");
    }
    

    public function myProfile(Request $req)
    {
        if($user = User::find(auth("manager")->user()->id))
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
        $me = auth("manager")->user();

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
}
