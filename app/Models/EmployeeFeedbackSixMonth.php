<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class EmployeeFeedbackSixMonth extends Model
{
    protected $table = 'employee_feedback_six_month';
    protected $primaryKey = 'i_id';
    public $timestamps = false;
 
    protected $fillable = [
        'v_emp_code',
        'v_employee_name',
        'v_department_name',
        'v_designation',
        'dt_date_of_joining',
        'dt_date_of_assessment',
        'i_teamwork_collaboration',
        'i_team_communication',
        'i_team_support',
        't_team_issues_conflicts',
        'i_manager_guidance',
        'i_manager_feedback_timely',
        'i_team_meeting_effective',
        'i_efforts_recognized',
        'i_understand_mission',
        'i_company_culture_respect',
        'i_internal_communication',
        'i_growth_opportunities',
        'i_career_progression',
        'i_worklife_balance',
        'i_manager_guidance_rating',
        'i_meeting_effectiveness_rating',
        'i_manager_satisfaction_rating',
        't_improvement_suggestions',
        'b_growth_opportunities_available',
        't_growth_opportunities_other',
        't_productivity_suggestions',
        'dt_created_at',
        'dt_updated_at',
    ];
}