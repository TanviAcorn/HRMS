<form id="letter-template-form">
    <input type="hidden" name="template" value="experience" />
    <div class="form-group">
        <label>Letter No</label>
        <input type="text" class="form-control" name="letter_no" value="EXP/{{ date('Y/m/') }}0000" />
    </div>
    <div class="form-group">
        <label>Date</label>
        <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" />
    </div>
    <div class="form-group">
        <label>Designation</label>
        <input type="text" class="form-control" name="designation" value="{{ optional($employee->designationInfo)->v_value ?? '' }}" />
    </div>
    <div class="form-group">
        <label>Joining Date</label>
        <input type="date" class="form-control" name="from_date" value="{{ isset($employee->dt_joining_date) ? date('Y-m-d', strtotime($employee->dt_joining_date)) : '' }}" />
    </div>
    <div class="form-group">
        <label>Exit Date</label>
        <input type="date" class="form-control" name="to_date" value="{{ isset($employee->dt_exit_date) ? date('Y-m-d', strtotime($employee->dt_exit_date)) : '' }}" />
    </div>
</form>
