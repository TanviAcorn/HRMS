<div class="modal fade bd-example-modal-lg" id="add-lookup-modal"  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
             	{!! Form::open(array( 'id '=> 'add-lookup-form' , 'method' => 'post' ,  'url' => '')) !!}
                <div class="modal-header">
                    <h5 class="modal-title twt-modal-header-name" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body add-lookup-modal-html">
                	<div class="row">
						<div class="col-md-12">
					    	<div class="form-group">
					        	<label>{{ trans('messages.value') }}<span class="text-danger">*</span></label>
					            <input type="text" name="module_value" class="form-control" placeholder="{{ trans('messages.value') }}" >
					       	</div>
					   	</div>
					   	<div class="col-md-12 lookup-chart-color" style="display: none;">
					    	<div class="form-group">
					        	<label>{{ trans('messages.chart-display-color') }}</label>
					            <input type="color" name="module_chart_color" class="form-control" placeholder="{{ trans('messages.chart-display-color') }}" >
					       	</div>
					   	</div>
					</div>
                </div>
                <input type="hidden" name="lookup_module_name" value="">
                <input type="hidden" name="lookup_module_record_id" value="">
                <input type="hidden" name="action_type" value="crud">
                 <input type="hidden" name="request_type" value="">
                 <input type="hidden" name="lookup_crud_module" value="">
				<div class="modal-footer justify-content-end">
					<button type="button" class="btn bg-theme text-white action-button lookup-modal-action-button" onclick="addLookup()" title="{{ trans('messages.add') }}">{{ trans('messages.add') }}</button>
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal" title="{{ trans('messages.close') }}">{{ trans('messages.close') }}</button>
				</div>
            {!! Form::close() !!}
        </div>
    </div>
</div>