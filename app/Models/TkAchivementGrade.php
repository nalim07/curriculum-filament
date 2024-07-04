<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TkAchivementGrade extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false;

    public function memberClassSchool()
    {
        return $this->belongsTo(MemberClassSchool::class);
    }

    public function point()
    {
        return $this->belongsTo(TkPoint::class, 'tk_point_id');
    }
}
