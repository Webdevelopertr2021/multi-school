<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function store(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "phone" => "required|unique:students,phone",
            "email" => "nullable|unique:students,email",
            "schoolId" => "required|exists:schools,id",
            "classId" => "required|exists:classes,id",
            "sectionId" => "required|exists:sections,id"
        ]);

        $student = new Student();
        $student->name = $req->name;
        $student->phone = $req->phone;
        $student->email = $req->email;
        $student->school_id = $req->schoolId;
        $student->class_id = $req->classId;
        $student->section_id = $req->sectionId;
        $student->address = $req->address;

        if($req->hasFile("photo"))
        {
            $file = $req->file("photo");
            $new_name = time()."_".rand().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$new_name,"StudentPhotos");
            $photo_url = route("student-photo",["filename"=>$new_name]);
            $student->photo = $new_name;
            $student->photo_url = $photo_url;
        }

        $student->save();

        return [
            "status" => "ok",
            "msg" => "Student was added successfully"
        ];
    }

    public function getStudentList()
    {
        $students = Student::with(["school:id,name","class:id,name","section:id,name"])->orderBy("id","desc");

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
            ->addColumn("action",function($row){
                $html = "<button data-student-edit='$row->id' class='ml-2 mb-2 btn btn-sm btn-warning'><i class='fas fa-edit'></i></button>";
                $html .= "<button data-student-delete='$row->id' class='ml-2 mb-2 btn btn-sm btn-danger'><i class='fas fa-trash'></i></button>";
                return $html;
            })
            ->rawColumns(["action"])
            ->make(true);
    }

    public function delete(Request $req)
    {
        $this->validate($req,[
            "studentId" => "required|exists:students,id"
        ]);

        $data = Student::find($req->studentId);

        if($data->photo != "")
        {
            if(Storage::disk("StudentPhotos")->exists($data->photo))
            {
                Storage::disk("StudentPhotos")->delete($data->photo);
            }
        }

        $data->delete();

        return [
            "status" => "ok",
            "msg" => "Student record was deleted"
        ];
    }

    public function getData(Request $req)
    {
        if($data = Student::with("school")->with("class")->with("section")->find($req->studentId))
        {
            $classes = Classes::where("school_id",$data->school_id)->get();
            $sections = Section::where("class_id",$data->class_id)->get();
            return [
                "status" => "ok",
                "student" => $data,
                "classes" => $classes,
                "section" => $sections,
                
            ];
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }

    public function update(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "phone" => "required|unique:students,phone,$req->studentId,id",
            "email" => "nullable|unique:students,email,$req->studentId,id",
            "schoolId" => "required|exists:schools,id",
            "classId" => "required|exists:classes,id",
            "sectionId" => "required|exists:sections,id"
        ]);

        $student = Student::find($req->studentId);
        $student->name = $req->name;
        $student->phone = $req->phone;
        $student->email = $req->email;
        $student->school_id = $req->schoolId;
        $student->class_id = $req->classId;
        $student->section_id = $req->sectionId;
        $student->address = $req->address;

        if($req->hasFile("photo"))
        {
            if($student->photo != "")
            {
                if(Storage::disk("StudentPhotos")->exists($student->photo))
                {
                    Storage::disk("StudentPhotos")->delete($student->photo);
                }
            }

            $file = $req->file("photo");
            $new_name = time()."_".rand().".".$file->getClientOriginalExtension();
            $file_path = $file->storeAs("",$new_name,"StudentPhotos");
            $photo_url = route("student-photo",["filename"=>$new_name]);
            $student->photo = $new_name;
            $student->photo_url = $photo_url;
        }

        $student->save();

        return [
            "status" => "ok",
            "msg" => "Student was added successfully"
        ];
    }
}
