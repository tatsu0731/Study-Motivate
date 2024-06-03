<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_term_id',
        'summary',
    ];

    public function surveyTerm()
    {
        return $this->belongsTo(SurveyTerm::class);
    }
}
