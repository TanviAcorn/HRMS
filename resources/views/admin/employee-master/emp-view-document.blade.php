<div class="modal fade document-folder document-type" id="view-document-model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
       <div class="modal-dialog modal-dialog-centered modal-lg">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="exampleModalLabel">{{ trans("messages.view") }} <span class="custom-twt-modal-header"></span></h5>
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <span aria-hidden="true"><i class="fas fa-times"></i></span>
                   </button>
               </div>
               <?php /*?>
                {!! Form::open(array( 'id '=> 'add-view-document-form' , 'method' => 'post' ,'url' => 'add')) !!}
                <?php */?>
              		<div class="modal-body">
                       <div class="table-responsive">
                           <table class="table table-hover table-bordered table-sm pb-4">
                               <thead>
                                   <tr class="text-center">
                                       <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                        <th style="max-width:180px;min-width:180px;">{{ trans("messages.file-name") }} </th>
                                      <th style="max-width:180px;min-width:180px;">{{ trans("messages.uploaded-date-and-time") }} </th>
									   <th style="max-width:150px;min-width:100px;">{{ trans("messages.remark") }} </th>
                                       <th style="width:150px;min-width:150px;">{{ trans("messages.actions") }}</th>
                                   </tr>
                               </thead>
                               <tbody class="good-in-buyer-master-tbody view-document-html">
                                </tbody>
                           </table>
                       </div>
                   </div>
                   <?php /*?>
               {!! Form::close() !!}
               <?php */?>
           </div>
       </div>
   </div>	
   <script>
   var employee_module_url = '{{config("constants.EMPLOYEE_MASTER_URL")}}' + '/';
   function getEmployeeDocumentList(thisitem = null ){
		var data_fetch_status = $.trim($(thisitem).attr("data-fetch"));
		var employee_id = $.trim($(thisitem).attr("data-record-id"));
		if ( data_fetch_status == "" || data_fetch_status == null ){
			data_fetch_status = "{{ config('constants.SELECTION_NO') }}";
		}
		if( data_fetch_status != "" && data_fetch_status != null && data_fetch_status == "{{ config('constants.SELECTION_NO') }}"){
			/* if( employee_id != "" && employee_id != null ){ */
				$.ajax({
					type : "POST", //get-emp-doc-list
					url : employee_module_url  +'getEmployeeDocumentList',
					data : { "_token": "{{ csrf_token() }}", 'employee_id' : employee_id },
					beforeSend: function() {
				        //block ui
						showLoader();
				    },success:function(response){
				    	hideLoader();
						if( response != "" && response != null ) {
							$(".emp-document-list").html(response);
							
							//$(thisitem).attr("data-fetch" , "{{ config('constants.SELECTION_YES') }}" );
						}	
					},error:function(){
				    	
				    }
			   });
			/* } */
		}
	}
   function documentDeleteRecordInfo(thisitem){
		 var document_record_id = $.trim($(thisitem).attr('data-record-id'));
		 alertify.confirm("{{ trans('messages.delete-file') }}","{{ trans('messages.confirm-delete-file-msg') }}",function() { 
	  		 $.ajax({
	  	 		type: "POST",
	  	 		url: employee_module_url + 'documentDelete',
	  	 		dataType:'json',
	  	 		data: {
	  	 			"_token": "{{ csrf_token() }}",'document_record_id':document_record_id
	  	 		},
	  	 		beforeSend: function() {
	  	 			//block ui
	  	 			showLoader();
	  	 		},
	  	 		success: function(response) {
	  	 	 		hideLoader();
	  	 	 		if(response.status_code == 1 ){
	  					alertifyMessage('success' , response.message);
	  					$(thisitem).parents('tr').remove();
	  					var record_count = $("#view-document-model").find('.view-document-html tr').length;
	  					
	  					if( record_count == 0 ){
							var no_record_html = '<tr class="text-center"><td colspan="5"><?php echo  trans('messages.no-record-found') ?></td></tr>';
							 $("#view-document-model").find('.view-document-html').html(no_record_html);
		  				} else {
			  				var loop_index = 1;
							$($("#view-document-model").find('.view-document-html tr')).each(function(){
								$(this).find('.sr-index').html(loop_index);
								loop_index++;
							});
			  			}  
	  					//$("#view-document-model").modal('hide');
	  					$(".emp-doc-tab").attr('data-fetch' , '{{ config("constants.SELECTION_NO") }}');
						$(".emp-doc-tab").trigger('click');
						var current_tab_link = window.location.href;
						if( ( current_tab_link.includes("my-documents") != false ) ){
							getEmployeeDocumentList();
						}
						
	  				} else {
	  			    	alertifyMessage('error' , response.message);
	  				}
	  	 			
	  	 		},
	  	 		error: function() {
	  	 			hideLoader();
	  	 		}
	  	 	});
		}, function () { });
	}

   function openViewModel(thisitem){
	 	var documet_type_id = $.trim($(thisitem).attr('data-document-type-record-id'));
	 	var header_documet_type_name = $.trim($(thisitem).attr('data-document-type-name'));
	 	var header_employee_name = $.trim($(thisitem).attr('data-employee-name'));
	 	var document_folder_name = $.trim($(thisitem).attr('data-document-folder-name'));
		var employee_id = "{{ (isset($empId) ? $empId : 0 )}}"

		if( employee_id == "" || employee_id == null || employee_id == 0 ){
			employee_id = $.trim($(thisitem).attr('data-employee-id'));
		}
				
		 $.ajax({
    		type: "POST",
    		url: employee_module_url + 'viewDocumentDetails',
    		data: {
    			"_token": "{{ csrf_token() }}",'documet_type_id':documet_type_id ,'employee_id':employee_id
    		},
    		beforeSend: function() {
    			//block ui
    			showLoader();
    		},
    		success: function(response) {
    			hideLoader();
    			$('.view-document-html').html(response);
    			//$("#view-document-model").find(".custom-twt-modal-header").html(common_emp_modal_header_title);
    			<?php /* var employee_name= "";
				if(header_employee_name !="" && header_employee_name != null){
					employee_name = ' - ' + header_employee_name;
				} else {
					employee_name = common_emp_modal_header_title;
				} */ ?>
    			$("#view-document-model").find(".custom-twt-modal-header").html(' - ' + document_folder_name+' - '+header_documet_type_name);
    			openBootstrapModal('view-document-model');
    		},
    		error: function() {
    			hideLoader();
    		}
    	});
  }
  </script>