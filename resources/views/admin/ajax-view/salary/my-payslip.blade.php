@php $index =  ( $pageNo - 1 ) * $perPageRecord; @endphp

@if (count($recordDetails) > 0)
    @foreach ($recordDetails as $key => $recordDetail)
        @include(config('constants.AJAX_VIEW_FOLDER') . 'salary/single-my-payslip', $recordDetail)
    @endforeach
    <div class="col-12 py-2 payslip-sticky-button">
      <button type="button" class="btn btn-theme text-white send-pay-slip-button" onclick="sendSinglePaySlip(this);" data-record-type="multiple" data-record-id="{{ ( isset($employeeId) ?  Wild_tiger::encode($employeeId) : '' ) }}" title="{{ trans('messages.send-pay-slip') }}">{{ trans('messages.send-pay-slip') }}</button>
    </div>
    @if (!empty($pagination))
        <input name="current_page" type="hidden" id="current_page" value="{{ $pagination['current_page'] }}">
        <input name="last_page" type="hidden" id="last_page" value="{{ $pagination['last_page'] }}">
        <input name="per_page" type="hidden" id="per_page" value="{{ $pagination['per_page'] }}">
    @endif
@else
    <div class="course-list-mdiv col-12">
        <div>
            <div class="text-center">
                <p>{{ trans('messages.no-record-found') }}</p>
            </div>
        </div>
    </div>
@endif
<script>
function checkSalarySlip(thisitem){
	var updated_status = $(thisitem).parents('.payslip-card').find('.row-payslip-checkbox').prop('checked');
	$(thisitem).parents('.payslip-card').find('.row-payslip-checkbox').prop('checked' , ( updated_status != false ? false : true ) );
}

function sendSingleSlip(thisitem){
	if( $(thisitem).parents('.payslip-card').find('.row-payslip-checkbox').prop('checked') != true ){
		$(thisitem).parents('.payslip-card').find('.row-payslip-checkbox').prop('checked' , true );
	}
	
	$(".send-pay-slip-button").trigger('click');

}

</script>
