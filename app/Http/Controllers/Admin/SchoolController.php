<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function store(Request $req)
    {
        $this->validate($req,[
            "name" => "required|unique:schools,name",
            "schoolCode" => "nullable|unique:schools,code",
        ]);

        $school = new School();

        $school->name = $req->name;
        $school->address = $req->address;
        $school->code = $req->schoolCode;
        $school->save();
        return [
            "status" => "ok",
            "msg" => "School addedd successfully"
        ];
    }

    public function getList(Request $req)
    {
        
        $data = School::orderBy("name","asc");
        if($req->search != "")
        {
            $data->where("name","like","%$req->search%")
            ->orWhere("code","$req->search");
        }
        $data = $data->paginate($req->perPage);
        
        return response()->json($data);
    }

    public function update(Request $req)
    {
        $this->validate($req,[
            "id" => "required|exists:schools,id",
            "name" => "required|unique:schools,name,$req->id,id",
            "schoolCode" => "nullable|unique:schools,code,$req->id,id",
        ],[
            "schoolCode.unique" => "This code is already taken"
        ]);

        $data = School::find($req->id);
        $data->name = $req->name;
        $data->address = $req->address;
        $data->code = $req->schoolCode;
        $data->save();
        return [
            "status" => "ok",
            "msg" => "Updated successfull",
            "newData" => $data
        ];
    }

    public function delete(Request $req)
    {
        $this->validate($req,[
            "schoolId" => "required|numeric|exists:schools,id"
        ]);

        $data = School::find($req->schoolId);
    
        // Delete teachers and supervisors
        $users = User::where("school_id",$req->schoolId)->whereIn("role",["supervisor","teacher"])->get();
        foreach($users as $user)
        {
            if($user->photo != "")
            {
                if(Storage::disk("UserPhotos")->exists("/$user->photo"))
                {
                    Storage::disk("UserPhotos")->delete("/$user->photo");
                }
            }
            $user->delete();
        }
        // 

        $data->delete();
        return [
            "status" => "ok",
            "msg" => "School data was deleted"
        ];
    }

    public function getData(Request $req)
    {
        if($req->schoolId != "")
        {
            if($data = School::find($req->schoolId))
            {
                $superVisors = User::where("role","supervisor")->where("school_id",$req->schoolId)->get();
                $teachers = User::where("role","teacher")->with("rating")->where("school_id",$req->schoolId)->get();
                
                return [
                    "status" => "ok",
                    "schoolData" => $data,
                    "supervisors" => $superVisors,
                    "teacher" => $teachers

                ];
            }
            else
            {
                return [
                    "status" => "fail"
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

    public function getAllSchool()
    {
        $data = School::orderBy("name","asc")->get(["id","name"]);
        return response()->json($data);
    }
}
