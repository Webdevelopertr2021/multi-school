<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:teacher");
    }

    public function getStudents()
    {
        $me = auth("teacher")->user();

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
            return 5;
        })
        ->addColumn("action",function($row){
            $html = '
            <button class="btn btn-sm btn-warning text-white">See ratings <i class="fas fa-star"></i></button>
            ';
            return $html;
        })
        ->rawColumns(["action"])
        ->make(true);

    }
}
