@php
$date = isset($recordDetail->dt_salary_month) && !empty($recordDetail->dt_salary_month) ? $recordDetail->dt_salary_month : date('Y-m-d');
@endphp
<div class="col-xl-2 col-sm-4 mb-5 payslip-card">
    <div class="form-check form-check-inline form-checkbox">
        <input class="form-check-input row-payslip-checkbox row-checkbox" value="{{ Wild_tiger::encode($recordDetail->i_id) }}" data-record-id="{{ Wild_tiger::encode($recordDetail->i_id) }}" type="checkbox" data-date="{{ $date }}" id="payslip_check_{{ $date }}" name="">
        <label class="form-check-label lable-control" for="payslip_check_{{ $date }}"></label>
    </div>
    <div class="card border-0 mb-3 payslip-cardbody" onclick="checkSalarySlip(this);">
        <!-- Card body -->
        <div class="card-body">
            <div>
                <div class="text-center">
                    <span class=" font-weight-bold mb-0"><i class="fa fa-file-pdf fa-5x text-theme"></i></span>
                </div>
            </div>
        </div>
        <div class="card-footer text-center d-flex">
            <div class="w-100">
                <a class="text-theme text-center" onclick="sendSinglePaySlip(this);" data-record-id="{{ Wild_tiger::encode($recordDetail->i_id) }}" href="javascript:void(0);">{{ trans('messages.send') }}</a>
            </div>
        </div>
    </div>
    <div>
        <div class="text-center text-theme m-2">
            <h5>{{ (!empty($date) ? convertDateFormat($date,'M-Y') : '' ) }}</h5>
        </div>
    </div>
</div>