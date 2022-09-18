<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttendance;
use App\Models\Question;
use App\Models\QuestionAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:student");
    }

    public function getUpcoming()
    {
        $student = auth("student")->user();

        $exams = Exam::where("school_id",$student->school_id)->where("class_id",$student->class_id)->where("section_id",$student->section_id)
        ->where("end_time",">=",Carbon::now())->with("question:id,exam_id,marks")->where("status","published")->withCount("question")->get();

        foreach($exams as $exam)
        {
            $start_time = Carbon::parse($exam->start_time);
            $end_time = Carbon::parse($exam->end_time);
            // Start Time
            $text = "";
            if($start_time->isToday())
            {
                $text = "Today - ".$start_time->format("h:i A");
            }
            else if($start_time->isTomorrow())
            {
                $text = "Tomorrow - ".$start_time->format("h:i A");
            }
            else
            {
                $text = $start_time->format("d M Y, h:i A");
            }
            $exam->start_text = $text;
            // 

            // Total time
            $exam->total_time = $start_time->diff($end_time)->format("%h.%i Hours");

            // Total marks
            $totalMarks = 0;
            foreach($exam->question as $q)
            {
                $totalMarks += $q->marks;
            }
            $exam->total_marks = $totalMarks;
        }

        return $exams;
    }

    public function attendExam(Request $req)
    {
        if($exam = Exam::find($req->examId))
        {
            // check if student is eligible for the exam
            $student = auth("student")->user();
            if($exam->school_id == $student->school_id && $exam->class_id == $student->class_id && $exam->section_id == $student->section_id)
            {

                $examStatus = "";
                $startTime = Carbon::parse($exam->start_time);
                $endTime = Carbon::parse($exam->end_time);
                $questions = [];
                if(Carbon::now()->greaterThanOrEqualTo($startTime))
                {
                    // check if exam already given or not
                    if($examAttend = ExamAttendance::where("student_id",$student->id)->where("exam_id",$exam->id)->first())
                    {
                        $questions = QuestionAnswer::where("student_id",$student->id)->where("exam_id",$exam->id)->with("qstn")->get();
                    }
                    else
                    {
                        $questions = $this->createExamForStudent($student->id,$exam->id);
                    }

                    // check exam status
                    
                    if($exam->end_time != "")
                    {
                        if(Carbon::now()->greaterThan($endTime))
                        {
                            $examStatus = "time_up";
                        }
                        else
                        {
                            $examStatus = "ok";
                        }
                    }
                    else
                    {
                        $examStatus = "ok";
                    }
                    // end
                }
                else
                {
                    $examStatus = "not_started";
                }

                return [
                    "status" => "ok",
                    "examData" => $exam,
                    "questions" => $questions,
                    "examStatus" => $examStatus,
                ];
            }
            else
            {
                return [
                    "status" => "not_allowed",
                    "msg" => "You are not allowed to attend this exam",
                ];
            }
            // end

        }
        else
        {
            return [
                "status" => "exam_not_found",
                "msg" => "The exam doesn't exist anymore"
            ];
        }
    }

    private function createExamForStudent($studentId,$examId)
    {
        // Assign the student for the exam
        // assign attendance
        $examAttend = new ExamAttendance();
        $examAttend->exam_id = $examId;
        $examAttend->student_id = $studentId;
        $examAttend->attendance_time = Carbon::now();
        $examAttend->save();

        // Assign the questions
        $questions = Question::where('exam_id',$examId)->get();
        
        foreach($questions as $q)
        {
            $data = QuestionAnswer::insert([
                "student_id" => $studentId,
                "exam_id" => $examId,
                "question_id" => $q->id,
                "correct_ans" => $q->correct_ans,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);
        }
        $answerPaper = QuestionAnswer::where("student_id",$studentId)->where("exam_id",$examId)->with("qstn")->get();
        // End
        return $answerPaper;
    }

    public function submitAnswer(Request $req)
    {
        $this->validate($req,[
            "answerId" => "required|numeric|exists:question_answers,id",
            "nowAns" => "required",
        ],[
            "nowAns" => "Must write your answer here..."
        ]);

        $answer = QuestionAnswer::find($req->answerId);
        
        $resp = array();

        $answer->answer = $req->nowAns;
        
        if($req->nowAns == $answer->correct_ans)
        {
            $answer->total_tries = $req->tries;
            $answer->total_time_to_ans = $req->timer;
            $answer->is_correct = true;
            $answer->status = "submited";

            $resp["status"] = "correct";
            $resp["msg"] = "Good Job | Your answer was correct";
        }
        else
        {
            $answer->total_tries = $req->tries;
            $answer->total_time_to_ans += $req->timer;
            $answer->is_correct = false;

            $resp["status"] = "incorrect";
            $resp["msg"] = "Your answer was wrong. Try again";
        }
        $answer->save();
        
        if($req->isLast == true)
        {
            $exam = ExamAttendance::where("exam_id",$answer->exam_id)->where("student_id",$answer->student_id)->first();
            $exam->is_finished = 1;
            $exam->finish_time = Carbon::now();
        }

        return response()->json($resp);

    }

    public function getMyExam()
    {
        $studentId = auth("student")->user()->id;

        $exams = ExamAttendance::where("student_id",$studentId)->orderBy("id","desc")
        ->with("exam:id,title,start_time")->withCount('question')
        ->get();

        foreach($exams as $exam)
        {
            $totalMarks = (integer) Question::where("exam_id",$exam->exam_id)->sum("marks");
            $exam->total_mark = $totalMarks;

            $correctAns = QuestionAnswer::where("exam_id",$exam->exam_id)->where("student_id",$studentId)->where("is_correct",1)
            ->with("qstn:id,marks")->get();
            $myMarks = 0;
            foreach($correctAns as $ans)
            {
                $myMarks+= $ans->qstn->marks;
            }
            $exam->my_marks = $myMarks;
            $exam->correct_ans = count($correctAns);
            $exam->wrong_ans = count(QuestionAnswer::where("exam_id",$exam->exam_id)->where("student_id",$studentId)->where("is_correct",0)->get(["id"]));
        }

        return $exams;
    }

    public function getResult(Request $req)
    {
        $studentId = auth("student")->user()->id;
        
        if($exam = ExamAttendance::where("exam_id",$req->exam)->where("student_id",$studentId)->with(["students","exam"])->first())
        {
            $totalMark = 0;
            $qs = QuestionAnswer::where("exam_id",$req->exam)->where("student_id",$studentId)
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
        $studentId = auth("student")->user()->id;

        if($exam = ExamAttendance::where("exam_id",$req->examId)->where("student_id",$studentId)->first())
        {

            $questions = QuestionAnswer::where('exam_id',$req->examId)
            ->where("student_id",$studentId)->with("qstn:id,marks")->get();
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
}
