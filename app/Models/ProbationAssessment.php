<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProbationAssessment extends Model
{
    protected $table = 'probation_assessments';
    protected $primaryKey = 'i_id';
    public $timestamps = true;

    protected $fillable = [
        'i_employee_id',
        'i_manager_id',
        'i_leave_in_probation',
        'i_quality_score', 'vch_quality_remarks',
        'i_efficiency_score', 'vch_efficiency_remarks',
        'i_attendance_score', 'vch_attendance_remarks',
        'i_teamwork_score', 'vch_teamwork_remarks',
        'i_communication_score', 'vch_communication_remarks',
        'i_competency_score', 'vch_competency_remarks',
        'e_objectives_met', 'vch_objectives_details',
        'e_training_addressed', 'vch_training_details',
        'vch_decision', 'i_extend_months', 'dt_extend_upto_date',
        'vch_status',
        'i_created_by', 'i_updated_by',
    ];
}
