<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        "name",
        "phone",
        "email",
        "address",
        "school_id",
        "class_id",
        "section_id",
        "photo",
        "photo_url"
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function school()
    {
        return $this->belongsTo(School::class,"school_id");
    }

    public function class()
    {
        return $this->belongsTo(Classes::class,"class_id");
    }

    public function section()
    {
        return $this->belongsTo(Section::class,"section_id");
    }

    public function rating()
    {
        return $this->hasMany(StudentReview::class,"student_id","id");
    }
}
