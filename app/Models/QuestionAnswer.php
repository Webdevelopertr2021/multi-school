<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    use HasFactory;

    public function qstn()
    {
        return $this->belongsTo(Question::class,"question_id","id");
    }
}
