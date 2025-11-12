<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmployeeModel;
use App\Helpers\Twt\Wild_tiger;

class OrganizationChartController extends MasterController
{
    public function __construct()
    {
        parent::__construct();
        $this->crudModel = new EmployeeModel();
        $this->moduleName = trans('messages.organization-chart');
    }

    public function index()
    {
        $data['pageTitle'] = trans('messages.organization-chart');
        return view(config('constants.ADMIN_FOLDER') . 'organization-chart/index')->with($data);
    }

    public function getChartData(Request $request)
    {
        try {
            // Get all active employees with their relationships
            $employees = EmployeeModel::with(['leaderInfo', 'designationInfo', 'teamInfo'])
                ->where('t_is_deleted', 0)
                ->where('t_is_active', 1)
                ->where('e_employment_status', '!=', config('constants.RELIEVED_EMPLOYMENT_STATUS'))
                ->orderBy('i_leader_id')
                ->orderBy('v_employee_full_name')
                ->get();

            // Build hierarchical structure
            $chartData = $this->buildHierarchy($employees);

            return response()->json([
                'status_code' => 1,
                'data' => $chartData,
                'message' => trans('messages.success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 101,
                'message' => trans('messages.error-something-went-wrong'),
                'error' => $e->getMessage()
            ]);
        }
    }

    private function buildHierarchy($employees, $parentId = null)
    {
        $branch = [];

        foreach ($employees as $employee) {
            if ($employee->i_leader_id == $parentId) {
                $children = $this->buildHierarchy($employees, $employee->i_id);

                $node = [
                    'id' => $employee->i_id,
                    'name' => $employee->v_employee_full_name,
                    'title' => $employee->designationInfo->v_value ?? '',
                    'team' => $employee->teamInfo->v_value ?? '',
                    'email' => $employee->v_outlook_email_id ?? $employee->v_personal_email_id,
                    'phone' => $employee->v_contact_no,
                    'profile_pic' => $employee->v_profile_pic,
                    'employee_code' => $employee->v_employee_code,
                    'profile_url' => config('constants.EMPLOYEE_PROFILE_LINK') . Wild_tiger::encode($employee->i_id)
                ];

                if (!empty($children)) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }

        return $branch;
    }
}
