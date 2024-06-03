<?php

namespace App\Traits;

use App\Traits\GetCompanyIdTrait;
use Illuminate\Support\Facades\DB;

trait GetPreviousMainSurveyTermIdTrait
{
  use GetCompanyIdTrait;

  public function GetPreviousMainSurveyTermIdTrait($survey_term_id)
  {
    $company_id = $this->getCompanyId($survey_term_id);

    $previous_main_survey_term_id = DB::table('survey_terms as st')
      ->join('survey_categories as scat', 'scat.id', '=', 'st.survey_category_id')
      ->where([['company_id', $company_id], ['st.id', '<', $survey_term_id], ['scat.category', 0]])
      ->orderBy('st.id', 'desc')
      ->select('st.id as survey_term_id')
      ->first()->survey_term_id;

    if ($previous_main_survey_term_id === null) {
      return $survey_term_id;
    } else {
      return $previous_main_survey_term_id;
    }
  }
}
