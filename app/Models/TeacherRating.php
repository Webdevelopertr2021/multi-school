<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherRating extends Model
{
    use HasFactory;

    public function rater()
    {
        return $this->belongsTo(\App\Models\User::class,"supervisor_id");
    }
}
