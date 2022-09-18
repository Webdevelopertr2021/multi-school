<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttendance extends Model
{
    use HasFactory;

    public function students()
    {
        return $this->belongsTo(Student::class,"student_id","id");
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class,"exam_id","id");
    }

    public function question()
    {
        return $this->hasMany(QuestionAnswer::class,"exam_id","exam_id");
    }
}
