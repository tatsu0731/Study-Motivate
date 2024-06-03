<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyContent extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function surveyCategory()
    {
        return $this->belongsTo(SurveyCategory::class);
    }

    public function surveyQuestion()
    {
        return $this->belongsTo(SurveyQuestion::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function surveyPersonalAnswer()
    {
        return $this->hasMany(SurveyPersonalAnswer::class);
    }
}
