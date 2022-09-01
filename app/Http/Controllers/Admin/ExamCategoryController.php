<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamCategory;
use Illuminate\Http\Request;

class ExamCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function getCategory()
    {
        $cats = ExamCategory::orderBy("name","asc")->get();
        return response()->json($cats);
    }
}
