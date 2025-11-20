<form id="letter-template-form">
    <input type="hidden" name="template" value="probation" />
    <div class="form-group">
        <label>Designation</label>
        <input type="text" class="form-control" name="designation" value="{{ data_get($employee,'designationInfo.v_value') ?? '' }}" />
    </div>
    <div class="form-group">
        <label>Confirmation Date</label>
        <input type="date" class="form-control" name="confirmation_date" value="{{ date('Y-m-d') }}" />
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
