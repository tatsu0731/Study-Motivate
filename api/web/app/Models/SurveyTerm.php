<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyTerm extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function surveyCategory()
    {
        return $this->belongsTo(SurveyCategory::class);
    }

    public function surveyPersonalAnswers()
    {
        return $this->hasMany(SurveyPersonalAnswer::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function plan()
    {
        return $this->hasMany(Plan::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function summaries()
    {
        return $this->hasMany(Summary::class);
    }
}
