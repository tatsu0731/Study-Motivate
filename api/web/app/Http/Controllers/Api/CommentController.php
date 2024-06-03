<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\SurveyContent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SurveyTerm;

class CommentController extends Controller
{
    public function commentList(Request $request, $survey_term_id)
    {
        $company_id = $this->getCompanyId($survey_term_id);
        $survey_main_term_id = DB::table('survey_terms as st')
            ->join('survey_categories as scat', 'scat.id', '=', 'st.survey_category_id')
            ->where('st.id', '<=', $survey_term_id)
            ->orderBy('st.id', 'desc')
            ->select('scat.category', 'st.id as survey_term_id', 'scat.company_id')
            ->where([['scat.category', '0'], ['scat.company_id', $company_id]])
            ->first()->survey_term_id;

        $data = DB::table('survey_contents as sc')
            ->join('survey_personal_answers as spa', 'spa.survey_content_id', '=', 'sc.id')
            ->join('survey_description_answers as sda', 'spa.id', '=', 'sda.survey_personal_answer_id')
            ->join('survey_categories as scat', 'scat.id', '=', 'sc.survey_category_id')
            ->join('survey_terms as st', 'spa.survey_term_id', '=', 'st.id')
            ->join('departments as d', 'spa.department_id', '=', 'd.id')
            ->where('st.id', '>=', $survey_main_term_id)
            ->where('st.id', '<=', $survey_term_id)
            ->select(DB::raw('DATE_FORMAT(sda.created_at, "%Y-%m-%d") as date, scat.category as survey_category, CASE WHEN age BETWEEN 20 AND 29 THEN "20代" WHEN age BETWEEN 30 AND 39 THEN "30代" WHEN age BETWEEN 40 AND 49 THEN "40代" WHEN age BETWEEN 50 AND 59 THEN "50代" WHEN age BETWEEN 60 AND 69 THEN "60代" ELSE "70代以上" END AS age, d.name as department, sda.answer as comment'))
            ->get();

        return response()->json($data, 200);
    }

    public function getCompanyId($survey_term_id)
    {
        $company_id = DB::table('survey_terms as st')
            ->join('survey_categories as scat', 'scat.id', '=', 'st.survey_category_id')
            ->where('st.id', '=', $survey_term_id)
            ->select('scat.company_id')
            ->first()->company_id;

        return $company_id;
    }
}
