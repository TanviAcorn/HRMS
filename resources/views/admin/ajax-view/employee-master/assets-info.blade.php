<div class="assets-details-view h-100">
    <div class="card card-display border-0 px-2 h-100">
        <div class="card-body px-2 py-0">
            <div class="row px-0 border-bottom">
                <div class="col-12 profile-details-title-card">
                    <h5 class="profile-details-title">{{ trans("messages.assets") }}</h5>
                    @if( ( ( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ) || ( ( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ) ) ) )
                    <a href="javascript:void(0);" data-employee-id="{{ (!empty($employeeRecordInfo->i_id) ? Wild_tiger::encode($employeeRecordInfo->i_id) : 0 ) }}" onclick="openEditAssetsModal(this)" title="{{ trans('messages.edit') }}">
                        {{ trans("messages.edit") }}
                    </a>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-12 py-0 profile-display-card">
                    <div class="row pb-0 pt-3">
                        @if(!empty($employeeRecordInfo->v_assets))
                            @php
                                $assets = json_decode($employeeRecordInfo->v_assets, true);
                            @endphp
                            @if(!empty($assets) && is_array($assets))
                                @foreach($assets as $asset)
                                    <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
                                        <div class="asset-item d-flex align-items-center p-3 border rounded bg-light">
                                            <i class="fa fa-check-circle text-success mr-3" style="font-size: 20px;"></i>
                                            <span class="font-weight-normal">{{ $asset }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        <i class="fa fa-info-circle mr-2"></i>{{ trans('messages.no-assets-assigned') }}
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <i class="fa fa-info-circle mr-2"></i>{{ trans('messages.no-assets-assigned') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Assets Modal -->
<div class="modal fade document-folder" id="edit-assets-modal" tabindex="-1" aria-labelledby="editAssetsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAssetsModalLabel">{{ trans("messages.edit-assets") }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times"></i></span>
                </button>
            </div>
            {!! Form::open(array( 'id '=> 'edit-assets-form' , 'method' => 'post' )) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="lable-control">{{ trans('messages.assign-assets') }}</label>
                                <div class="asset-checklist">
                                    @php
                                        $currentAssets = [];
                                        if(!empty($employeeRecordInfo->v_assets)) {
                                            $currentAssets = json_decode($employeeRecordInfo->v_assets, true);
                                            if(!is_array($currentAssets)) {
                                                $currentAssets = [];
                                            }
                                        }
                                        
                                        $adminAssets = [
                                            'Notebook', 'Pen', 'ID Card', 'Headphone', 'Webcam', 
                                            'Mobile', 'SIM Card', 'Dongle'
                                        ];
                                        
                                        $itAssets = [
                                            'PC', 'Laptop', 'Dual Screen',
                                            'Outlook ID - acornuniversalconsultancy.com',
                                            'Outlook ID - astutehealthcare.co.uk',
                                            'Outlook ID - docpharm.de',
                                            'Software - InDesign', 'Software - Nice Label',
                                            'Software - MS Office', 'Software - Acrobat', 'Software - Vonage'
                                        ];
                                    @endphp
                                    
                                    <!-- Admin Assets -->
                                    <h6 class="mt-3 mb-3" style="color: #8B1538; font-weight: 600;">Admin Assets</h6>
                                    <div class="row">
                                        @foreach($adminAssets as $asset)
                                            <div class="col-xl-3 col-sm-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" name="assets[]" value="{{ $asset }}" id="edit_asset_{{ str_replace([' ', '-', '.'], '_', strtolower($asset)) }}" {{ in_array($asset, $currentAssets) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="edit_asset_{{ str_replace([' ', '-', '.'], '_', strtolower($asset)) }}">
                                                        {{ $asset }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- IT Assets -->
                                    <h6 class="mt-4 mb-3" style="color: #8B1538; font-weight: 600;">IT Assets</h6>
                                    <div class="row">
                                        @foreach($itAssets as $asset)
                                            <div class="col-xl-3 col-sm-6">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" name="assets[]" value="{{ $asset }}" id="edit_asset_{{ str_replace([' ', '-', '.'], '_', strtolower($asset)) }}" {{ in_array($asset, $currentAssets) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="edit_asset_{{ str_replace([' ', '-', '.'], '_', strtolower($asset)) }}">
                                                        {{ $asset }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="employee_id" value="{{ (!empty($employeeRecordInfo->i_id) ? Wild_tiger::encode($employeeRecordInfo->i_id) : 0 ) }}">
                <div class="modal-footer justify-content-end">
                    <button type="button" onclick="updateEmployeeAssets()" class="btn bg-theme text-white action-button btn-add" title="{{ trans('messages.update') }}">{{ trans('messages.update') }}</button>
                    <button type="button" class="btn btn-outline-secondary btn-add" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
    function openEditAssetsModal(thisitem) {
        var employee_id = $.trim($(thisitem).attr('data-employee-id'));
        $("[name='employee_id']").val(employee_id);
        openBootstrapModal('edit-assets-modal');
    }

    function updateEmployeeAssets() {
        var employee_id = $.trim($("[name='employee_id']").val());
        var selectedAssets = [];
        
        $("[name='assets[]']:checked").each(function() {
            selectedAssets.push($(this).val());
        });

        alertify.confirm("{{ trans('messages.update-assets') }}", "{{ trans('messages.common-confirm-msg',['module'=> trans('messages.update-assets')]) }}", function() {
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: employee_module_url + 'updateEmployeeAssets',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'employee_id': employee_id,
                    'assets': selectedAssets
                },
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status_code == 1) {
                        $("#edit-assets-modal").modal('hide');
                        alertifyMessage('success', response.message);
                        
                        // Reload the assets section
                        if(response.data && response.data.html) {
                            $('.assets-details-view').html(response.data.html);
                        } else {
                            location.reload();
                        }
                    } else {
                        alertifyMessage('error', response.message);
                    }
                },
                error: function() {
                    hideLoader();
                    alertifyMessage('error', '{{ trans("messages.error-something-went-wrong") }}');
                }
            });
        }, function() {});
    }
</script>
