@extends('includes/header') @section('pageTitle', $pageTitle )

@section('content')
<main class="page-height bg-light-color">
<div class="breadcrumb-wrapper d-flex align-items-center border-bottom">
	<div class="container-fluid">
		<h1 class="h3 mb-lg-0 mr-3 header-title mb-0" id="pageTitle">{{
			trans("messages.access-denied") }}</h1>
	</div>
</div>
<section
	class="inner-wrapper-common-section access-denied-section main-listing-section d-flex align-items-center justify-content-center chnage-password py-3">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 d-flex align-items-center flex-column">
					<div class="page-not-found">
						<img class="img-fluid access-denied-img"
							src="{{ asset ('images/access-denied.png') }}"
							alt="Page Not Found">
					</div>
					<div class="custom-card-header main-inner-card">
						<div class="custom-breadcrumb">
							<h2
								class="font-weight-bold text-center text-theme access-denied-title mb-0 py-2">{{
								trans("messages.access-denied") }}</h2>
							<p class="access-denied-discription">
								You Currently does not have access to this page. <br> Please try
								again later.
							</p>
						</div>
						<div class="button text-center pb-xl-0 pb-3">
							<a href="{{ url('dashboard') }}"
								class="btn text-white bg-theme py-2"><i
								class="fas fa-chevron-left pr-1"></i> Back To Home</a>
						</div>
					</div>
			</div>
		</div>
	</div>
</section>
</main>
@endsection
