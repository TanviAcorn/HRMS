<?php

namespace App;

use App\BaseModel;
use App\Traits\MySoftDeletes;
use App\LookupMaster;

class SubDesignationMasterModel extends BaseModel
{
    use MySoftDeletes;

    const CREATED_AT = 'dt_created_at';
    const UPDATED_AT = 'dt_updated_at';
    const DELETED_AT = 'dt_deleted_at';

    protected $table = '';
    protected $primaryKey = 'i_id';
    protected $dates = ['dt_deleted_at'];

    public function __construct()
    {
        parent::__construct();
        $this->table = config('constants.SUB_DESIGNATION_MASTER_TABLE');
    }

    // Relation with designation (LookupMaster)
    public function designation()
    {
        return $this->belongsTo(LookupMaster::class, 'i_designation_id');
    }

    // Fetch records with optional filters and pagination
    public function getRecordDetails($where = [], $likeData = [], $additionalData = [])
    {
        $query = SubDesignationMasterModel::with(['designation']);

        if (isset($where['master_id']) && !empty($where['master_id'])) {
            $query->where('i_id', '=', $where['master_id']);
        }

        if (isset($where['active_status'])) {
            $query->where('t_is_active', $where['active_status']);
        }

        if (isset($where['designation_id']) && !empty($where['designation_id'])) {
            $query->where('i_designation_id', $where['designation_id']);
        }

        if (isset($likeData['searchBy']) && !empty($likeData['searchBy'])) {
            $searchString = $likeData['searchBy'];
            $query->where(function ($q) use ($searchString) {
                $q->orWhere('v_sub_designation_name', 'like', "%" . $searchString . "%");
            });
        }

        $query->orderBy('i_id', 'DESC');

        if (isset($where['offset'])) {
            $query->skip($where['offset']);
        }

        if (isset($where['limit'])) {
            $query->take($where['limit']);
        }

        if (isset($where['singleRecord']) && ($where['singleRecord'] != false)) {
            return $query->first();
        }

        return $query->get();
    }
}
