<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function surveyContent()
    {
        return $this->hasMany(surveyContent::class);
    }

    public function surveyQuestionCategory()
    {
        return $this->belongsTo(surveyQuestionCategory::class);
    }

    public function Goal()
    {
        return $this->hasMany(Goal::class);
    }
}
