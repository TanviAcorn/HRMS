<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppraisalPeriod extends Model
{
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';

    protected $table = 'appraisal_periods';
    protected $primaryKey = 'i_id';
}
