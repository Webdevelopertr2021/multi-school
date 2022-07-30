<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestApproval extends Model
{
    use HasFactory;

    public function superv()
    {
        return $this->belongsTo(User::class,"supervisor_id","id");
    }

    public function teacher()
    {
        return $this->belongsTo(User::class,"teacher_id","id");
    }

    public function leave()
    {
        return $this->belongsTo(LeaveRequest::class,"leave_requestId","id");
    }
}

