<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');

Route::group(['middleware' => 'auth:api'],function(){    
    Route::get('coach/get_coach/{id}', 'Api\CoachController@getCoachById');
    Route::get('coach/get_coach_achievements/{id}', 'Api\CoachController@getAllAchievementByIdCoach');
    
    Route::post('coach/update_coach/{id}', 'Api\CoachController@updateCoach');
    Route::put('coach/change_password', 'Api\CoachController@changePassword');

    Route::post('coach/add_achievement', 'Api\CoachController@addAchievement');
    Route::post('coach/update_achievement/{id}', 'Api\CoachController@updateAchievement');
    Route::get('coach/delete_achievement/{id}', 'Api\CoachController@deleteAchievement');

    Route::get('coach/get_coaching_clinics', 'Api\CoachingClinicController@getAllCoachingClinic');
    Route::get('coach/get_coaching_clinic/{id}', 'Api\CoachingClinicController@getCoachingClinicById');
    Route::get('coach/get_coach_coaching_clinics/{id}', 'Api\CoachingClinicController@getAllCoachingClinicByIdCoach');
    Route::get('coach/get_coaching_clinic_schedules/{id}', 'Api\CoachingClinicController@getAllScheduleByIdCoachingClinic');
    Route::get('coach/get_coaching_clinic_modules/{id}', 'Api\CoachingClinicController@getAllModuleByIdCoachingClinic');
    
    Route::post('coach/add_coaching_clinic', 'Api\CoachingClinicController@addCoachingClinic');
    Route::post('coach/update_coaching_clinic/{id}', 'Api\CoachingClinicController@updateCoachingClinic');
    Route::get('coach/delete_coaching_clinic/{id}', 'Api\CoachingClinicController@deleteCoachingClinic');

    Route::post('coach/add_schedule_coaching_clinic', 'Api\CoachingClinicController@addScheduleCoachingClinic');
    Route::put('coach/update_schedule_coaching_clinic/{id}', 'Api\CoachingClinicController@updateScheduleCoachingClinic');
    Route::get('coach/delete_schedule_coaching_clinic/{id}', 'Api\CoachingClinicController@deleteScheduleCoachingClinic');

    Route::post('coach/add_module_coaching_clinic', 'Api\CoachingClinicController@addModule');
    Route::get('coach/delete_module_coaching_clinic/{id}', 'Api\CoachingClinicController@deleteModule');

    Route::get('coach/get_coach_payment_coaching_clinics/{id}', 'Api\PaymentController@getAllPaymentByIdCoach');
    Route::get('coach/get_coach_total_rate/{id}', 'Api\CoachAssessmentController@getTotalRateCoachByIdCoach');
    Route::get('coach/get_coach_assessments/{id}', 'Api\CoachAssessmentController@getAllAssessmentByIdCoach');

    Route::put('coach/confirm_payment_coaching_clinic/{id}', 'Api\PaymentController@confirmPayment');

    Route::get('coach/get_dashboard/{id}', 'Api\PaymentController@getDashboardCoach');
    Route::get('coach/get_report_transaction_dt/{dtF}_{dtL}/{id}', 'Api\PaymentController@getReportTransactionsBasedOnDate');
    Route::get('coach/get_report_income_yr/{year}/{id}', 'Api\PaymentController@getReportIncomeInYear');
    Route::get('coach/get_report_activity_coaching_clinic_dt/{dtF}_{dtL}/{id}', 'Api\CoachingClinicController@getReportActivityCoachingClinic');    

    Route::get('coach/logout','Api\AuthController@logout');
});

