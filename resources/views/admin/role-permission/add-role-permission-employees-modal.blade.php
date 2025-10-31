@if (isset($recordDetails) && !empty($recordDetails) && isset($rolePermissionInfo) && !empty($rolePermissionInfo))
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label for="employees" class="control-label">{{ trans('messages.employees') }}</label>
                <select class="form-control select2 select2-hidden-accessible" name="employees[]" multiple="">
                    @foreach ($recordDetails as $recordDetail)
                        @php
                            $encodeEmployeeId = Wild_tiger::encode($recordDetail->i_login_id);
                            if (isset($rolePermissionInfo->v_assign_employees) && !empty($rolePermissionInfo->v_assign_employees)) {
                                $assignEmployee = explode(',', $rolePermissionInfo->v_assign_employees);
                            }
                            
                            $selected = '';
                            if (isset($recordDetail) && !empty($recordDetail->i_login_id) && isset($assignEmployee) && !empty($assignEmployee) && in_array($recordDetail->i_login_id, $assignEmployee)) {
                                $selected = "selected='selected'";
                            }
                        @endphp
                        <option value="{{ $encodeEmployeeId }}" {{ $selected }}>
                            {{ (!empty($recordDetail->v_employee_full_name) ? $recordDetail->v_employee_full_name .(!empty($recordDetail->v_employee_code) ? ' ('.$recordDetail->v_employee_code. ')' : ''): '') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endif
