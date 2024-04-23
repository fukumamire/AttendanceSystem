<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    //WorkBreakモデル(休憩時間)とのリレーション
    public function workBreaks()
    {
        return $this->hasMany(WorkBreak::class);
    }
    // Userモデルとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
