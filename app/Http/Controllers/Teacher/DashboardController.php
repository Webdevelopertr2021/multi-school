<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:teacher");
    }

    public function getDashboardData()
    {
        $me = auth("teacher")->user();

        $user = User::with(["school:id,name","classes:id,name","section:id,name"])->find($me->id);

        $school = School::find($me->school_id);

        $students = Student::where("school_id",$me->school_id)->where("class_id",$me->class_id)->count();

        return [
            "user" => $user,
            "school" => $school,
            "student" => $students,
        ];

    }
}
