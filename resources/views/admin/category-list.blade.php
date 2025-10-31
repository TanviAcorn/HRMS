@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<main class="page-height bg-light-color">
<div class="breadcrumb-wrapper d-sm-flex p-3 border-bottom">
    <h1 class="h3 mb-lg-0 mr-3 header-title"  id="pageTitle">{{ trans("messages.all-categories") }}</h1> 
    <div class="ml-auto pt-sm-0">
        <a href="{{ url('add-category') }}" class="btn btn btn-theme text-white border btn-sm mr-2 button-actions-top-bar" title="{{ trans("messages.add-category") }}"><i class="fas fa-plus"></i> {{ trans("messages.add-category") }}</a>
        <button class="btnbtn btn-theme text-white border btn-sm button-actions-top-bar" data-toggle="collapse" data-target="#searchFilter" title="Toggle Filter"><i class="fas fa-filter"></i> {{ trans("messages.filter") }}</button>
    </div>
</div>
<div class="container-fluid pt-3 visit-history">
    <div class="collapse" id="searchFilter">
        <div class="card card-body mb-3">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="category_name">{{ trans("messages.category-name") }}</label>
                        <input type="text" name="category_name" id="category_name" class="form-control" placeholder="{{ trans('messages.category-name') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="status">{{ trans("messages.status") }}</label>
                        <select class="form-control" name="status">
                            <option value="">{{ trans("messages.select") }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md pt-lg-2">
                    <button type="button" class="btn btn-theme text-white mt-lg-3" >{{ trans("messages.search") }}</button>
                    <button class="btn btn-outline-secondary reset-wild-tigers mt-lg-3">{{ trans("messages.reset") }}</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="filter-result-wrapper">
        <div class="card card-body shadow-sm">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center sr-col">{{ trans("messages.sr-no") }}</th>
                            <th class="text-center" width="55%">{{ trans("messages.category-name") }}</th>
                            <th class="text-center" width="30%">{{ trans("messages.status") }}</th>
                            <th class="actions-col" width="20%">{{ trans("messages.actions") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="sr-col">1</td>
                            <td>Ahmedabad</td>
                            <td class="text-center">Active</td>
                            <td class="actions-col">
                                <a title="Edit Record" href="{{ url('update-category') }}" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
                                <button title="Inactive" class="btn btn-sm btn-warning mb-1"><i class="fa fa-eye-slash fa-fw"></i></button>
                                <button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="sr-col">2</td>
                            <td>Ahmedabad</td>
                            <td class="text-center">Active</td>
                            <td class="actions-col">
                                <a title="Edit Record" href="{{ url('update-category') }}" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
                                <button title="Inactive" class="btn btn-sm btn-warning mb-1"><i class="fa fa-eye-slash fa-fw"></i></button>
                                <button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
                            </td>
                        </tr>

                        <tr>
                            <td class="sr-col">3</td>
                            <td>Ahmedabad</td>
                            <td class="text-center">Active</td>
                            <td class="actions-col">
                                <a title="Edit Record" href="{{ url('update-category') }}" class="btn btn-sm btn-info mb-1"><i class="fas fa-fw fa-pencil-alt"></i></a>
                                <button title="Inactive" class="btn btn-sm btn-warning mb-1"><i class="fa fa-eye-slash fa-fw"></i></button>
                                <button title="Delete" class="btn btn-sm btn-danger mb-1"><i class="fa fa-trash fa-fw"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>
<script>

</script>
@endsection