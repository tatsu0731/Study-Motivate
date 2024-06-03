<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait GetCompanyIdTrait
{
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
