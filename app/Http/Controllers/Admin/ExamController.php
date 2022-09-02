<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        ->withCount("question")
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

    public function checkExamData(Request $req)
    {
        if($exam = Exam::with("school:id,name")->with("classes:id,name")
        ->with("section:id,name")->find($req->examId))
        {
            return [
                "status" => "ok",
                "exam" => $exam
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
            "examId" => "required",
            "name" => "required",
            "start_time" => "required",
            "schoolId" => "required",
            "classId" => "required",
            "sectionId" => "required",
            "category" => "required",
            "formla" => "required",
        ]);

        $exam = Exam::find($req->examId);
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
        $exam->status = $req->status;
        $exam->save();

        return [
            "status" => "ok",
            "msg" => "Exam updated successfully"
        ];
    }

    public function deleteExam(Request $req)
    {
        if($exam = Exam::find($req->examId))
        {
            $questions = Question::where("exam_id",$req->examId)->get();
            foreach($questions as $q)
            {
                if($q->ans_file != "")
                {
                    if(Storage::disk("Question")->exists($q->ans_file))
                    {
                        Storage::disk("Question")->delete($q->ans_file);
                    }
                }
                $q->delete();
            }
            $exam->delete();
            return [
                "status" => "ok",
                "msg" => "Exam was deleted"
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
