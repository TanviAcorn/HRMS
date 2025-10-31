@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ $pageTitle }}</h1>
    </div>
   	{!! Form::open(array( 'id '=> 'roles-permissions-form' , 'method' => 'post' ,  'url' => 'role-permission/add')) !!}
        <div class="filter-result-wrapper">
            <div class="container-fluid pt-3">
            <div class="card card-body pb-0">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-4 col-12">
                            <label for="role_name" class="control-label">{{ trans("messages.role-name") }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="role_name" placeholder="{{ trans('messages.role-name') }}" value="{{ old('role_name' , ( (isset($recordInfo) && (!empty($recordInfo->v_role_name)) ? $recordInfo->v_role_name : ''  ) ) ) }}">
                        </div>


                        <div class="form-group col-sm-4 col-12">
                            <label for="role_description" class="control-label">{{ trans("messages.role-description") }}</label>
                            <input type="text" class="form-control" name="role_description" placeholder="{{ trans('messages.role-description') }}" value="{{ old('role_description' , ( (isset($recordInfo) && (!empty($recordInfo->v_role_description)) ? $recordInfo->v_role_description : ''  ) ) ) }}">
                        </div>

                        <div class="col-12 permission-table">
                            <p class="h6 my-3">{{ trans("messages.permission") }}:</p>
                            <div class="table-responsive fixed-tabel-body">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th style=" min-width:120px;">{{ trans("messages.group") }}</th>
                                            <th style=" min-width:120px;">{{ trans("messages.subgroup") }}</th>
                                            <th style=" min-width:180px;">{{ trans("messages.module") }}</th>
                                            <th style=" min-width:120px;">{{ trans("messages.view") }}</th>
                                            <th style=" min-width:120px;">{{ trans("messages.add") }}</th>
                                            <th style=" min-width:120px;">{{ trans("messages.edit") }}</th>
                                            <th style=" min-width:120px;">{{ trans("messages.delete") }}</th>
                                            <th style=" min-width:120px;">{{ trans("messages.all") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="check-box-design">
                                        @if(count($moduleDetails) > 0 )
                                        	
                                            @php
                                                $trStart = false;
                                                $totalRow = 0;
                                            @endphp
                                            @foreach($moduleDetails as $moduleDetail)
                                                @if(isset($moduleDetail->moduleInfo) && !empty($moduleDetail->moduleInfo) && count($moduleDetail->moduleInfo) > 0)
                                                @php $permissionIds = (isset($recordInfo) ? explode("," , $recordInfo->v_permission_ids) : []); @endphp
                                                    <tr class="permission-row module-row menu-row" data-menu-name="<?php echo (isset($moduleDetail->v_menu_name) && !empty($moduleDetail->v_menu_name) ? $moduleDetail->v_menu_name : '') ?>" data-module-name="<?php echo (isset($moduleDetail->moduleInfo[0]->v_module_name) && !empty($moduleDetail->moduleInfo[0]->v_module_name) ? $moduleDetail->moduleInfo[0]->v_module_name : '') ?>">
                                                        <td class="main-td-{{ $loop->iteration }}" rowspan="{{ count($moduleDetail->moduleInfo) }}">
                                                            <div class="form-group mb-0">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input parent-checkbox module-checkbox menu-checkbox" type="checkbox" value="{{ (!empty($moduleDetail->i_id) ? Wild_tiger::encode($moduleDetail->i_id)  : 0 ) }}" id="employees_group_{{ (!empty($moduleDetail->i_id) ? $moduleDetail->i_id  :'') }}" name="employees_group[]">
                                                                    <label class="form-check-label lable-control" for="employees_group_{{ (!empty($moduleDetail->i_id) ? $moduleDetail->i_id  :'') }}">{{ (!empty($moduleDetail->v_menu_name) ? $moduleDetail->v_menu_name  :'') }}</label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        @foreach($moduleDetail->moduleInfo as $moduleInfo)
                                                            @if((isset($moduleInfo->modulePermissionGroup)) && (!empty($moduleInfo->modulePermissionGroup)) && (count($moduleInfo->modulePermissionGroup) > 0 ))
                                                                @if($loop->iteration > 1)
                                                                    <tr class="permission-row module-row" data-menu-name="<?php echo (isset($moduleDetail->v_menu_name) && !empty($moduleDetail->v_menu_name) ? $moduleDetail->v_menu_name : '') ?>" data-module-name="<?php echo (isset($moduleInfo->v_module_name) && !empty($moduleInfo->v_module_name) ? $moduleInfo->v_module_name : '') ?>">
                                                                    @php
                                                                        $trStart = true;
                                                                    @endphp
                                                                @endif

                                                                @php
                                                                    $totalRow += count($moduleInfo->modulePermissionGroup);
                                                                @endphp
                                                                <td rowspan="{{ count($moduleInfo->modulePermissionGroup) }}">
                                                                    <div class="form-group mb-0">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input parent-checkbox module-checkbox" type="checkbox" value="{{ (!empty($moduleInfo->i_id) ? Wild_tiger::encode($moduleInfo->i_id)  : 0 ) }}" id="employees_sub_group_{{ (!empty($moduleInfo->i_id) ? $moduleInfo->i_id :'') }}" name="employees_sub_group[]">
                                                                            <label class="form-check-label lable-control" for="employees_sub_group_{{ (!empty($moduleInfo->i_id) ? $moduleInfo->i_id :'') }}">{{ (!empty($moduleInfo->v_module_name) ? $moduleInfo->v_module_name :'') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                @foreach($moduleInfo->modulePermissionGroup as $modulePermissionGroup)
                                                                    @if($loop->iteration > 1 && !$trStart)
                                                                        <tr class="permission-row" data-menu-name="<?php echo (isset($moduleDetail->v_menu_name) && !empty($moduleDetail->v_menu_name) ? $moduleDetail->v_menu_name : '') ?>" data-module-name="<?php echo (isset($moduleInfo->v_module_name) && !empty($moduleInfo->v_module_name) ? $moduleInfo->v_module_name : '') ?>">
                                                                    @endif
                                                                    <td>
                                                                        <div class="form-group mb-0">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input parent-checkbox" type="checkbox" value="{{ (!empty($modulePermissionGroup->i_id) ? Wild_tiger::encode($modulePermissionGroup->i_id)  : 0 ) }}" id="emp_module_{{ (!empty($modulePermissionGroup->i_id) ? $modulePermissionGroup->i_id :'') }}" name="emp_module[]">
                                                                                <label class="form-check-label lable-control" for="emp_module_{{ (!empty($modulePermissionGroup->i_id) ? $modulePermissionGroup->i_id :'') }}">{{ (!empty($modulePermissionGroup->v_group_name) ? $modulePermissionGroup->v_group_name :'') }}</label>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <?php 
                                                                    $tablePemissionHtml = [
                                                                    		'View' => '',
                                                                    		'Add' => '',
                                                                    		'Edit' => '',
                                                                    		'Delete' => ''
                                                                    ];
                                                                    $tablePemissionHtmlNotAdded = ['View', 'Add', 'Edit', 'Delete' , 'All'];
                                                                    ?>
                                                                     @if((isset($modulePermissionGroup->groupPermission)) && (!empty($modulePermissionGroup->groupPermission)))
                                                                        @foreach($modulePermissionGroup->groupPermission as $groupPermission)
                                                                        	@php $checked = ""; @endphp
				                                        					@if(in_array($groupPermission->i_id,$permissionIds))
				                                        						@php $checked = "checked='checked'"; @endphp
				                                        					@endif
				                                        					<?php 
				                                        					$html = '';
				                                        					$html .= '<td>';
				                                        					$html .= '<div class="form-group mb-0">';
				                                        					$html .= '<div class="form-check form-check-inline">';
				                                        					$html .= '<input type="checkbox" class="form-check-input child-checkbox" value="'.(isset($groupPermission->i_id) && !empty($groupPermission->i_id) ? Wild_tiger::encode($groupPermission->i_id) : '').'" name="view_employees[]" id="view_employees_'.(isset($groupPermission->i_id) && !empty($groupPermission->i_id) ? $groupPermission->i_id : '').'" '.$checked.'>';
				                                        					$html .= '<label class="form-check-label lable-control" for="view_employees_'.(isset($groupPermission->i_id) && !empty($groupPermission->i_id) ? $groupPermission->i_id : '').'">'.(isset($groupPermission->v_title) && !empty($groupPermission->v_title) ? ucwords($groupPermission->v_title) : '').'</label>';
				                                        					$html .= '</div>';
				                                        					$html .= '</div>';
				                                        					$html .= '</td>';
																			if(isset($groupPermission->v_title) && !empty($groupPermission->v_title) && str_contains($groupPermission->v_title, 'View') != false){
																			    if (isset($tablePemissionHtmlNotAdded) && !empty($tablePemissionHtmlNotAdded) && in_array('View', $tablePemissionHtmlNotAdded)){
																			        $tablePemissionHtmlNotAdded = array_diff($tablePemissionHtmlNotAdded, ['View']);
																			        $tablePemissionHtml['View'] = isset($html) && !empty($html) ? $html : '';
																			    }
																			}
																			if(isset($groupPermission->v_title) && !empty($groupPermission->v_title) && str_contains($groupPermission->v_title, 'Add') != false){
																			    if (isset($tablePemissionHtmlNotAdded) && !empty($tablePemissionHtmlNotAdded) && in_array('Add', $tablePemissionHtmlNotAdded)){
																			        $tablePemissionHtmlNotAdded = array_diff($tablePemissionHtmlNotAdded, ['Add']);
																			        $tablePemissionHtml['Add'] = isset($html) && !empty($html) ? $html : '';
																			    }
																			}
																			if(isset($groupPermission->v_title) && !empty($groupPermission->v_title) && str_contains($groupPermission->v_title, 'Edit') != false){
																			    if (isset($tablePemissionHtmlNotAdded) && !empty($tablePemissionHtmlNotAdded) && in_array('Edit', $tablePemissionHtmlNotAdded)){
																			        $tablePemissionHtmlNotAdded = array_diff($tablePemissionHtmlNotAdded, ['Edit']);
																			        $tablePemissionHtml['Edit'] = isset($html) && !empty($html) ? $html : '';
																			    }
																			}
																			if(isset($groupPermission->v_title) && !empty($groupPermission->v_title) && str_contains($groupPermission->v_title, 'Delete') != false){
																			    if (isset($tablePemissionHtmlNotAdded) && !empty($tablePemissionHtmlNotAdded) && in_array('Delete', $tablePemissionHtmlNotAdded)){
																			        $tablePemissionHtmlNotAdded = array_diff($tablePemissionHtmlNotAdded, ['Delete']);
																			        $tablePemissionHtml['Delete'] = isset($html) && !empty($html) ? $html : '';
																			    }
																			}
																			if(isset($groupPermission->v_title) && !empty($groupPermission->v_title) && str_contains($groupPermission->v_title, 'All') != false){
																				if (isset($tablePemissionHtmlNotAdded) && !empty($tablePemissionHtmlNotAdded) && in_array('All', $tablePemissionHtmlNotAdded)){
																					$tablePemissionHtmlNotAdded = array_diff($tablePemissionHtmlNotAdded, ['All']);
																					$tablePemissionHtml['All'] = isset($html) && !empty($html) ? $html : '';
																				}
																			}
				                                        					?>
                                                                        @endforeach
                                                                        <?php 
                                                                        if (isset($tablePemissionHtmlNotAdded) && !empty($tablePemissionHtmlNotAdded)){
                                                                        	foreach ($tablePemissionHtmlNotAdded as $tablePemissionHtmlNotAddedInfo){
                                                                        		$tablePemissionHtml[$tablePemissionHtmlNotAddedInfo] = '<td></td>';
                                                                        	}
                                                                        }
                                                                        ?>
                                                                        {!! isset($tablePemissionHtml) && !empty($tablePemissionHtml) ? implode(' ', $tablePemissionHtml) : '' !!}
                                                                    @endif
                                                                    </tr>
                                                                    @php
                                                                        $trStart = false;
                                                                    @endphp
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                @endif

                                                <script>
                                                    $(document).ready(function(){
                                                        $('.main-td-{{ $loop->iteration }}').attr('rowspan', '{{ $totalRow }}');
                                                    })
                                                </script>
                                                @php $totalRow = 0; @endphp
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
						
                        <div class="col-md-12 submit-sticky">
                        	@if (isset($recordInfo) && ($recordInfo->i_id > 0))
                        		<input type="hidden" name="record_id" value="{{ Wild_tiger::encode($recordInfo->i_id) }}">
                            	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.update') }}">{{ trans("messages.update") }}</button>
                            @else
                            	<button type="submit" class="btn btn bg-theme text-white btn-wide" title="{{ trans('messages.submit') }}">{{ trans("messages.submit") }}</button>
                            @endif
                            <a href="{{ config('constants.ROLES_AND_PERMISSION_MASTER_URL') }}" class="btn btn-outline-secondary shadow-sm btn-wide" title="{{ trans('messages.cancel') }}">{{ trans("messages.cancel") }}</a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    {!! Form::close() !!}
</main>



<script>
var role_permission_module_url = '{{config("constants.ROLES_AND_PERMISSION_MASTER_URL")}}' + '/';

    $("#roles-permissions-form").validate({
        errorClass: "invalid-input",
        onfocusout: false,
		onkeyup: false,
        rules: {
            role_name: {
                required: true,
                noSpace :true,
                checkUniqueRoleName:true
            },
        },
        messages: {
            role_name: {
                required: "{{ trans('messages.require-role-name') }}"
            },
        },
        submitHandler: function(form) {
        	var confirm_box = "";
    	    var confirm_box_msg = "";
    	    
    	    <?php if(isset($recordInfo) && ($recordInfo->i_id > 0)){ ?>
    	    	confirm_box = "{{ trans('messages.update-roles-permissions') }}";
    	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-roles-permissions')]) }}";
    	    <?php } else { ?>
    	    	confirm_box = "{{ trans('messages.add-roles-permissions') }}";
    	    	confirm_box_msg = "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.add-roles-permissions')]) }}";
    	    	
    	   <?php }?>
    	    alertify.confirm(confirm_box,confirm_box_msg,function() {   
            	showLoader()
           	 	form.submit();
    	    },function() {});
        }
    });

    $.validator.addMethod("checkUniqueRoleName", function(value, element){
    	var result = true;
    	$.ajax({
    		type: "POST",
    		async: false,
    		url: role_permission_module_url +'checkUniqueRoleName',
    		dataType: "json",
    		data: {
    			"_token": "{{ csrf_token() }}",
    			'role_name': $.trim($("[name='role_name']").val()),'record_id': ( $.trim($("[name='record_id']").val()) != '' ? $.trim($("[name='record_id']").val()) : null) 
    		     },
    		beforeSend: function() {
    			
    		},
    		success: function (response) {
    			if (response.status_code == 1) {
    				return false;
    			} else {
    				result = false;
    				return true;
    			}
    		}
    	});
    	return result;

    },'{{ trans("messages.error-unique-role-name") }}');

    $(function() {
		$('.parent-checkbox').on('click', function() {
			var check_module_class = $(this).parents('tr').hasClass('module-row');
			var check_menu_class = $(this).parents('tr').hasClass('menu-row');
			if(check_menu_class != false && check_module_class != false) {
				if ($(this).hasClass('menu-checkbox')) {
					var menu_name = $(this).parents('tr').attr('data-menu-name');
					$("tr[data-menu-name='" + menu_name + "']").find(".parent-checkbox").prop('checked', $(this).prop('checked'));
					$("tr[data-menu-name='" + menu_name + "']").find(".child-checkbox").prop('checked', $(this).prop('checked'));
				} else if($(this).hasClass('module-checkbox')){	
					var module_name = $(this).parents('tr').attr('data-module-name');
					var menu_name = $(this).parents('tr').attr('data-menu-name');
					$("tr[data-module-name='" + module_name + "']").find(".parent-checkbox").prop('checked', $(this).prop('checked'));
					$("tr[data-module-name='" + module_name + "']").find(".child-checkbox").prop('checked', $(this).prop('checked'));
					$("tr[data-menu-name='" + menu_name + "']").find(".menu-checkbox").prop('checked', false);
				} else {
					if ($(this).prop('checked') != false) {
						$(this).parents('.permission-row').find('.child-checkbox').prop('checked', true);
					} else if ($(this).prop('checked') != true) {
						$(this).parents('.permission-row').find('.child-checkbox').prop('checked', false);
					}
				}
			} else if(check_menu_class != false){		
				if ($(this).hasClass('menu-checkbox')) {
					var menu_name = $(this).parents('tr').attr('data-menu-name');
					$("tr[data-menu-name='" + menu_name + "']").find(".parent-checkbox").prop('checked', $(this).prop('checked'));
					$("tr[data-menu-name='" + menu_name + "']").find(".child-checkbox").prop('checked', $(this).prop('checked'));
				} else {
					if ($(this).prop('checked') != false) {
						$(this).parents('.permission-row').find('.child-checkbox').prop('checked', true);
					} else if ($(this).prop('checked') != true) {
						$(this).parents('.permission-row').find('.child-checkbox').prop('checked', false);
					}
				}
			} else if (check_module_class != false) {
				if ($(this).hasClass('module-checkbox')) {
					var module_name = $(this).parents('tr').attr('data-module-name');
					$("tr[data-module-name='" + module_name + "']").find(".parent-checkbox").prop('checked', $(this).prop('checked'));
					$("tr[data-module-name='" + module_name + "']").find(".child-checkbox").prop('checked', $(this).prop('checked'));
				} else {
					if ($(this).prop('checked') != false) {
						$(this).parents('.permission-row').find('.child-checkbox').prop('checked', true);
					} else if ($(this).prop('checked') != true) {
						$(this).parents('.permission-row').find('.child-checkbox').prop('checked', false);
					}
				}
			} else {
				
				if ($(this).prop('checked') != false) {					
					$(this).parents('.permission-row').find('.child-checkbox').prop('checked', true);
				} else if ($(this).prop('checked') != true) {
					$(this).parents('.permission-row').find('.child-checkbox').prop('checked', false);
				}
			}
		})

	})
   
   $(document).ready(function(){
		$('.parent-checkbox').each(function(){
			var checkbox_length = $.trim($(this).parents('tr').find('.child-checkbox').length);
			var checked_checkbox_length = $.trim($(this).parents('tr').find('.child-checkbox:checked').length);

			if( ( checkbox_length > 0 ) && ( checked_checkbox_length > 0 ) && ( checkbox_length == checked_checkbox_length ) ){
				if(!$(this).hasClass('module-checkbox')){
					$(this).prop('checked' , true);
				}
			}
		})
   });
</script>

@endsection