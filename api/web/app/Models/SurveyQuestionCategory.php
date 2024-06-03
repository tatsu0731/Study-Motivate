<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestionCategory extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'name',
    ];

    public function surveyQuestions()
    {
        return $this->hasMany(SurveyQuestion::class);
    }
}
