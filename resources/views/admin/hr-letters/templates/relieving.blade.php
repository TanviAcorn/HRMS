<form id="letter-template-form">
    <input type="hidden" name="template" value="relieving" />
    <div class="form-group">
        <label>Designation</label>
        <input type="text" class="form-control" name="designation" value="{{ data_get($employee,'designationInfo.v_value') ?? '' }}" />
    </div>
    <div class="form-group">
        <label>Joining Date</label>
        <input type="date" class="form-control" name="joining_date" value="{{ isset($employee->dt_joining_date) ? date('Y-m-d', strtotime($employee->dt_joining_date)) : '' }}" />
    </div>
    <div class="form-group">
        <label>Relieving Date</label>
        <input type="date" class="form-control" name="relieving_date" value="{{ isset($employee->dt_notice_period_end_date) ? date('Y-m-d', strtotime($employee->dt_notice_period_end_date)) : '' }}" />
    </div>
    <div class="form-group">
        <label>Letter No (optional)</label>
        <input type="text" class="form-control" name="letter_no" />
    </div>
    <div class="form-group">
        <label>Date (optional)</label>
        <input type="date" class="form-control" name="date" />
    </div>
</form>
