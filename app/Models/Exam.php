<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    public function school()
    {
        return $this->belongsTo(School::class,"school_id","id");
    }

    public function classes()
    {
        return $this->belongsTo(Classes::class,"class_id","id");
    }

    public function section()
    {
        return $this->belongsTo(Section::class,"section_id","id");
    }
}
