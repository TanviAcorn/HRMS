<div class="relation h-100">
    <div class="card card-display border-0 px-2 h-100">
        <div class="card-body px-2 py-0">
            <div class="row px-0 border-bottom">
                <div class="col-12 profile-details-title-card">
                    <h5 class="profile-details-title" id="exampleModalLabel">{{ trans("messages.relation") }}</h5>
                    @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) || (  (isset($employeeRecordInfo->i_login_id) && ( $employeeRecordInfo->i_login_id == session()->get('user_id') ) )  ) )	 
                    	<a href="javascript:void(0);" data-emplyee-id="{{ (!empty($employeeRecordInfo->i_id) ? Wild_tiger::encode($employeeRecordInfo->i_id) : 0 ) }}" onclick="openRelationModel(this)" title="{{ trans('messages.edit') }}">{{ trans("messages.edit") }}</a>
                    @endif	
                </div>
            </div>
            <div class="row">
                <div class="col-12 py-0 profile-display-card">
                    <div class="row pb-0 pt-3 employee-relation-list">
                    	<?php 
						$recordInfo['employeeRecordInfo'] = $employeeRecordInfo;
						$html = view (config('constants.AJAX_VIEW_FOLDER') . 'employee-master/relation-list')->with ( $recordInfo )->render();
						echo $html;
						?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade document-folder relation-modal relation-modal-changes" id="relation-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.relation") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'add-relation-form' , 'method' => 'post' ,  'url' => 'add')) !!}
                <div class="modal-body overflow-hidden add-relation-html">
                   
                </div>
                <div class="modal-footer justify-content-end">
                <input type="hidden" name="employee_record_id" value="">
                    <button type="button" onclick="addRelationModel()" class="btn bg-theme text-white action-button lookup-modal-action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
                <input type="hidden" name="relation_count" value="">
            {!! Form::close() !!}
        </div>
    </div>
</div>



<script>
    $("#add-relation-form").validate({
        errorClass: "invalid-input",
        rules: {
            relation: {
                required: true,
                noSpace:true
            },
            name: {
                required: true,
                noSpace:true
            }
        },
        messages: {
            relation: {
                required: "{{ trans('messages.required-relation') }}"
            },
            name: {
                required: "{{ trans('messages.require-name') }}"
            },
        }
    });
var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
    function openRelationModel(thisitem){
        var employee_id = $.trim($(thisitem).attr('data-emplyee-id'));
        $("[name='employee_record_id']").val(employee_id);
		
        $.ajax({
    		type: "POST",
    		url: employee_module_url + 'editRealtion',
    		data: {
    			"_token": "{{ csrf_token() }}",'employee_id':employee_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			$('.add-relation-html').html(response);
    			openBootstrapModal('relation-model');
    		
    		
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
    	
    }
	var relation_count = 2;
    function addNewRelationRow(thisitem){
        relation_count++;
        var html ='';
        html += '<tr>';
        html += '<td class="table-index text-center">'+relation_count+'</td>';
        html += '<td> <select class="form-control relation-row" name="relation_'+relation_count+'">';
        html += '<option value="">{{ trans("messages.select") }}</option>';
       	<?php 
        if(!empty($relationInof)){
       		foreach ($relationInof as $key => $relation){
         		?>
       			html += '<option value="{{ $key }}">{{ (!empty($relation) ? $relation : '') }}</option>';
          		<?php 
        	}
      	}
       	?>
       	html += '</select>';
       	html += '</td>';
       	html += '<td> <input type="text" name="name_'+relation_count+'" class="form-control relation-name-row" placeholder="{{ trans('messages.name') }}">';
       	html += '</td>';
       	html += '<td> <input type="text" name="mobile_'+relation_count+'" maxlength="15" onkeyup="onlyNumberWithSpaceAndPlusSign(this)" class="form-control" placeholder="{{ trans('messages.mobile') }}">';
       	html += '</td>';
		// html += '<td> <input type="text" name="profession" class="form-control" placeholder="{{ trans('messages.profession') }}">';
       	// html += '</td>';
       	html += '<td class="position-relative">';
       	html += '<div class="relation-date"><input type="text" class="form-control birth-date" name="rel_date_of_birth_'+relation_count+'" placeholder="{{ trans('messages.date-of-birth') }}"></div>';
       	html += '</td>';

       	html += '<td class="text-center"><button type="button" title="{{ trans("messages.delete") }}" onclick="removeTableRrecord(this)" class="btn btn-sm btn-delete-icon"><i class="fa fa-trash"></i></button></td>';
       	html += '</tr>';

       	if( $('.relation-tbody').find('tr').length > 0 ){
      		$(html).insertAfter($('.relation-tbody').find('tr:last'));	
    	} else {
    		$('.relation-tbody').html(html);
    	}
    	reindexTable('relation-tbody');	
        //$("[name='relation_count']").val(relation_count);
       
    	$(function() {
    		$("[name='rel_date_of_birth_"+relation_count+"']").datetimepicker({
    	        useCurrent: false,
    	        viewMode: 'days',
    	        ignoreReadonly: true,
    	        format: 'DD-MM-YYYY',
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
    		 $("[name='rel_date_of_birth_"+relation_count+"']").data('DateTimePicker').maxDate(moment().endOf('d'));
    	});
    }

   
    function addRelationModel(){
        var relation_status = false;
        var relation_name_status = false;
        $('.relation-tbody tr').each(function(){
   			var relation_value = $.trim($(this).find('.relation-row').val());
   			var relation_name_value = $.trim($(this).find('.relation-name-row').val());
   			
   			if(relation_value !="" && relation_value != null ){
   				relation_status = true;
   				if( ( relation_name_value == "" || relation_name_value == null ) && (relation_name_status != true) ){
					$.trim($(this).find('.relation-name-row').focus());
					relation_name_status = true;
            	}
   				
   	   		}
   		});
        
        if( relation_name_status != false ){
     		alertifyMessage("error","{{ trans('messages.require-name') }} ");
        	return false;
        }
    	
    	$("[name='relation_count']").val(relation_count);
    	var formData = new FormData( $('#add-relation-form')[0] );
    	
    	alertify.confirm("{{ trans('messages.update-relation') }}","{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-relation')]) }}",function() { 
	    	 $.ajax({
	     		type: "POST",
	     		dataType :'json',
	     		url: employee_module_url + 'addRelation',
	     		data:formData,
				processData:false,
				contentType:false,
	     		beforeSend: function() {
	     			//block ui
	     			showLoader();
	     		},
	     		success: function(response) {
	     			hideLoader();
	     			if( response.status_code == 1 ){
						$("#relation-model").modal('hide');
						alertifyMessage('success',response.message);
						$('.employee-relation-list').html(response.data.html);
					} else {
						alertifyMessage('error',response.message);
					}
	     		},
	     		error: function() {
	     			hideLoader();
	     		}
	     	});
    	 },function() {});
    }
</script>