<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

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
}
