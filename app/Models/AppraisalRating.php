<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppraisalRating extends Model
{
    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';

    protected $table = 'appraisal_ratings';
    protected $primaryKey = 'i_id';

    protected $fillable = [
        'i_appraisal_id',
        'vch_type',
        'i_reference_id',
        'i_rating',
        'vch_comments'
    ];
}
