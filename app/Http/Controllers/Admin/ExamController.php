<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function saveExam(Request $req)
    {
        $this->validate($req,[
            "name" => "required",
            "start_time" => "required",
            "schoolId" => "required",
            "classId" => "required",
            "sectionId" => "required",
            "category" => "required",
            "formla" => "required",
        ]);

        $exam = new Exam();
        $exam->title = $req->name;
        $exam->start_time = $req->start_time;
        $exam->end_time = $req->end_time;
        $exam->school_id = $req->schoolId;
        $exam->class_id = $req->classId;
        $exam->section_id = $req->sectionId;
        $exam->category_id = $req->category;
        $exam->user_id = auth()->user()->id;
        $exam->created_by = auth()->user()->name;
        $exam->formla = $req->formla;
        $exam->save();

        return [
            "status" => "ok",
            "examId" => $exam->id,
            "msg" => "Exam created successfully"
        ];
    }

    public function getExamList()
    {
        $exams = Exam::orderBy("id","desc")
        ->with("school:id,name")->with("classes:id,name")
        ->with("section:id,name")
        ->paginate(15);
        return response()->json($exams);
    }

    public function getExamData(Request $req)
    {
        if($exam = Exam::with("school:id,name")->with("classes:id,name")
        ->with("section:id,name")->find($req->examId))
        {
            $questions = Question::where("exam_id",$exam->id)->orderBy("id","asc")->get();

            return [
                "status" => "ok",
                "examData" => $exam,
                "questions" => $questions,
            ];
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }

}
