<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';

    protected $table = 'appraisals';
    protected $primaryKey = 'i_id';

    protected $fillable = [
        'i_employee_id',
        'i_manager_id',
        'i_period_id',
        'vch_status',
        'vch_overall_comments',
        'dt_submitted_at',
        'i_created_by',
        'i_updated_by'
    ];

    public function ratings()
    {
        return $this->hasMany(AppraisalRating::class, 'i_appraisal_id');
    }
}
