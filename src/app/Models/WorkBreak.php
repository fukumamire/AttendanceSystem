<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkBreak extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'attendance_id', 'start_break', 'end_break'];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
