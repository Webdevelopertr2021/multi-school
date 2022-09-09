<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttendance;
use App\Models\Question;
use App\Models\QuestionAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        ->where("end_time",">=",Carbon::now())->with("question:id,exam_id,marks")->withCount("question")->get();

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

                
                // check if exam already given or not
                if($examAttend = ExamAttendance::where("student_id",$student->id)->where("exam_id",$exam->id)->first())
                {
                    $questions = QuestionAnswer::where("student_id",$student->id)->where("exam_id",$exam->id)->with("qstn")->get();
                }
                else
                {
                    $questions = $this->createExamForStudent($student->id,$exam->id);
                }

                return [
                    "status" => "ok",
                    "examData" => $exam,
                    "questions" => $questions,
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
        return response()->json($resp);

    }
    
}
