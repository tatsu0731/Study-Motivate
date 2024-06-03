<?php

namespace App\Traits;

use App\Traits\GetCompanyIdTrait;
use Illuminate\Support\Facades\DB;

trait GetLatestSurveyTermIdTrait
{
  use GetCompanyIdTrait;

  public function GetLatestSurveyTermIdTrait($survey_term_id)
  {
    $company_id = $this->getCompanyId($survey_term_id);

    $data = DB::table('survey_terms as st')
      ->join('survey_categories as scat', 'scat.id', '=', 'st.survey_category_id')
      ->where([['company_id', $company_id], ['st.id', '>', $survey_term_id]])
      ->orderBy('st.id', 'asc')
      ->select('st.id as survey_term_id', 'category');

    $latest_survey_term_id = $data->get();

    if ($latest_survey_term_id->isEmpty()) {
      return $survey_term_id;
    } else {
      $latest_id = $latest_survey_term_id->where('category', 0)->first();
      if ($latest_id !== null) {
        $id = $latest_id->survey_term_id - 1;
      } else {
        $id = $latest_survey_term_id->max('survey_term_id');
      }
      return $id;
    }
  }
}
