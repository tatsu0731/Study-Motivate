<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyPersonalAnswer extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function surveyContent()
    {
        return $this->belongsTo(surveyContent::class);
    }

    public function surveyMainAnswer()
    {
        return $this->hasMany(surveyMainAnswer::class);
    }

    public function surveyMonthlyAnswer()
    {
        return $this->hasMany(surveyMonthlyAnswer::class);
    }

    public function surveyDescriptionAnswer()
    {
        return $this->hasMany(surveyDescriptionAnswer::class);
    }

    public function Employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function Department()
    {
        return $this->belongsTo(Department::class);
    }

    public function SurveyTerm()
    {
        return $this->belongsTo(SurveyTerm::class);
    }
}
