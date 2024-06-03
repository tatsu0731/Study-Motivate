<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\SurveyTerm;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function surveyQuestions(Request $request, $survey_term_id)
    {
        $survey_questions = DB::table('survey_terms as st')
            ->join('survey_categories as scat', 'st.survey_category_id', '=', 'scat.id')
            ->join('survey_contents as sc', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sc.survey_question_id', '=', 'sq.id')
            ->where('st.id', $survey_term_id)
            ->select('sc.id as question_content_id', 'survey_question_id', 'st.id as survey_term_id', 'sq.text as question')
            ->get();

        $survey_questions_monthly = DB::table('survey_terms as st')
            ->join('survey_categories as scat', 'st.survey_category_id', '=', 'scat.id')
            ->join('survey_contents as sc', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_questions as sq', 'sc.survey_question_id', '=', 'sq.id')
            ->where('st.id', 3)
            ->select('sc.id as question_content_id', 'survey_question_id', 'st.id as survey_term_id', 'sq.text as question')
            ->get();

        $survey_category = DB::table('survey_categories as scat')
            ->join('survey_terms as st', 'scat.id', '=', 'st.survey_category_id')
            ->where('st.id', $survey_term_id)
            ->first()
            ->category;

        if($survey_category === 0) {
            $survey_questions = array_merge($survey_questions->toArray(), $survey_questions_monthly->toArray());
        }

        return response()->json([
            'data' => $survey_questions
        ], 200);
    }
}
