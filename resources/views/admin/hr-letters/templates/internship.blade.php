<form id="letter-template-form">
    <input type="hidden" name="template" value="internship" />
    <div class="form-group">
        <label>From Date</label>
        <input type="date" class="form-control" name="from_date" value="{{ date('Y-m-d') }}" />
    </div>
    <div class="form-group">
        <label>To Date</label>
        <input type="date" class="form-control" name="to_date" value="{{ date('Y-m-d') }}" />
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
