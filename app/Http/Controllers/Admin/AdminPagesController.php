<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

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
}
