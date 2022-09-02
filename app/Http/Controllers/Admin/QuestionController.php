<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function storeQuestion(Request $req)
    {
        $this->validate($req,[
            "examId" => "required|numeric|exists:exams,id",
            "body" => "required",
            "marks" => "required",
            "correctAns" => "required"
        ]);
        $question = new Question();
        $question->exam_id = $req->examId;
        $question->body = $req->body;
        $question->marks = $req->marks;
        $question->correct_ans = $req->correctAns;

        if($req->hasFile("ansFile"))
        {
            $file = $req->file("ansFile");
            $new_name = "ans_script_".rand()."_".time().".".$file->getClientOriginalExtension();
            $path = $file->storeAs("",$new_name,"Question");
            $question->ans_file = $new_name;
        }
        $question->save();
        return [
            "status" => "ok",
            "question" => $question,
            "msg" => "Question addedd successfully"
        ];
    }

    public function delete(Request $req)
    {
        $this->validate($req,[
            "questionId" => "required|numeric|exists:questions,id"
        ]);

        $question = Question::find($req->questionId);

        if($question->ans_file != "")
        {
            if(Storage::disk("Question")->exists($question->ans_file))
            {
                Storage::disk("Question")->delete($question->ans_file);
            }
        }

        $question->delete();

        return [
            "status" => "ok",
            "msg" => "Question removed"
        ];

    }
}
