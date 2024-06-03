<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function surveyContent()
    {
        return $this->belongsTo(SurveyContent::class);
    }

    public function surveyTerm()
    {
        return $this->belongsTo(SurveyTerm::class);
    }

    public function surveyQuestion()
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}
