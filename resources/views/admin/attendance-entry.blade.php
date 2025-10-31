@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
    <div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
        <h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{ trans("messages.attendance-report") }}</h1>
        <span class="head-total-counts total-record-count">1</span>
        <div class="ml-auto pt-sm-0 d-flex align-items-center">
            <button type="button" title="{{ trans('messages.export-excel') }}" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center mr-2"><i class="fas fa-file-excel mr-0 mr-sm-2 fa-fw"></i><span class="d-sm-block d-none">{{ trans("messages.export-excel") }}</span></button>
            <button type="button" class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar d-sm-flex align-items-center" data-toggle="collapse" data-target="#searchFilter" title="{{ trans('messages.filter') }}"><i class="fas fa-filter mr-sm-2"></i> <span class="d-sm-block d-none"> {{ trans("messages.filter") }} </span></button>
        </div>
    </div>
    <div class="container-fluid pt-3 visit-history">
        <?php
        $tableSearchPlaceholder = "Search By Employee Code, Employee Name";
        ?>
        <div class="collapse" id="searchFilter">
            <div class="card card-body mb-3">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-12">
                        <div class="form-group">
                            <label class="control-label" for="buyer_company">{{ trans("messages.employee-name-code") }}</label>
                            <select name="buyer_company" class="form-control select2">
                                <option value="">{{ trans("messages.select") }}</option>
                                <option value="">Deep Suthar (D29042003)</option>
                                <option value="">Jaymin Ahir (D29042003)</option>
                                <option value="">Riyank Shah (D29042003)</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_from_date">{{ trans("messages.from-date") }}</label>
                            <input type="text" name="search_from_date" class="form-control" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="search_to_date">{{ trans("messages.to-date") }}</label>
                            <input type="text" name="search_to_date" class="form-control" placeholder="{{ trans('messages.dd-mm-yyyy') }}">
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="search_team" class="control-label">{{ trans('messages.team') }}</label>
                            <select class="form-control" name="search_team">
                                <option value="">Select</option>
                                <option value="Web Designer">Web Designer</option>
                                <option value="Web developer">Web developer</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md pt-lg-2 d-flex align-items-end gap justify-content-sm-start justify-content-center">
                        <button type="button" class="btn btn-theme text-white mb-3" title="{{ trans('messages.search') }}">{{ trans("messages.search") }}</button>
                        <button type="button" class="btn btn-outline-secondary reset-wild-tigers mb-3" title="{{ trans('messages.reset') }}">{{ trans("messages.reset") }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-result-wrapper">
        	{{ Wild_tiger::readMessage() }}
            {!! Form::open(array( 'id '=> '' , 'method' => 'post' , 'url' => 'add-attendance' )) !!}
            <div class="card card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered text-left">
                        <thead>
                            <tr>
                                <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                                <th class="text-center">start time</th>
                                <th class="text-center">end time</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                        	@if(!empty($allDates))
                        	@php $rowIndex = 0 @endphp
                        		@foreach($allDates as $allDate)
                        			<tr>
                        				<td>{{ ++$rowIndex }} </td>
                        				<td><input type="time" class="form-control" value="09:15:00" name="start_time_<?php echo $allDate ?>" placeholder="start time"></td>
                        				<td><input type="time" class="form-control" value="19:15:00" name="end_time_<?php echo $allDate ?>" placeholder="end time"></td>
                        			</tr>
                        		@endforeach
                        	@endif
						</tbody>
                    </table>
                </div>
            </div>
            <input type="hidden" name="employee_id" value="{{ $employeeId }}">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <button type="submit" class="btn btn-sm btn-success">submit</button>
            {!! Form::close() !!}
        </div>
    </div>


</main>


<script>
    $("[name='search_from_date'],[name='search_to_date']").datetimepicker({
        useCurrent: false,
        ignoreReadonly: true,
        format: 'DD-MM-YYYY',
        showClose: true,
        showClear: true,
        icons: {
            clear: 'fa fa-trash',
        },
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'bottom'
        },
    });
</script>


@endsection