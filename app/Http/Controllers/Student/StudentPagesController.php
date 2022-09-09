<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentPagesController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:student");
    }

    public function index()
    {
        return view("backend.student.index");
    }
}
