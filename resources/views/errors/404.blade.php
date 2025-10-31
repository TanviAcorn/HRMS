<!DOCTYPE html>
<html lang="en">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
	<link rel="icon" href="{{ asset('images/icon.png') }}">
	<title>{{ trans('messages.page-not-found') }} | {{ config('constants.SITE_TITLE', 'HR Managment System Acorn')}} </title>
	<style>
		:root {
			--primary-color: #8d191a;
			--text-gray-color: #838383;
		}

		.text-theme {
			color: var(--primary-color) !important;
		}

		.bg-theme {
			background-color: #005597 !important;
		}

		.not-found {
			font-size: 45px;
		}

		.page-not-found {
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.page-not-found-section {
			background-color: #fafbff;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
		}

		@media(max-width:767px) {
			.not-found{font-size: 28px;}
			.man {
				height: 280px;
				width: auto !important;
			}

			.main-heading {
				font-size: 40px;
				margin-top: 20px;
			}
		}

		.btn.bg-theme {
			background-color: var(--primary-color) !important;
		}
	</style>
</head>

<body>
	<section class="page-not-found-section py-3">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					<?php /* <h1 class="font-weight-bold text-center text-theme not-found">Sorry, Page Not Found!</h1> */ ?>
					<div class="page-not-found">
						<img class="img-fluid" src="{{ asset ('images/page-not-found.png') }}" alt="Page Not Found">
					</div>
				</div>
			</div>
			<div class="button text-center">
				<a href="{{ url('dashboard') }}" class="btn text-white bg-theme"><i class="fas fa-chevron-left pr-1"></i> Back To Home</a>
			</div>
		</div>
	</section>
</body>

</html>