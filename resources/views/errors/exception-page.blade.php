@inject('baseModel', 'App\BaseModel')
<?php 
$settingsInfo = $baseModel->getSingleRecordById( config('constants.SETTING_TABLE') , [ '*' ] );
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{ asset ('css/bootstrap.min.css') }}">
        <script type="text/javascript" src="{{ asset ('js/jquery-3.4.1.min.js') }}"></script>
		<script type="text/javascript" src="{{ asset ('js/bootstrap.bundle.min.js') }}"></script>
        
        <title>{{ trans('messages.exception') }} | {{ isset($settingsInfo) && !empty($settingsInfo->v_site_title) ? $settingsInfo->v_site_title : '' }}</title>
		
		<style>
			.custom-error-section{min-height:100vh; display:flex; align-items: center;}
			.error-text{font-size: 60px;text-align: left;line-height: 1;font-weight: bold;text-transform: uppercase;color: #ff0000;letter-spacing: 3px;}
			.common-link{display: inline-block;padding: 8px 15px;background-color: #3b933f;color: #fff;border-radius: 5px;min-width: 120px;text-decoration: none;border: 0;text-align: center;}
			.common-link:hover{text-decoration:none;color: #fff;}
			@media(max-width:767px){
				.man{width: 100%; object-fit: contain; margin-bottom: 20px;}
				.error-text{font-size: 40px;margin-top: 20px;}
			}
		</style>
	</head>
<body>
	<section class="custom-error-section bg-light">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-8">
					<div class="row">
						<div class="col-md-6 text-center">
							<img src="{{ asset ('images/exception.png') }}" alt="exception" class="img-fluid man">
						</div>
						<div class="col-md-6">
							<h2 class="error-text mb-4">Oops!!!</h2>
							<h4 class="text-muted mb-4">Something went wrong..!!</h4>
							<p class="text-muted h6 mb-4">Please Contact Technical Team to know more about this error.</p>
                             <a href="javascript:void(0)" onclick="redirectPreviousPage()" class="common-link home-page-link">Go Back</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<script>
	function redirectPreviousPage() {
	    window.history.back();
	}
	</script>
</body>
</html>