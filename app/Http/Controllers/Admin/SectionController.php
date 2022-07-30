<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function store(Request $req)
    {
        $this->validate($req,[
            "schoolId" => "required|exists:schools,id",
            "classId" => "required|exists:classes,id",
            "name" => "required",
        ]);

        $section = new Section();
        $section->name = $req->name;
        $section->school_id = $req->schoolId;
        $section->class_id = $req->classId;
        $section->save();

        return [
            "status" => "ok",
            "msg" => "New section is created",
            "data" => Section::with("school:id,name")->with("classes:id,name")->find($section->id)
        ];

    }

    public function getList(Request $req)
    {
        if($req->classId == "")
        {
            $sections = Section::with("school:id,name")
            ->with("classes:id,name")->orderBy("name","asc")
            ->paginate(15);
        }
        else
        {
            $sections = Section::where("class_id",$req->classId)->get(["id","name"]);
        }

        return response()->json($sections);
    }

    public function delete(Request $req)
    {
        $this->validate($req,[
            "sectionId" => "required|exists:sections,id"
        ]);

        $data = Section::find($req->sectionId);
        $data->delete();

        return [
            "status" => "ok",
            "msg" => "Section was deleted"
        ];
    }

    public function getData(Request $req)
    {
        if($req->sectionId == '')
        {
            return [
                "status" => 'fail',
            ];
        }
        else
        {
            $data = Section::with("school:id,name")->with("classes:id,name")->findOrFail($req->sectionId);
            return [
                "status" => "ok",
                "section" => $data
            ];
        }
    }

    public function update(Request $req)
    {
        $this->validate($req,[
            "sectionId" => "required|exists:sections,id",
            "schoolId" => "required|exists:schools,id",
            "classId" => "required|exists:classes,id",
            "name" => "required",
        ]);

        $section = Section::find($req->sectionId);
        $section->name = $req->name;
        $section->school_id = $req->schoolId;
        $section->class_id = $req->classId;
        $section->save();

        return [
            "status" => "ok",
            "msg" => "Section was updated",
        ];

    }
}
