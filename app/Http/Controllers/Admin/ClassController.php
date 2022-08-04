<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function add(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "school" => 'required|exists:schools,id'
        ]);

        $class = new Classes();
        $class->name = $req->name;
        $class->school_id = $req->school;
        $class->save();

        return [
            "status" => "ok",
            "msg" => "New class has been added",
            "data" => Classes::with("school")->find($class->id)
        ];
        
    }

    public function getList(Request $req)
    {
        if($req->schoolId == "")
        {
            $classes = Classes::orderBy("id","desc")
            ->with("school:id,name")
            ->paginate(20);
        }
        else
        {
            $classes = Classes::orderBy("id","desc")
            ->where("school_id",$req->schoolId)->get(["id","name"]);
        }
        
        return response()->json($classes);
    }

    public function delete(Request $req)
    {
        $this->validate($req,[
            "classId" => "required|exists:classes,id",
        ]);

        $data = Classes::find($req->classId);

        $data->delete();

        return [
            "status" => "ok",
            "msg" => "Class deleted successfully"
        ];
    }

    public function update(Request $req)
    {
        $this->validate($req,[
            "id" => "required|exists:classes,id",
            "name" => "required",
            "school" => 'required|exists:schools,id'
        ]);

        $class = Classes::find($req->id);
        $class->name = $req->name;
        $class->school_id = $req->school;
        $class->save();

        return [
            "status" => "ok",
            "msg" => "Class has been updated",
            "data" => Classes::with("school")->find($class->id)
        ];
    }
}
