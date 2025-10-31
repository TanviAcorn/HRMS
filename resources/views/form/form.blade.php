
@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')
<main class="page-height">
    <section class="inner-wrapper-common-sections main-listing-section p-3">
        <div class="container-fluid">
            {!! Form::open(array( 'id '=> 'add-banner-form' , 'method' => 'post' ,  'files' => true ,   'url' => 'banner/store' )) !!}
            <div class="row mb-3">
                <div class="col-md-12">
                	<label>First Name</label>
                	<input type="text" class="form-control" name="first_name">
                </div>
            </div>
            <div class="addbtn ">
				<button type="button" onclick="addNewBanner();" class="btn btn-outline-success shadow-sm btn-wide mb-4" title="{{ trans('messages.add-new')}}">Add  New</button>
            </div>
            <div class="center-block text-center">
		    	<button type="button" onclick="addBannerImage(this);" class="btn btn-success submit-button shadow-sm btn-wide" disabled title="{{ trans('messages.upload')}}" >Upload</button>
		        <a href="" class="btn btn-outline-secondary shadow-sm btn-wide reset-btn" title="{{ trans('messages.back')}}" >Back</a>
			</div>
			<input type="hidden" name="new_added_div" value="">
			{!! Form::close() !!}
        </div>
    </section>
</main>
<script>

</script>
@endsection
