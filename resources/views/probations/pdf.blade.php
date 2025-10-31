<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    @if(empty($forPdf))
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @endif
    <title>Probation Assessment</title>
    @if(empty($forPdf))
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @endif
    <style>
    @if(!empty($forPdf))
        body { font-family: Arial, Helvetica, sans-serif; color: #000; background: #fff; }
        .wrapper { width: 100%; margin: 0; padding: 0; }
        .card { border: 1px solid #dddddd; margin-bottom: 10px; }
        .card-header { padding: 8px 10px; font-weight: bold; border-bottom: 1px solid #dddddd; background: #f2f2f2; }
        .card-body { padding: 8px 10px; }
        h1 { font-size: 18px; margin: 0 0 5px 0; }
        .meta { font-size: 10px; color: #666666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #dddddd; padding: 6px 8px; font-size: 11px; }
        th { background: #f2f2f2; text-align: left; font-weight: bold; }
        .badge { display: inline-block; padding: 2px 6px; font-size: 10px; border: 1px solid #dddddd; border-radius: 3px; }
        .badge-success { background: #e6f4ea; color: #137333; }
        .badge-warning { background: #fff4e5; color: #b26a00; }
        .title-row { width: 100%; }
    @else
        body{ font-family: Arial, Helvetica, sans-serif; color:#212529; background:#f8f9fa; }
        .wrapper{ max-width: 1100px; margin: 24px auto; padding: 0 16px; }
        .toolbar{ background:#fff; border:1px solid #dee2e6; border-radius:6px; padding:10px 12px; display:flex; align-items:center; gap:8px; margin-bottom:12px; }
        .btn{ display:inline-block; padding:6px 10px; font-size:13px; border-radius:4px; text-decoration:none; cursor:pointer; border:1px solid #dee2e6; background:#fff; color:#212529; }
        .btn-primary{ background:#8a1538; color:#fff; border-color:#8a1538; }
        .btn-outline{ background:#fff; }
        .btn + .btn{ margin-left:6px; }
        .card{ background:#fff; border:1px solid #dee2e6; border-radius:6px; margin-bottom:14px; }
        .card-header{ padding:10px 12px; font-weight:600; border-bottom:1px solid #dee2e6; background:#f9fafb; }
        .card-body{ padding:12px; }
        h1{ font-size:20px; margin:0; }
        .meta{ font-size:12px; color: #6c757d; margin-top:2px; }
        table{ width:100%; border-collapse:collapse; }
        th,td{ border:1px solid #dee2e6; padding:8px 10px; font-size:13px; }
        th{ background:#f9fafb; text-align:left; font-weight:600; }
        .badge{ display:inline-block; padding:4px 8px; font-size:12px; border-radius:10px; font-weight:600; }
        .badge-success{ background:#e6f4ea; color:#137333; }
        .badge-warning{ background:#fff4e5; color:#b26a00; }
        .title-row{ display:flex; align-items:baseline; justify-content:space-between; }
        .muted{ color:#6c757d; }
    @endif
    </style>
</head>
<body>
    <div class="wrapper">
        @if(empty($forPdf))
        <div class="toolbar">
            <strong class="muted" style="margin-right:auto">Preview</strong>
            <button class="btn btn-primary" onclick="window.print()">Save as PDF</button>
            <a class="btn btn-outline" href="{{ url('probation-assessments') }}">Back to List</a>
        </div>
        @endif

        <div class="title-row" style="margin-bottom:8px;">
            <div>
                <h1>Probation Assessment</h1>
                <div class="meta">Generated at: {{ optional($generatedAt)->format('Y-m-d H:i') }}</div>
            </div>
            <div>
                @php $st = strtoupper($assessment->vch_status ?? ''); @endphp
                @if($st === 'COMPLETED')
                    <span class="badge badge-success">COMPLETED</span>
                @else
                    <span class="badge badge-warning">{{ $st }}</span>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">Employee Details</div>
            <div class="card-body">
                <table>
                    <tr>
                        <th>Employee Name</th>
                        <td>{{ $employee->v_employee_full_name }}</td>
                        <th>Employee Code</th>
                        <td>{{ $employee->v_employee_code }}</td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td>{{ data_get($employee, 'teamInfo.v_value', '') }}</td>
                        <th>Designation</th>
                        <td>{{ data_get($employee, 'designationInfo.v_value', '') }}</td>
                    </tr>
                    <tr>
                        <th>Joining Date</th>
                        <td>{{ $employee->dt_joining_date }}</td>
                        <th>Probation End Date</th>
                        <td>{{ $employee->dt_probation_end_date }}</td>
                    </tr>
                    <tr>
                        <th>Sub-Designation</th>
                        <td colspan="3">{{ data_get($employee, 'subDesignationInfo.v_sub_designation_name', '') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Summary</div>
            <div class="card-body">
                <table>
                    <tr>
                        <th>Leave in Probation (days)</th>
                        <td>{{ $assessment->i_leave_in_probation }}</td>
                        <th>Status</th>
                        <td>{{ strtoupper($assessment->vch_status) }}</td>
                    </tr>
                    <tr>
                        <th>Decision</th>
                        <td>{{ $assessment->vch_decision }}</td>
                        <th>Extended Months</th>
                        <td>{{ $assessment->i_extend_months }}</td>
                    </tr>
                    <tr>
                        <th colspan="2">Extended Till Date</th>
                        <td colspan="2">{{ $assessment->dt_extend_upto_date }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Assessment Details</div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th style="width:55%">Particular</th>
                            <th style="width:10%">Score</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Quality and accuracy of work</td>
                            <td>{{ $assessment->i_quality_score }}</td>
                            <td>{{ $assessment->vch_quality_remarks }}</td>
                        </tr>
                        <tr>
                            <td>Work Efficiency</td>
                            <td>{{ $assessment->i_efficiency_score }}</td>
                            <td>{{ $assessment->vch_efficiency_remarks }}</td>
                        </tr>
                        <tr>
                            <td>Attendance & Time Keeping</td>
                            <td>{{ $assessment->i_attendance_score }}</td>
                            <td>{{ $assessment->vch_attendance_remarks }}</td>
                        </tr>
                        <tr>
                            <td>Teamwork, Communication & Technical Skill</td>
                            <td>{{ $assessment->i_teamwork_score }}</td>
                            <td>{{ $assessment->vch_teamwork_remarks }}</td>
                        </tr>
                        <tr>
                            <td>Competency in the Role</td>
                            <td>{{ $assessment->i_competency_score }}</td>
                            <td>{{ $assessment->vch_competency_remarks }}</td>
                        </tr>
                        <tr>
                            <th style="text-align:right;">TOTAL SCORE</th>
                            <th>{{ number_format($totalScore, 2) }}</th>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Objectives and Training</div>
            <div class="card-body">
                <table>
                    <tr>
                        <th style="width:25%">Have the Objectives identified for the probation period met?</th>
                        <td>{{ $assessment->e_objectives_met }}</td>
                    </tr>
                    <tr>
                        <th>Objectives Details</th>
                        <td>{{ $assessment->vch_objectives_details }}</td>
                    </tr>
                    <tr>
                        <th>Have the training/development needs for the probation period been addressed?</th>
                        <td>{{ $assessment->e_training_addressed }}</td>
                    </tr>
                    <tr>
                        <th>Training Details</th>
                        <td>{{ $assessment->vch_training_details }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @if(empty($forPdf))
    <script>
        // Auto-trigger Save as PDF if ?save=1 or hash #save present
        (function(){
            var p = new URLSearchParams(window.location.search);
            if (p.get('save') === '1' || window.location.hash === '#save') {
                setTimeout(function(){ window.print(); }, 300);
            }
        })();
    </script>
    @endif
</body>
</html>
