<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeFeedbackOneMonth extends Model
{
    protected $table = 'employee_feedback_one_month';
    protected $primaryKey = 'i_id';
    public $timestamps = false; // Disable automatic timestamps since the table doesn't have created_at/updated_at columns

    protected $fillable = [
        'v_employee_name',
        'v_emp_code',
        'v_department',
        'v_designation',
        'd_date_of_joining',
        'i_understand_onboarding_process',
        'i_understand_company_policy',
        'i_well_trained_about_process',
        'i_aware_department_process',
        'i_trained_for_responsibilities',
        'i_interested_and_motivated',
        'i_responsibilities_assigned',
        'i_feel_welcomed_by_team',
        'i_team_bonding',
        'i_team_motivates',
        'i_comfortable_giving_feedback',
        'i_manager_supportive',
        'i_learn_from_manager',
        'i_understand_goals',
        'b_joining_designation',
        'b_joining_doj',
        'b_joining_id_card',
        'b_joining_bank_account',
        'b_doc_appointment_letter',
        'b_doc_list_of_holidays',
        'b_doc_hrms_login',
        'b_team_leader_intro',
        'b_team_intro',
        'b_teamwork_allocation',
        'b_team_satisfaction',
        'b_work_satisfaction',
        't_suggestion',
        'dt_created_at',
        'dt_updated_at'
    ];
}
