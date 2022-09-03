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

    public function save(Request $req)
    {
        $this->validate($req,[
            "name" => "required"
        ]);

        $cat = new ExamCategory();
        $cat->name = $req->name;
        $cat->save();

        return [
            "status" => "ok",
            "msg" => "Category created",
            "cat" => $cat
        ];
    }

    public function update(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "catId" => "required|exists:exam_categories,id"
        ]);

        $cat = ExamCategory::find($req->catId);
        $cat->name = $req->name;
        $cat->save();

        return [
            "status" => "ok",
            "msg" => "Category updated",
            "cat" => $cat
        ];
    }

    public function delete(Request $req)
    {
        $this->validate($req,[
            "catId" => "required|exists:exam_categories,id",
        ]);
        $cat = ExamCategory::find($req->catId);
        $cat->delete();
        return [
            "status" => "ok",
            "msg" => "Category deleted"
        ];
    }
}
