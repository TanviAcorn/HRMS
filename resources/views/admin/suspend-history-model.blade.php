<div class="modal fade document-folder" id="suspend-history-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title twt-header-name" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body overflow-hidden">
                <div class="row px-3 py-4">
                    <div class="col-12 table-responsive">
                        <table class="table table-sm table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center sr-col" style="min-width: 50px;">{{ trans('messages.sr-no') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.application-date') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.from-date') }}</th>
                                    <th style="min-width: 120px;">{{ trans('messages.to-date') }}</th>
                                    <th style="min-width: 180px;">{{ trans('messages.suspension-reason') }}</th>
                                    <th style="min-width: 140px;">{{ trans('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="show-suspend-history-html">
                           
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var employee_module_url = '{{ config("constants.EMPLOYEE_MASTER_URL") }}' + '/';
function openSuspendHistoryModel(thisitem){

	var employee_id = $.trim($(thisitem).attr('data-employee-id'));
	
	if( employee_id  != "" && employee_id != null  ){
		$.ajax({
			type: "POST",
			url: employee_module_url + 'showSuspendHistory',
			data: {
				"_token": "{{ csrf_token() }}",
				'employee_id':employee_id
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
		 		hideLoader();
				if( response != "" && response != null ){
					$(".show-suspend-history-html").html(response);
					openBootstrapModal('suspend-history-model');
					$("#suspend-history-model").find('.twt-header-name').html("{{ trans("messages.suspension-history") }} " + common_emp_modal_header_title);
				}
		 	},
			error: function() {
				hideLoader();
			}
		});
	}
	
}


</script>