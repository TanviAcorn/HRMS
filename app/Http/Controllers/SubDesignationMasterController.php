<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Twt\Wild_tiger;
use App\SubDesignationMasterModel;
use App\LookupMaster;

class SubDesignationMasterController extends MasterController
{
    public function __construct()
    {
        parent::__construct();
        $this->crudModel = new SubDesignationMasterModel();
        $this->moduleName = 'Sub Designation';
        $this->perPageRecord = config('constants.PER_PAGE');
        $this->defaultPage = config('constants.DEFAULT_PAGE_INDEX');
        $this->tableName = config('constants.SUB_DESIGNATION_MASTER_TABLE');
        $this->folderName = config('constants.ADMIN_FOLDER') . 'sub-designation-master/';
        $this->redirectUrl = config('constants.SUB_DESIGNATION_MASTER_URL');
    }

    public function index()
    {
        $data = [];
        $data['pageTitle'] = 'Sub Designation Master';
        $page = $this->defaultPage;

        $whereData = $paginationData = [];

        if ($page == $this->defaultPage) {
            $totalRecords = count($this->crudModel->getRecordDetails($whereData));
            $lastPage = ceil($totalRecords / $this->perPageRecord);
            $paginationData['current_page'] = $this->defaultPage;
            $paginationData['per_page'] = $this->perPageRecord;
            $paginationData['last_page'] = $lastPage;
        }
        $whereData['limit'] = $this->perPageRecord;

        $data['recordDetails'] = $this->crudModel->getRecordDetails($whereData);
        $data['pagination'] = $paginationData;
        $data['page_no'] = $page;
        $data['perPageRecord'] = $this->perPageRecord;
        $data['totalRecordCount'] = isset($totalRecords) ? $totalRecords : 0;

        // Designation lookup (active first page view)
        $designationWhere = [];
        $designationWhere['t_is_deleted != '] = 1;
        $designationWhere['v_module_name'] = config('constants.DESIGNATION_LOOKUP');
        $designationList = LookupMaster::where('t_is_deleted', '!=', 1)
            ->where('v_module_name', config('constants.DESIGNATION_LOOKUP'))
            ->orderBy('v_value', 'ASC')
            ->get();
        $data['designationRecordDetails'] = $designationList;

        return view($this->folderName . 'sub-designation-master')->with($data);
    }

    public function edit(Request $request)
    {
        $data = $whereData = [];
        $recordId = (!empty($request->input('record_id')) ? $request->input('record_id') : '');

        if (!empty($recordId)) {
            $recordId = (int) Wild_tiger::decode($recordId);
            $whereData['master_id'] = $recordId;
            $whereData['singleRecord'] = true;
            $recordInfo = $this->crudModel->getRecordDetails($whereData);
            if (!empty($recordInfo)) {
                $data['recordInfo'] = $recordInfo;
            }
        }

        $designationList = LookupMaster::where('t_is_deleted', '!=', 1)
            ->where('v_module_name', config('constants.DESIGNATION_LOOKUP'))
            ->orderBy('v_value', 'ASC')
            ->get();
        $data['designationRecordDetails'] = $designationList;

        $html = view($this->folderName . 'add-sub-designation-master')->with($data)->render();
        echo $html;
        die;
    }

    public function add(Request $request)
    {
        if (!empty($request->input())) {
            $recordId = (!empty($request->post('record_id')) ? (int) Wild_tiger::decode($request->post('record_id')) : 0);
            $designationId = (!empty($request->post('designation')) ? (int) Wild_tiger::decode($request->post('designation')) : 0);
            $name = (!empty($request->post('sub_designation_name')) ? trim($request->post('sub_designation_name')) : '');

            $formValidation = [];
            $formValidation['designation'] = ['required'];
            $formValidation['sub_designation_name'] = ['required'];

            $checkValidation = Validator::make($request->all(), $formValidation, [
                'designation.required' => __('messages.required-designation'),
                'sub_designation_name.required' => __('messages.required-name'),
            ]);

            if ($checkValidation->fails()) {
                $this->ajaxResponse(101, implode("<br>", $checkValidation->errors()->all()));
            }

            // Duplicate check (designation + name)
            $dupWhere = [];
            $dupWhere['t_is_deleted != '] = 1;
            $dupWhere['i_designation_id'] = $designationId;
            $dupWhere['v_sub_designation_name'] = $name;
            if ($recordId > 0) {
                $dupWhere['i_id != '] = $recordId;
            }
            $exist = $this->crudModel->getSingleRecordById($this->tableName, ['i_id'], $dupWhere);
            if (!empty($exist)) {
                $this->ajaxResponse(101, __('messages.duplicate-record'));
            }

            $recordData = [];
            $recordData['i_designation_id'] = $designationId;
            $recordData['v_sub_designation_name'] = $name;
            if (!empty($request->post('chart_color'))) {
                $recordData['v_chart_color'] = storeChartColor($request->post('chart_color'));
            }

            $result = false;
            $successMessage = __('messages.success-create', ['module' => $this->moduleName]);
            $errorMessage = __('messages.error-create', ['module' => $this->moduleName]);

            if ($recordId > 0) {
                $successMessage = __('messages.success-update', ['module' => $this->moduleName]);
                $errorMessage = __('messages.error-update', ['module' => $this->moduleName]);
                $result = $this->crudModel->updateTableData($this->tableName, $recordData, ['i_id' => $recordId]);
            } else {
                $insertId = $this->crudModel->insertTableData($this->tableName, $recordData);
                if ($insertId > 0) {
                    $result = true;
                }
            }

            // Row HTML for immediate UI update (minimal placeholder)
            $html = '';
            if ($result != false) {
                if ($recordId > 0) {
                    $recordDetail = $this->crudModel->getRecordDetails(['master_id' => $recordId, 'singleRecord' => true]);
                } else {
                    $recordDetail = $this->crudModel->getRecordDetails(['singleRecord' => true, 'order_by' => ['i_id' => 'desc']]);
                }
                if (!empty($recordDetail)) {
                    $recordInfo = [];
                    $recordInfo['rowIndex'] = (!empty($request->input('row_index')) ? $request->input('row_index') : 1);
                    $recordInfo['recordDetail'] = $recordDetail;
                    $html = view(config('constants.AJAX_VIEW_FOLDER') . 'sub-designation-master/single-sub-designation-master')->with($recordInfo)->render();
                }
                $this->ajaxResponse(1, $successMessage, ['html' => $html]);
            } else {
                $this->ajaxResponse(101, $errorMessage);
            }
        }
    }

