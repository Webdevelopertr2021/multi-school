<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentReview extends Model
{
    use HasFactory;

    public function rater()
    {
        return $this->belongsTo(User::class,"teacher_id","id");
    }
}
