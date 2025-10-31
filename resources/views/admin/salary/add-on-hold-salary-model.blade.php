					<div class="row">
	                    <div class="col-lg-12 col-md ">	  
				       		<div class="form-group ">
				                <div class="salary-on-hold-master-div ">
				                <?php 
				                $month_date = [];
				                $joiningDate = (!empty($onHoldSalaryjoiningDate) ? $onHoldSalaryjoiningDate :'');
				                if((!empty($holdSalaryDetails)) && (count($holdSalaryDetails) > 0 )){
				                	$monthCount = 1;
				                	foreach ($holdSalaryDetails as $countKey => $holdSalaryDetail){
				                		$onSalaryId = (!empty($holdSalaryDetail->i_id) ? $holdSalaryDetail->i_id : '')?>
				                		<div class="row month-master-row">
							           		<div class="col-6 mt-2">
							           			<?php if($monthCount == 1 ){?>
							           				<label class="control-label">{{ trans('messages.month') }}</label>
							           			<?php } ?>
							           			<?php $month_date[] = 'edit_month_'.(!empty($onSalaryId) ? $onSalaryId : 0)?>
							                	<input type="text" name="edit_month_{{ $onSalaryId }}" class="form-control onhold-salary-month month-record-row unique-month" placeholder="{{ config('constants.ON_HOLD_SALARY_DEFAULT_MONTH_FORMAT')}}" value="{{ (!empty($holdSalaryDetail->dt_month) ? convertDateFormat($holdSalaryDetail->dt_month,'M-Y') : '' ) }}"> 
							            	</div>
							               	<div class="col-4 mt-2">
							               	<?php if($monthCount == 1 ){?>
							               		<label class="control-label">{{ trans('messages.amount') }}</label>
							               	<?php } ?>
							                 	<input type="text" name="edit_amount_{{ $onSalaryId }}" onkeyup="onlyNumber(this)" class="form-control amount-record-row" placeholder="{{ trans('messages.amount')}}" value="{{ (!empty($holdSalaryDetail->d_amount) ? $holdSalaryDetail->d_amount :'') }}"> 
							                </div>
							                <?php if($monthCount == 1 ){?>
							                <div class="col-2 mt-4 pt-2">
								        		<button type="button" class="btn btn-danger btn-sm mt-2" data-remove-id="{{ (!empty($onSalaryId) ? $onSalaryId :'') }}" onclick="removeHtml(this)" title="{{ trans('messages.delete')}}"><i class="fas fa-trash"></i></button>
								        	</div>
							                <?php } else{?>
							                
							               	<div class="col-2 mt-1">
							                	<button type="button" class="btn btn-danger btn-sm mt-2" title="{{ trans('messages.delete')}}" data-remove-id="{{ (!empty($onSalaryId) ? $onSalaryId :'') }}" onclick="removeHtml(this)"><i class="fas fa-trash"></i></button>
							               	</div>  
							               	<?php } ?>             
										</div>
				                		<?php 
				                		$monthCount++;
				                	} ?>
				                <?php } else{ ?>
						        	<div class="row month-master-row">
						             	<div class="col-6">
						             	<?php $month_date[] = 'month_1'?>
						               		<label class="control-label">{{ trans('messages.month') }}</label>
						                	<input type="text" name="month_1" class="form-control onhold-salary-month month-record-row unique-month" placeholder="{{ config('constants.ON_HOLD_SALARY_DEFAULT_MONTH_FORMAT')}}" value=""> 
								        </div>
						                <div class="col-4 ">
						                	<label class="control-label">{{ trans('messages.amount') }}</label>
						               		<input type="text" name="amount_1" onkeyup="onlyNumber(this)" onchange="onlyNumber(this)" class="form-control amount-record-row" placeholder="{{ trans('messages.amount')}}" value=""> 
						                </div>
									</div>
									<div class="row month-master-row">
						           		<div class="col-6 mt-2">
						           		<?php $month_date[] = 'month_2'?>
						                	<input type="text" name="month_2" class="form-control onhold-salary-month month-record-row unique-month" placeholder="{{ config('constants.ON_HOLD_SALARY_DEFAULT_MONTH_FORMAT')}}" value=""> 
						            	</div>
						               	<div class="col-4 mt-2">
						                 	<input type="text" name="amount_2" onkeyup="onlyNumber(this)" onchange="onlyNumber(this)"  class="form-control amount-record-row" placeholder="{{ trans('messages.amount')}}" value=""> 
						                </div>
						               	<div class="col-2 mt-1">
						                	<button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeHtml(this)" title="{{ trans('messages.delete')}}"><i class="fas fa-trash"></i></button>
						               	</div>               
									</div>
								<?php } ?>
								</div>
								<input type="hidden" name="on_hold_joining_date" value="{{ (isset($joiningDate) ? $joiningDate :'') }}">
								<div class="col-lg-12 mt-2 pl-0">
					            	<button type="button" class="btn bg-theme text-white btn-sm" onclick="addNewRow()" title="{{ trans('messages.add')}}"> <i class="fas fa-plus"></i></button>
					            </div>
				         	</div>              
		    			</div>
		    			
                    </div>
                    
                    
                  <script>
                	var all_on_hold_joining_date = "{{ (isset($joiningDate) ? $joiningDate :'') }}";
                	
                  	var month_wise_date = <?php echo (isset($month_date) ? json_encode($month_date) : [])?>;
					var month_date_details = month_wise_date;
				     $(month_date_details).each(function(index, value) {
				    	
				    	$('[name="'+value+'"]').datetimepicker({
							useCurrent: false,
							viewMode: 'days',
							ignoreReadonly: true,
							format: 'MMM-YYYY',
							showClear: true,
							showClose: true,
							widgetPositioning: {
								vertical: 'bottom',
								horizontal: 'auto'

							},
							icons: {
								clear: 'fa fa-trash',
								Close: 'fa fa-trash',
							},
						});
				    	$('[name="'+value+'"]').data('DateTimePicker').minDate(moment(all_on_hold_joining_date,'MMM-YYYY'));
				     });
				    var on_hold_record_id = "{{ (isset($onholdRecordId) ? $onholdRecordId :'') }}";
			   		
			   		if(on_hold_record_id !="" && on_hold_record_id != null){
			   			$("#on-hold-salary-model").find('.onhold-salary-action-button').html("{{ trans('messages.update')  }}");
			   			$('.onhold-salary-action-button').attr('title' , "{{ trans('messages.update') }}");
			   		}
			   		
			   			
                  </script>