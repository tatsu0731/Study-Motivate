<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SurveyTerm;
use App\Models\SurveyCategory;
use App\Models\SurveyQuestion;
use App\Models\SurveyContent;

class CreateMonthlySurveyController extends Controller
{
    public function store(Request $request)
    {
        $survey_category = new SurveyCategory();
        $survey_category->company_id = $request->company_id;
        $survey_category->name = $request->name;
        $survey_category->department_id = $request->department_id;
        $survey_category->frequency = $request->frequency;
        $survey_category->category = $request->category;
        $survey_category->status = $request->status;
        $survey_category->save();

        $questions = $request->text;

        foreach ($questions as $question) {
            $survey_question = new SurveyQuestion();
            $survey_question->text = $question;
            $survey_question->save();

            $survey_content = new SurveyContent();
            $survey_content->survey_category_id = $survey_category->id;
            $survey_content->survey_question_id = $survey_question->id;
            $survey_content->save();
        }

        $survey_term = SurveyTerm::where('id', $request->survey_term_id)->first();
        $survey_term->update([
            'survey_category_id' => $survey_category->id
        ]);

        return response()->json([
            'data' => [
                $survey_category,
                $survey_term
            ]
        ], 201);
    }
}
