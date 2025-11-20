<div class="form-group">
    <label>Date</label>
    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
</div>

<div class="form-group">
    <label>Employee Name</label>
    <input type="text" class="form-control" value="{{ $employee->v_employee_full_name }}" readonly>
</div>

<div class="form-group">
    <label>Employee ID</label>
    <input type="text" class="form-control" value="{{ $employee->v_employee_id }}" readonly>
</div>

<div class="form-group">
    <label>Designation</label>
    <input type="text" class="form-control" value="{{ $employee->designation }}" readonly>
</div>

<div class="form-group">
    <label>Leave Type</label>
    <select name="leave_type" class="form-control" required>
        <option value="Casual Leave">Casual Leave</option>
        <option value="Sick Leave">Sick Leave</option>
        <option value="Earned Leave">Earned Leave</option>
        <option value="Maternity Leave">Maternity Leave</option>
        <option value="Paternity Leave">Paternity Leave</option>
        <option value="Bereavement Leave">Bereavement Leave</option>
        <option value="Compensatory Off">Compensatory Off</option>
    </select>
</div>

<div class="form-group">
    <label>Leave Period</label>
    <div class="row">
        <div class="col-md-6">
            <label>From Date</label>
            <input type="date" name="from_date" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>To Date</label>
            <input type="date" name="to_date" class="form-control" required>
        </div>
    </div>
</div>

<div class="form-group">
    <label>Number of Days</label>
    <input type="number" name="days" class="form-control" min="0.5" step="0.5" required>
</div>

<div class="form-group">
    <label>Reason for Leave</label>
    <textarea name="reason" class="form-control" rows="3" required></textarea>
</div>
