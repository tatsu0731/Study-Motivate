<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function surveyTerm()
    {
        return $this->belongsTo(SurveyTerm::class);
    }
}
