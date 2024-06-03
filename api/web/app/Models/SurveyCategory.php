<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyCategory extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        // 'name',
    ];

    public function surveyContents()
    {
        return $this->hasMany(SurveyContent::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function surveyTerm()
    {
        return $this->hasMany(SurveyTerm::class);
    }
}
