<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttendance;
use App\Models\Question;
use App\Models\QuestionAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

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
            $participent = ExamAttendance::where("exam_id",$exam->id)->count();
            return [
                "status" => "ok",
                "exam" => $exam,
                "participent" => $participent,
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

    public function getReport(Request $req)
    {
        if($exam = Exam::find($req->examId))
        {
            $students = ExamAttendance::where("exam_id",$exam->id)->with("students:id,name")->get();
            
            return Datatables::of($students)
            ->addColumn("student_id",function($row){
                return $row->students->id;
            })
            ->addColumn("name",function($row) {
                return $row->students->name;
            })
            ->addColumn("total_question",function($row){
                return Question::where("exam_id",$row->exam_id)->count();
            })
            ->addColumn("correct",function($row){
                $ans = QuestionAnswer::where("exam_id",$row->exam_id)
                ->where("student_id",$row->student_id)->where("is_correct",1)->count();
                return $ans;
            })
            ->addColumn("full_mark",function($row){
                $qs = Question::where("exam_id",$row->exam_id)->get(["id","marks"]);
                $mark = 0;
                foreach($qs as $q)
                {
                    $mark += $q->marks;
                }
                return $mark;
            })
            ->addColumn("obtain_mark",function($row){
                $ans = QuestionAnswer::where("exam_id",$row->exam_id)
                ->where("student_id",$row->student_id)->where("is_correct",1)
                ->with('qstn:id,marks')
                ->get();
                $mark = 0;
                foreach($ans as $an)
                {
                    $mark += $an->qstn->marks;
                }
                return $mark;
            })
            ->addColumn("attend_time",function($row){
                if($row->attendance_time != "")
                {
                    return Carbon::parse($row->attendance_time)->format("d M y - h:i A");
                }
                else
                {
                    return "";
                }
                
            })
            ->addColumn("submission_time",function($row){
                if($row->finish_time != "")
                {
                    return Carbon::parse($row->finish_time)->format("d M y - h:i A");
                }
                else
                {
                    return "";
                }
                
            })
            ->addColumn("action",function($row){
                $html = '<button data-result data-student="'.$row->student_id.'" data-exam="'.$row->exam_id.'" class="btn btn-sm btn-primary">See Details</button>';
                return $html;
            })
            ->rawColumns(["action"])
            ->make(true);
        }
        else
        {
            abort(404);
        }
    }

    public function getResult(Request $req)
    {
        if($exam = ExamAttendance::where("exam_id",$req->exam)->where("student_id",$req->student)->with(["students","exam"])->first())
        {
            $totalMark = 0;
            $qs = QuestionAnswer::where("exam_id",$req->exam)->where("student_id",$req->student)
            ->where("is_correct",1)->with("qstn:id,marks")->get();
            foreach($qs as $q)
            {
                $totalMark += $q->qstn->marks;
            }

            return [
                "status" => "ok",
                "exam" => $exam,
                "totalMark" => $totalMark,
            ];
        }
        else
        {
            return [
                "status" => "fail"
            ];
        }
    }

    public function getResultData(Request $req)
    {
        if($exam = ExamAttendance::where("exam_id",$req->examId)->where("student_id",$req->student)->first())
        {

            $questions = QuestionAnswer::where('exam_id',$req->examId)
            ->where("student_id",$req->student)->with("qstn:id,marks")->get();
            foreach($questions as $i=>$q)
            {
                $q->index = $i+1;
            }

            return Datatables::of($questions)
            ->addColumn("question",function($row){
                return "<span class='deg'>Question $row->index</span>";
            })
            ->addColumn("answer",function($row){
                return "<b>$row->answer</b>";
            })
            ->addColumn("status",function($row){
                if($row->is_correct == 1) {
                    return "<span class='text-success'>Correct <i class='fas fa-check-circle'></i></span>";
                }
                else {
                    return "<span class='text-danger'>Wrong <i class='fas fa-times-circle'></i></span>";
                }
            })
            ->addColumn("correct",function($row){
                return "<b>$row->correct_ans</b>";
            })
            ->addColumn("mark",function($row){
                if($row->is_correct == 1) {
                    return $row->qstn->marks;
                }
                else {
                    return 0;
                }
            })
            ->addColumn("total_time",function($row){
                return $row->total_time_to_ans. " seconds";
            })
            ->addColumn("is_submit",function($row){
                if($row->status == 'submited') {
                    return "<span class='badge badge-success badge-pill'>Submited</span>";
                }
                else {
                    return "<span class='badge badge-danger badge-pill'>Not Submited</span>";
                }
            })
            ->rawColumns(["question","answer","status","correct","is_submit"])
            ->make(true);
        }
        else
        {
            abort(404);
        }
    }
    
    public function copy(Request $req)
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

        $oldExam = Exam::find($req->examId);

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
        $exam->status = $req->status;
        $exam->save();

        // Copy Questions
        $oldQuestions = Question::where("exam_id",$oldExam->id)->get();
        foreach($oldQuestions as $question)
        {
            $newQuestion = $question->replicate();
            $newQuestion->exam_id = $exam->id;
            $newQuestion->save();
        }
        // End

        return [
            "status" => "ok",
            "msg" => "Exam Copied successfully",
            "examId" => $exam->id,
        ];
    }
}
