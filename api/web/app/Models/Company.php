<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function surveyCategories()
    {
        return $this->hasMany(SurveyCategory::class);
    }
}
