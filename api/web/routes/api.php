<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ChartController;
use App\Http\Controllers\Api\RecordController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ComparisonController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\SurveyTermController;
use App\Http\Controllers\Api\EmployeeCSVController;
use App\Http\Controllers\Api\MonthlyChartController;
use App\Http\Controllers\Api\MonthlySurveyController;
use App\Http\Controllers\Api\SurveyContentController;
use App\Http\Controllers\Api\SurveyCategoryController;
use App\Http\Controllers\Api\SurveyQuestionController;
use App\Http\Controllers\Api\SurveyMainAnswerController;
use App\Http\Controllers\Api\SurveyMonthlyAnswerController;
use App\Http\Controllers\Api\SurveyPersonalAnswerController;
use App\Http\Controllers\Api\SurveyQuestionCategoryController;
use App\Http\Controllers\Api\SurveyDescriptionAnswerController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\MonthlyDateController;
use App\Http\Controllers\Api\CreateMonthlySurveyController;
use App\Http\Controllers\Api\StatusController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum', 'verified'])->get('/user', function (Request $request) {
    return $request->user();
});

// ログインと新規登録のルーティング
Route::post('/register', 'App\Http\Controllers\AuthController@register');
Route::post('/login', 'App\Http\Controllers\AuthController@login');

// 登録招待メール送信のルーティング
Route::post('/send-invitation-email', [InvitationController::class, 'sendInvitationEmail']);

// CRUDのルーティング
Route::apiResource('companies', CompanyController::class);
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('admins', AdminController::class);
Route::apiResource('departments', DepartmentController::class);
Route::apiResource('survey_categories', SurveyCategoryController::class);
Route::apiResource('survey_terms', SurveyTermController::class);
Route::apiResource('survey_contents', SurveyContentController::class);
Route::apiResource('survey_personal_answers', SurveyPersonalAnswerController::class);
Route::apiResource('survey_main_answers', SurveyMainAnswerController::class);
Route::apiResource('survey_monthly_answers', SurveyMonthlyAnswerController::class);
Route::apiResource('survey_description_answers', SurveyDescriptionAnswerController::class);
Route::apiResource('survey_questions', SurveyQuestionController::class);
Route::apiResource('survey_question_categories', SurveyQuestionCategoryController::class);
Route::apiResource('reports', ReportController::class);
Route::apiResource('plans', PlanController::class);
Route::apiResource('goals', GoalController::class);
Route::apiResource('status', StatusController::class);

Route::post('/user', function (Request $request) {
    return $request->user();
});