    public function filter(Request $request)
    {
        $whereData = $likeData = [];
        $page = (!empty($request->post('page')) ? $request->post('page') : 1);

        if (!empty($request->post('search_by'))) {
            $likeData['searchBy'] = trim($request->post('search_by'));
        }
        if (!empty($request->post('search_designation'))) {
            $whereData['designation_id'] = (int) Wild_tiger::decode($request->post('search_designation'));
        }
        if (!empty($request->post('search_status')) && $request->post('search_status') != 'all') {
            $whereData['active_status'] = (trim($request->input('search_status')) == config('constants.INACTIVE_STATUS') ? 0 : 1);
        }

        $paginationData = [];
        if ($page == $this->defaultPage) {
            $totalRecords = count($this->crudModel->getRecordDetails($whereData, $likeData));
            $lastpage = ceil($totalRecords / $this->perPageRecord);
            $paginationData['current_page'] = $this->defaultPage;
            $paginationData['per_page'] = $this->perPageRecord;
            $paginationData['last_page'] = $lastpage;
        }

        if ($page == $this->defaultPage) {
            $whereData['offset'] = 0;
            $whereData['limit'] = $this->perPageRecord;
        } else if ($page > $this->defaultPage) {
            $whereData['offset'] = ($page - 1) * $this->perPageRecord;
            $whereData['limit'] = $this->perPageRecord;
        }

        $data = [];
        $data['recordDetails'] = $this->crudModel->getRecordDetails($whereData, $likeData);
        if (isset($totalRecords)) {
            $data['totalRecordCount'] = $totalRecords;
        }
        $data['pagination'] = $paginationData;
        $data['page_no'] = $page;
        $data['perPageRecord'] = $this->perPageRecord;

        $html = view(config('constants.AJAX_VIEW_FOLDER') . 'sub-designation-master/sub-designation-master-list')->with($data)->render();
        echo $html;
        die;
    }

    public function updateStatus(Request $request)
    {
        if (!empty($request->input())) {
            return $this->updateStatusMaster($request, $this->tableName, $this->moduleName);
        }
    }

    public function delete(Request $request)
    {
        if (!empty($request->input())) {
            $recordId = (!empty($request->input('delete_record_id')) ? (int) Wild_tiger::decode($request->input('delete_record_id')) : 0);
            return $this->removeRecord($this->tableName, $recordId, $this->moduleName);
        }
    }

    public function checkUniqueSubDesignationName(Request $request)
    {
        $recordId = (!empty($request->input('record_id')) ? (int) Wild_tiger::decode($request->input('record_id')) : 0);
        $designationId = (!empty($request->input('designation')) ? (int) Wild_tiger::decode($request->input('designation')) : 0);
        $name = (!empty($request->input('sub_designation_name')) ? trim($request->input('sub_designation_name')) : '');

        $dupWhere = [];
        $dupWhere['t_is_deleted != '] = 1;
        $dupWhere['i_designation_id'] = $designationId;
        $dupWhere['v_sub_designation_name'] = $name;
        if ($recordId > 0) {
            $dupWhere['i_id != '] = $recordId;
        }
        $exist = $this->crudModel->getSingleRecordById($this->tableName, ['i_id'], $dupWhere);

        $result = [];
        $result['status_code'] = 1;
        $result['message'] = trans('messages.success');
        if (!empty($exist)) {
            $result['status_code'] = 101;
            $result['message'] = trans('messages.error');
        }
        echo json_encode($result);
        die;
    }
}
