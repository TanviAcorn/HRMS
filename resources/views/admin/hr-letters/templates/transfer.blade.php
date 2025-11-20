<form id="letter-template-form">
    <input type="hidden" name="template" value="transfer" />
    <div class="form-group">
        <label>From Department</label>
        <input type="text" class="form-control" name="from_department" value="{{ data_get($employee,'teamInfo.v_value') ?? '' }}" />
    </div>
    <div class="form-group">
        <label>To Department</label>
        <input type="text" class="form-control" name="to_department" />
    </div>
    <div class="form-group">
        <label>Effective Date</label>
        <input type="date" class="form-control" name="effective_date" value="{{ date('Y-m-d') }}" />
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