Route::group(['middleware' => 'auth:participant-api'],function(){
    Route::get('participant/get_coachs', 'Api\CoachController@getAllCoach');
    Route::get('participant/get_coach/{id}', 'Api\CoachController@getCoachById');
    Route::get('participant/get_coaching_clinic_schedules/{id}', 'Api\CoachingClinicController@getAllScheduleByIdCoachingClinic');
    Route::get('participant/get_coach_achievements/{id}', 'Api\CoachController@getAllAchievementByIdCoach');
    Route::get('participant/get_active_coaching_clinics/{category}', 'Api\CoachingClinicController@getAllActiveCoachingClinic');
    Route::get('participant/get_coach_coaching_clinics/{id}', 'Api\CoachingClinicController@getAllCoachingClinicByIdCoach');
    Route::get('participant/get_coaching_clinic/{id}', 'Api\CoachingClinicController@getCoachingClinicById');

    Route::get('participant/get_participants', 'Api\ParticipantController@getAllParticipant');
    Route::get('participant/get_participant/{id}', 'Api\ParticipantController@getParticipantById');

    Route::post('participant/update_participant/{id}', 'Api\ParticipantController@updateParticipant');
    Route::put('participant/change_password', 'Api\ParticipantController@changePassword');

    Route::get('participant/get_register_coaching_clinic/{id}', 'Api\RegisterCoachingClinicController@getRegisterCoachingClinicById');
    Route::get('participant/get_participant_register_coaching_clinics/{id}', 'Api\RegisterCoachingClinicController@getAllRegisterCoachingClinicByIdParticipant');

    Route::post('participant/add_register_coaching_clinic', 'Api\RegisterCoachingClinicController@addRegisterCoachingClinic');
    Route::post('participant/update_register_coaching_clinic/{id}', 'Api\RegisterCoachingClinicController@updateRegisterCoachingClinic');
    Route::get('participant/delete_register_coaching_clinic/{id}', 'Api\RegisterCoachingClinicController@deleteRegisterCoachingClinic');    
    
    Route::get('participant/get_participant_payment_coaching_clinics/{id}', 'Api\PaymentController@getAllPaymentByIdParticipant');
    Route::get('participant/get_payment_coaching_clinic/{id}', 'Api\PaymentController@getPaymentById');
    Route::post('participant/add_payment_coaching_clinic', 'Api\PaymentController@addPayment');
    Route::post('participant/update_payment_coaching_clinic/{id}', 'Api\PaymentController@updatePayment');    

    Route::get('participant/get_participant_my_coaching_clinics/{id}', 'Api\MyCoachingClinicController@getAllMyCoachingClinicByIdParticipant');    

    Route::get('participant/get_assessments', 'Api\CoachAssessmentController@getAllAssessment');
    Route::get('participant/get_assessment/{id}', 'Api\CoachAssessmentController@getAssessmentByIdMyCoachingClinic');
    Route::get('participant/get_coach_assessments/{id}', 'Api\CoachAssessmentController@getAllAssessmentByIdCoach');    
    Route::post('participant/add_coach_assessment', 'Api\CoachAssessmentController@addAssessment');

    Route::get('participant/logout','Api\AuthController@logout');
});

Route::group(['middleware' => 'auth:admin-api'],function(){
    Route::get('admin/verify_coach/{id}','Api\AdminController@verifyCoach');
    
    Route::get('admin/get_coachs','Api\AdminController@getAllCoach');    
    Route::get('admin/get_coaching_clinics','Api\AdminController@getAllCoachingClinic');    
    
    Route::get('admin/get_report_activity_coaching_clinic_dt/{dtF}_{dtL}/{id}', 'Api\CoachingClinicController@getReportActivityCoachingClinic');
    Route::get('admin/get_report_coachs_register_dt/{dtF}_{dtL}', 'Api\CoachController@getReportListCoachBasedOnRegisterDate');
    Route::get('admin/get_report_participants_register_dt/{dtF}_{dtL}', 'Api\ParticipantController@getReportListParticipantBasedOnRegisterDate');
    
    Route::get('admin/get_assessments_month', 'Api\AdminController@getListAssessmentInMonth');
    Route::get('admin/get_dashboard', 'Api\AdminController@getDashboardAdmin');
    
    Route::get('admin/logout','Api\AuthController@logout');
});
