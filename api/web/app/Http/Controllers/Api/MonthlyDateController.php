<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MonthlyDateController extends Controller
{
    public function index(Request $request, $company_id)
    {
        $survey_terms = DB::table('survey_terms')
            ->join('survey_categories', 'survey_terms.survey_category_id', '=', 'survey_categories.id')
            ->where('survey_categories.company_id', $company_id)
            ->where('survey_terms.start_date', '>=', date('Y-m-d'))
            ->select('survey_terms.id as survey_term_id', 'survey_terms.start_date')
            ->get();

        return response()->json([
            'data' => $survey_terms
        ], 200);
    }
}
