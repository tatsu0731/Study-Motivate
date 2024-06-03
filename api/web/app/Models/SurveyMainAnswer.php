<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyMainAnswer extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function surveyPersonalAnswer()
    {
        return $this->belongsTo(SurveyPersonalAnswer::class);
    }
}
