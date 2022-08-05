<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:supervisor");
    }

    public function getDashboardData()
    {
        $me = auth("supervisor")->user();

        $schoolInfo = School::find($me->school_id);

        $teacher = User::where('role',"teacher")->where("school_id",$me->school_id)->count();

        return [
            "status" => "ok",
            "school" => $schoolInfo,
            "user" => $me,
            "totalTeacher" => $teacher,
        ];
    }
}
