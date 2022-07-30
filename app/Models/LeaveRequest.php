<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LeaveRequest extends Model
{
    use HasFactory;

    /**
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fromDate() : Attribute {
        
        return Attribute::make(
            get: fn ($val) => Carbon::parse($val)->format("d F Y")
        );
    }

    protected function toDate() : Attribute {
        return Attribute::make(
            get: fn ($val) => Carbon::parse($val)->format("d F Y")
        );
    }

    public function approval()
    {
        return $this->hasMany(LeaveRequestApproval::class,"leave_requestId");
    }
}
