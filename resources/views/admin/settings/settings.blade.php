
@extends('includes/header')

@section('pageTitle', $pageTitle )

@section('content')

<div class="breadcrumb-wrapper d-lg-flex p-3 border-bottom">
    <h1 class="h3 mb-lg-0 mr-3"  id="pageTitle">{{ $pageTitle }}</h1>
</div>
<div class="setting-part setting-tabs">
	<?php /*
	if( (empty($settingsInfo)) || (  (!empty($settingsInfo)) && ( $settingsInfo->t_contact_settings_tab == 1 ) ) ){ ?>
		<a class="setting-link text-decoration-none d-line-block" href="#contact_settings" title="{{ trans('messages.contact-settings')}}">{{ trans("messages.contact-settings")}}</a>
	<?php }
	if( (empty($settingsInfo)) || ( (!empty($settingsInfo)) && ( $settingsInfo->t_social_links_tab == 1 ) ) ){?>
		<a class="setting-link text-decoration-none d-line-block" href="#social_links" title="{{ trans('messages.social-links')}}">{{ trans("messages.social-links") }}</a>
	<?php } ?>
	<?php */?>
	<?php 
	if( (empty($settingsInfo)) || ( (!empty($settingsInfo)) && ( $settingsInfo->t_smtp_connection_tab == 1 ) ) ){?>
		<a class="setting-link text-decoration-none d-line-block" href="#smtp-connection" title="{{ trans('messages.smtp-connection')}}">{{ trans("messages.smtp-connection")}}</a>
	<?php }
	if( (empty($settingsInfo)) || ( (!empty($settingsInfo)) && ( $settingsInfo->t_site_info_tab == 1 ) ) ){?>
		<a class="setting-link text-decoration-none d-line-block" href="#site-info" title="{{ trans('messages.policy-info')}}">{{ trans("messages.policy-info")}}</a>
	<?php }
	if( (empty($settingsInfo)) || ( (!empty($settingsInfo)) && ( $settingsInfo->t_logo_settings_tab == 1 ) ) ){?>
		<a class="setting-link text-decoration-none d-line-block" href="#logo-settings" title="{{ trans('messages.logo-settings')}}">{{ trans("messages.logo-settings")}}</a>
	<?php }?>
	<?php if( config('constants.SHOW_DEVELOPER_SETTINGS') == 1 ){?>
		<a class="setting-link text-decoration-none d-line-block" href="#developer-settings" title="{{ trans('messages.developer-settings')}}">{{ trans("messages.developer-settings")}}</a>
	<?php }?>
</div>

<div class="container-fluid pt-3">
	<div class="settings-cards">
	 {{ Wild_tiger::readMessage() }}
     {!! Form::open(array( 'id '=> 'add-settings-form' , 'method' => 'post' ,  'files' => true , 'url' => 'settings/add')) !!}
     
     <?php /* if( (empty($settingsInfo)) || (  (!empty($settingsInfo)) && ( $settingsInfo->t_contact_settings_tab == 1 ) ) ){
			?>
			 
		<div class="card mb-3 shadow-sm" id="contact_settings">
			<div class="card-header">
				<h2 class="h4 mb-0">{{ trans("messages.contact-settings") }}</h2>
			</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label" for="primary_mobile_no">{{ trans("messages.primary-mobile-no") }}:</label>
								<input type="text" class="form-control" name="primary_mobile_no" id="primary_mobile_no" placeholder="{{ trans('messages.primary-mobile-no') }}" onkeyup="onlyNumberWithSpaceAndPlusSign(this)" minlength="8" maxlength="16" value="{{ old('primary_mobile_no' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_primary_mobile_no)) ? $settingsInfo->v_primary_mobile_no : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label" for="secondary_mobile_no">{{ trans("messages.secondary-mobile-no") }}:</label>
								<input type="text" class="form-control" name="secondary_mobile_no" id="secondary_mobile_no" placeholder="{{ trans("messages.secondary-mobile-no") }}" onkeyup="onlyNumberWithSpaceAndPlusSign(this)" minlength="8" maxlength="16" value="{{ old('secondary_mobile_no' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_secondary_mobile_no)) ? $settingsInfo->v_secondary_mobile_no : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-lg-6 d-none">
							<div class="form-group">
								<label class="control-label" for="other_mobile_no">{{ trans("messages.other-mobile-no") }}:</label>
								<input type="text" class="form-control" name="other_mobile_no" id="other_mobile_no" placeholder='{{ trans("messages.other-mobile-no") }}' onkeyup="onlyNumberWithSpaceAndPlusSign(this)" minlength="8" maxlength="16" value="{{ old('other_mobile_no' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_other_mobile_no)) ? $settingsInfo->v_other_mobile_no : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label" for="whatsapp_no">{{ trans("messages.whatsapp-no") }}:</label>
								<input type="text" class="form-control" name="whatsapp_no" id="whatsapp_no" placeholder='{{ trans("messages.whatsapp-no") }}' onkeyup="onlyNumberWithSpaceAndPlusSign(this)" minlength="8" maxlength="16" value="{{ old('whatsapp_no' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_whatsapp_number)) ? $settingsInfo->v_whatsapp_number : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label" for="whatsapp_no_position">{{ trans("messages.whatsapp-no-position") }}:</label>
								<select class="form-control shadow-sm position-value" name="whatsapp_no_position" id="whatsapp_no_position">
									<option value="">{{ trans("messages.select") }}</option>
									<option value="{{ config('constants.LEFT')}}"<?php echo ( ( (!empty($settingsInfo->e_whatsapp_position)) && ($settingsInfo->e_whatsapp_position ==  config('constants.LEFT') ) ) ? 'selected="selected"' : '' ) ?> >{{ trans("messages.left") }}</option>
									<option value="{{ config('constants.RIGHT')}}" <?php echo ( ( (!empty($settingsInfo->e_whatsapp_position)) && ($settingsInfo->e_whatsapp_position == config('constants.RIGHT')) ) ? 'selected="selected"' : '' ) ?>>{{ trans("messages.right") }}</option>
								</select>
							</div>
						</div>
						
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label" for="working_hours">{{ trans("messages.monday-saturday") }}:</label>
								<input type="text" class="form-control" name="working_hours" id="working_hours" placeholder="{{ trans("messages.monday-saturday") }}" value="{{ old('working_hours' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_working_hours)) ? $settingsInfo->v_working_hours : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label" for="working_days">{{ trans("messages.sunday") }}:</label>
								<input type="text" class="form-control" name="working_days" id="working_days" placeholder="{{ trans("messages.sunday") }}" value="{{ old('working_days' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_working_days)) ? $settingsInfo->v_working_days : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label" for="email">{{ trans("messages.email") }}:</label>
								<input type="text" class="form-control" name="email" id="email" placeholder='{{ trans("messages.email") }}' value="{{ old('email' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_email)) ? $settingsInfo->v_email : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label" for="google_map">{{ trans("messages.google-map") }}:</label>
								<textarea class="form-control" id="google_map" name="google_map" rows="5">{{ old('google_map' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_google_map)) ? html_entity_decode( stripslashes( $settingsInfo->v_google_map ) ) : ''  ) ) ) }}</textarea>
							</div>
						</div>
						<div class="col-lg-12 d-none">
							<div class="form-group">
								<label class="control-label" for="short_address">{{ trans("messages.short-address") }}:</label>
								<input type="text" class="form-control" name="short_address" id="short_address" placeholder="{{ trans("messages.short-address") }}" value="{{ old('short_address' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_short_address)) ? $settingsInfo->v_short_address : ''  ) ) ) }}">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="address" class="control-label">{{ trans("messages.address") }}:</label>
								<textarea class="form-control" id="address" name="address" rows="3">{{ old('address' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_address)) ? html_entity_decode($settingsInfo->v_address) : ''  ) ) ) }}</textarea>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			} 
			if( (empty($settingsInfo)) || ( (!empty($settingsInfo)) && ( $settingsInfo->t_social_links_tab == 1 ) ) ){
			?>
				<div class="card mb-3 shadow-sm" id="social_links">
					<div class="card-header">
						<h2 class="h4 mb-0">{{ trans("messages.social-links") }}</h2>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="facebook">{{ trans("messages.facebook") }}:</label>
									<input type="text" class="form-control" name="facebook" id="facebook" placeholder="{{ trans("messages.facebook") }}" value="{{ old('facebook' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_facebook_link)) ? $settingsInfo->v_facebook_link : ''  ) ) ) }}" >
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="instagram">{{ trans("messages.instagram") }}:</label>
									<input type="text" class="form-control" name="instagram" id="instagram" placeholder="{{ trans("messages.instagram") }}" value="{{ old('instagram' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_instagram_link)) ? $settingsInfo->v_instagram_link : ''  ) ) ) }}">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="youtube">{{ trans("messages.youtube") }}:</label>
									<input type="text" class="form-control" name="youtube" id="youtube" placeholder="{{ trans("messages.youtube") }}" value="{{ old('youtube' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_youtube_link)) ? $settingsInfo->v_youtube_link : ''  ) ) ) }}">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="linkedin">{{ trans("messages.linkedin") }}:</label>
									<input type="text" class="form-control" name="linkedin" id="linkedin" placeholder="{{ trans("messages.linkedin") }}" value="{{ old('linkedin' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_linkedin_link)) ? $settingsInfo->v_linkedin_link : ''  ) ) ) }}">
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label class="control-label" for="twitter">{{ trans("messages.twitter") }}:</label>
									<input type="text" class="form-control" name="twitter" id="twitter" placeholder="{{ trans("messages.twitter") }}" value="{{ old('twitter' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_twitter_link)) ? $settingsInfo->v_twitter_link : ''  ) ) ) }}">
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php 
			}?>
			<?php */?>
			<?php 
			if( (empty($settingsInfo)) || ( (!empty($settingsInfo)) && ( $settingsInfo->t_smtp_connection_tab == 1 ) ) ){?>
				<div class="card mb-3 shadow-sm" id="smtp-connection">
					<div class="card-header">
						<h2 class="h4 mb-0">{{ trans("messages.smtp-connection") }}</h2>
					</div>
					<div class="card-body">
						<div class="row">
							<?php /* ?>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="contact_receive_mail">{{ trans("messages.contact-receive-mail") }}:</label>
									<input type="text" class="form-control" name="contact_receive_mail" id="contact_receive_mail" placeholder="{{ trans("messages.contact-receive-mail") }}" value="<?php echo old('contact_receive_mail' , ( !empty( $settingsInfo->v_contact_receive_mail ) ? $settingsInfo->v_contact_receive_mail : '' ) ) ?>">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="default_cc_mail">{{ trans("messages.default-cc-mail") }}:</label>
									<input type="text" class="form-control" name="default_cc_mail" id="default_cc_mail" placeholder="{{ trans("messages.default-cc-mail") }}" value="<?php echo old('default_cc_mail' , ( !empty( $settingsInfo->v_default_cc_mail ) ? $settingsInfo->v_default_cc_mail : '' ) ) ?>">
								</div>
							</div>
							<?php  */ ?>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="send_email_protocol">{{ trans("messages.send-email-protocol") }}:</label>
									<input type="text" class="form-control" name="send_email_protocol" id="send_email_protocol" placeholder="{{ trans("messages.send-email-protocol") }}"value="<?php echo old('send_email_protocol' , ( !empty( $settingsInfo->v_send_email_protocol ) ? $settingsInfo->v_send_email_protocol : '' ) ) ?>" >
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="send_email_host">{{ trans("messages.send-email-host") }}:</label>
									<input type="text" class="form-control" name="send_email_host" id="send_email_host" placeholder="{{ trans("messages.send-email-host") }}" value="<?php echo old('send_email_host' , ( !empty( $settingsInfo->v_send_email_host ) ? $settingsInfo->v_send_email_host : '' ) ) ?>">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="send_email_port">{{ trans("messages.send-email-port") }}:</label>
									<input type="text" class="form-control" name="send_email_port" id="send_email_port" placeholder="{{ trans("messages.send-email-port") }}" value="<?php echo old('send_email_port' , ( !empty( $settingsInfo->i_send_email_port ) ? $settingsInfo->i_send_email_port : '' ) ) ?>" >
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="send_email_user">{{ trans("messages.send-email-user") }}:</label>
									<input type="text" class="form-control" name="send_email_user" id="send_email_user" autocomplete="new_send_email_user" placeholder="{{ trans("messages.send-email-user") }}" value="<?php echo old('send_email_user' , ( !empty( $settingsInfo->v_send_email_user ) ? $settingsInfo->v_send_email_user : '' ) ) ?>">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="send_email_password">{{ trans("messages.send-email-password") }}:</label>
									<input type="password" class="form-control" name="send_email_password" id="send_email_password" autocomplete="new-password" placeholder="{{ trans("messages.send-email-password") }}" value="<?php echo old('send_email_password' , ( !empty( $settingsInfo->v_send_email_password ) ? $settingsInfo->v_send_email_password : '' ) ) ?>">
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php 
			}
			if( (empty($settingsInfo)) || ( (!empty($settingsInfo)) && ( $settingsInfo->t_site_info_tab == 1 ) ) ){?>
				<div class="card mb-3 shadow-sm" id="site-info">
					<div class="card-header">
						<h2 class="h4 mb-0">{{ trans("messages.policy-info") }}</h2>
					</div>
					<div class="card-body">
						<div class="row">
							<?php /* ?>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="site_title">{{ trans("messages.site-title") }}:</label>
									<input type="text" class="form-control" name="site_title" id="site_title" placeholder="{{ trans("messages.site-title") }}" value="<?php echo old('site_title' , ( !empty( $settingsInfo->v_site_title ) ? $settingsInfo->v_site_title : '' ) ) ?>">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="site_keywords">{{ trans("messages.site-keywords") }}:</label>
									<input type="text" class="form-control" name="site_keywords" id="site_keywords" placeholder="{{ trans("messages.site-keywords") }}" value="<?php echo old('site_keywords' , ( !empty( $settingsInfo->v_site_keywords ) ? $settingsInfo->v_site_keywords : '' ) ) ?>">
								</div>
							</div>
							<?php */ ?>
							<?php /*
							<div class="col-lg-12">
								<div class="form-group">
									<label class="control-label" for="about_short_description">{{ trans("messages.about-short-description") }}:</label>
									<textarea class="form-control" id="about_short_description" name="about_short_description" rows="3">{{ old('about_short_description' , ( !empty( $settingsInfo->v_about_short_description ) ? html_entity_decode($settingsInfo->v_about_short_description) : '' ) ) }}</textarea>
								</div>
							</div>
							*/?>
							<?php /* ?>
							<div class="col-md-12">
								<div class="form-group">
									<label for="site_description" class="control-label">{{ trans("messages.site-description") }}:</label>
									<textarea class="form-control" id="site_description" name="site_description" rows="3">{{ old('site_description' , ( !empty( $settingsInfo->v_site_description ) ? $settingsInfo->v_site_description : '' ) )}}</textarea>
								</div>
							</div>
							<?php */ ?>
							<div class="col-md-12">
								<div class="form-group">
									<label for="time_off_policy" class="control-label">{{ trans("messages.time-off-policy") }}:</label>
									<textarea class="form-control" id="time_off_policy" name="time_off_policy" rows="3">{{ old('time_off_policy' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_time_off_policy)) ? html_entity_decode($settingsInfo->v_time_off_policy) : ''  ) ) ) }}</textarea>
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<label for="leave_policy" class="control-label">{{ trans("messages.leave-policy") }}:</label>
									<textarea class="form-control" id="leave_policy" name="leave_policy" rows="3">{{ old('leave_policy' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_leave_policy)) ? html_entity_decode($settingsInfo->v_leave_policy) : ''  ) ) ) }}</textarea>
								</div>
							</div>
						</div>
					</div>
					
				</div>
			<?php 
			}
			if( (empty($settingsInfo)) || ( (!empty($settingsInfo)) && ( $settingsInfo->t_logo_settings_tab == 1 ) ) ){
				$websiteLogo = ( ( isset($settingsInfo) && (!empty($settingsInfo->v_website_logo)) ) ? config('constants.FILE_STORAGE_PATH_URL').config('constants.UPLOAD_FOLDER').($settingsInfo->v_website_logo) : '' );           
                $websiteFooterLogo = ( ( isset($settingsInfo) && (!empty($settingsInfo->v_website_footer_logo)) ) ? config('constants.FILE_STORAGE_PATH_URL').config('constants.UPLOAD_FOLDER').($settingsInfo->v_website_footer_logo) : '' );           
                $websiteFavIcon = ( ( isset($settingsInfo) && (!empty($settingsInfo->v_website_fav_icon)) ) ? config('constants.FILE_STORAGE_PATH_URL').config('constants.UPLOAD_FOLDER').($settingsInfo->v_website_fav_icon) : '' );           
                $websiteOgIcon = ( ( isset($settingsInfo) && (!empty($settingsInfo->v_website_og_icon)) ) ? config('constants.FILE_STORAGE_PATH_URL').config('constants.UPLOAD_FOLDER').($settingsInfo->v_website_og_icon) : '' );
                //$defaultImage = config('constants.STATIC_IMAGE_PATH');
                $defaultImage = '';
                ?>
				<div class="card mb-3 shadow-sm" id="logo-settings">
					<div class="card-header">
						<h2 class="h4 mb-0">{{ trans("messages.logo-settings") }}</h2>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-3">
								<div class="row website-logo-div image-start-div">
									<div class="col-12 form-group">
										<label class="control-label" for="logo_image">{{ trans("messages.header-logo") }}:</label>
										<div class="custom-file mb-3">
											<input type="file" class="custom-file-input" id="logo_image" name="logo_image" onchange="imagePreview(this)">
											<label class="custom-file-label text-truncate" for="logo_image">{{ ( ( isset($settingsInfo) && (!empty($settingsInfo->v_website_logo)) ) ? basename($settingsInfo->v_website_logo) : trans('messages.choose-file') ) }}</label>
											<label id="logo_image-error" class="invalid-input" for="logo_image" style="display: none;"></label>
										</div>
									</div>
									<div class="col-12 position-relative">
										<div class="remove-banner-btn setting-remove-btn hide-button ">
											<?php
												if(!empty($settingsInfo->v_website_logo)){
													?>
													<button type="button" class="btn btn-sm btn-danger rounded-circle close-button" onclick="removeLogo(this)"> <i class="fas fa-fw fa-times"></i> </button>
													<?php 
												}
											?>
										</div>
										<div class="mb-3 preview-image-div logo_image-preview-div">
											<img src="<?php echo (!empty($websiteLogo) ? $websiteLogo : $defaultImage) ?>" alt="{{ config('constants.SITE_TITLE') }}" class="setting-logo preview-image border-0 file-upload-preview img-fluid logo_image-preview">
										</div>
									</div>
								</div>
							</div>
							
							<div class="col-lg-3">
								<div class="row website-footer-logo-div image-start-div">
									<div class="col-12 form-group">
										<label class="control-label" for="footer_logo_image">{{ trans("messages.footer-logo") }}:</label>
										<div class="custom-file mb-3">
											<input type="file" class="custom-file-input" id="footer_logo_image" name="footer_logo_image" onchange="imagePreview(this)">
											<label class="custom-file-label text-truncate" for="footer_logo_image">{{ ( ( isset($settingsInfo) && (!empty($settingsInfo->v_website_footer_logo)) ) ? basename($settingsInfo->v_website_footer_logo) : trans('messages.choose-file') ) }}</label>
											<label id="footer_logo_image-error" class="invalid-input" for="footer_logo_image" style="display: none;"></label>
										</div>
									</div>
									<div class="col-12 position-relative">
										<div class="remove-banner-btn setting-remove-btn hide-button ">
											<?php
												if(!empty($settingsInfo->v_website_footer_logo)){
													?>
													<button type="button" class="btn btn-sm btn-danger rounded-circle close-button" onclick="removeFooterLogo(this)"> <i class="fas fa-fw fa-times"></i> </button>
													<?php 
												}
											?>
										</div>
										<div class="mb-3 preview-image-div footer_logo_image-preview-div">
											<img src="<?php echo (!empty($websiteFooterLogo) ? $websiteFooterLogo : $defaultImage) ?>" alt="{{ config('constants.SITE_TITLE') }}" class="setting-logo preview-image border-0 file-upload-preview img-fluid footer_logo_image-preview">
										</div>
									</div>
								</div>
							</div>
							
							
							<div class="col-lg-3">
								<div class="row website-fav-icon-div image-start-div">
									<div class="col-12 form-group">
										<label class="control-label" for="fav_icon_image">{{ trans("messages.fav-icon") }}:</label>
										<div class="custom-file mb-3">
											<input type="file" class="custom-file-input" id="fav_icon_image" name="fav_icon_image" onchange="imagePreview(this)">
											<label class="custom-file-label text-truncate" for="fav_icon_image">{{ ( ( isset($settingsInfo) && (!empty($settingsInfo->v_website_fav_icon)) ) ? basename($settingsInfo->v_website_fav_icon) : trans('messages.choose-file') ) }}</label>
											<label id="fav_icon_image-error" class="invalid-input" for="fav_icon_image" style="display: none;"></label>
										</div>
									</div>
									<div class="col-12 position-relative">
										<div class="remove-banner-btn hide-button setting-remove-btn">
											<?php
												if(!empty($settingsInfo->v_website_fav_icon)){
													?>
													<button type="button" class="btn btn-sm btn-danger rounded-circle close-button" onclick="removeFavIcon(this)"> <i class="fas fa-fw fa-times"></i> </button>
													<?php 
												}
											?>
										</div>
										<div class="mb-3 preview-image-div fav_icon_image-preview-div">
											<img src="<?php echo (!empty($websiteFavIcon) ? $websiteFavIcon : $defaultImage)?>" alt="{{ config('constants.SITE_TITLE') }}" class="setting-logo preview-image border-0 file-upload-preview img-fluid fav_icon_image-preview">
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="row website-og-icon-div image-start-div">
									<div class="col-12 form-group">
										<label class="control-label" for="og_icon_image">{{ trans("messages.og-icon") }}:</label>
										<div class="custom-file mb-3">
											<input type="file" class="custom-file-input" id="og_icon_image" name="og_icon_image" onchange="imagePreview(this)">
											<label class="custom-file-label text-truncate" for="og_icon_image">{{ ( ( isset($settingsInfo) && (!empty($settingsInfo->v_website_og_icon)) ) ? basename($settingsInfo->v_website_og_icon) : trans('messages.choose-file') ) }}</label>
											<label id="og_icon_image-error" class="invalid-input" for="og_icon_image" style="display: none;"></label>
											<label><small>Size should be 200x200 px.</small></label>
										</div>
									</div>
									<div class="col-12 position-relative">
										<div class="remove-banner-btn hide-button setting-remove-btn">
											<?php 
												if(!empty($settingsInfo->v_website_og_icon)){
													?>
													<button type="button" class="btn btn-sm btn-danger rounded-circle close-button" onclick="removeOgIcon(this)"> <i class="fas fa-fw fa-times"></i> </button>
													<?php 
												}
											?>
										</div>
										<div class="mb-3 preview-image-div og_icon_image-preview-div">
											<img src="<?php echo (!empty($websiteOgIcon) ? $websiteOgIcon :$defaultImage)?>" alt="{{ config('constants.SITE_TITLE') }}" class="setting-logo border-0 preview-image file-upload-preview img-fluid	 og_icon_image-preview">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php }
			 if( config('constants.SHOW_DEVELOPER_SETTINGS') == 1 ){?>
			<div class="card shadow-sm" id="developer-settings">
					<div class="card-header">
						<h2 class="h4 mb-0">{{ trans("messages.developer-settings") }}</h2>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="version">{{ trans("messages.version") }}:</label>
									<input type="text" class="form-control" name="version" id="version" placeholder="{{ trans("messages.version") }}" value="{{ old('version' , ( (isset($settingsInfo) && (!empty($settingsInfo->d_version)) ? $settingsInfo->d_version : ''  ) ) ) }}"  onkeyup="onlyDecimal(this)">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="site_name">{{ trans("messages.site-name") }}:</label>
									<input type="text" class="form-control" name="site_name" id="site_name" placeholder="{{ trans("messages.site-name") }}" value="{{ old('site_name' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_site_name)) ? $settingsInfo->v_site_name : ''  ) ) ) }}">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="meta_author">{{ trans("messages.meta-author") }}:</label>
									<input type="text" class="form-control" name="meta_author" id="meta_author" placeholder="{{ trans("messages.meta-author") }}" value="{{ old('meta_author' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_meta_author)) ? $settingsInfo->v_meta_author : ''  ) ) ) }}">
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="control-label" for="powered_by">{{ trans("messages.powered-by") }}:</label>
									<input type="text" class="form-control" name="powered_by" id="powered_by" placeholder="{{ trans("messages.powered-by") }}" value="{{ old('powered_by' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_powered_by)) ? $settingsInfo->v_powered_by : ''  ) ) ) }}">
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group">
									<label class="control-label" for="powered_by_link">{{ trans("messages.powered-by-link") }}:</label>
									<input type="text" class="form-control" name="powered_by_link" id="powered_by_link" placeholder="{{ trans("messages.powered-by-link") }}" value="{{ old('powered_by_link' , ( (isset($settingsInfo) && (!empty($settingsInfo->v_powered_by_link)) ? $settingsInfo->v_powered_by_link : ''  ) ) ) }}">
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label for="dealing" class="control-label">{{ trans("messages.contact-settings") }}:</label>
									<div class="custom-control custom-switch custom-switch-enable-disable">
										<input type="checkbox" class="custom-control-input" id="contact_settings_tab" name="contact_settings_tab" value="1" <?php echo ( ( ( isset($settingsInfo) ) && ( strlen($settingsInfo->t_contact_settings_tab) > 0 ) && ( $settingsInfo->t_contact_settings_tab == 0 ) ) ? "" :  'checked' )?>>
										<label class="custom-control-label" for="contact_settings_tab"><span></span></label>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">	
									<label for="dealing" class="control-label">{{ trans("messages.social-links") }}:</label>
									<div class="custom-control custom-switch custom-switch-enable-disable">
										<input type="checkbox" class="custom-control-input" id="social_links_tab" name="social_links_tab" value="1" <?php echo ( ( ( isset($settingsInfo) ) && ( strlen($settingsInfo->t_social_links_tab) > 0 ) && ( $settingsInfo->t_social_links_tab == 0 ) ) ? "" :  'checked' )?>>
										<label class="custom-control-label status-product" for="social_links_tab"><span></span></label>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label for="dealing" class="control-label">{{ trans("messages.smtp-connection") }}:</label>
									<div class="custom-control custom-switch custom-switch-enable-disable">
										<input type="checkbox" class="custom-control-input" id="smtp_connection_tab" name="smtp_connection_tab" value="1" <?php echo ( ( ( isset($settingsInfo) ) && ( strlen($settingsInfo->t_smtp_connection_tab) > 0 ) && ( $settingsInfo->t_smtp_connection_tab == 0 ) ) ? "" :  'checked' )?>>
										<label class="custom-control-label status-product" for="smtp_connection_tab"><span></span></label>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">	
									<label for="dealing" class="control-label">{{ trans("messages.site-info") }}:</label>
									<div class="custom-control custom-switch custom-switch-enable-disable">
										<input type="checkbox" class="custom-control-input" id="site_info_tab" name="site_info_tab" value="1" <?php echo ( ( ( isset($settingsInfo) ) && ( strlen($settingsInfo->t_site_info_tab) > 0 ) && ( $settingsInfo->t_site_info_tab == 0 ) ) ? "" :  'checked' ) ?>>
										<label class="custom-control-label status-product" for="site_info_tab"><span></span></label>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label for="dealing" class="control-label">{{ trans("messages.logo-settings") }}:</label>
									<div class="custom-control custom-switch custom-switch-enable-disable">
										<input type="checkbox" class="custom-control-input" id="logo_settings_tab" name="logo_settings_tab" value="1" <?php echo ( ( ( isset($settingsInfo) ) && ( strlen($settingsInfo->t_logo_settings_tab) > 0 ) && ( $settingsInfo->t_logo_settings_tab == 0 ) ) ? "" :  'checked' )?>>
										<label class="custom-control-label status-product" for="logo_settings_tab"><span></span></label>
									</div>
								</div>
							</div>
							<div class="col-lg-4">
								<div class="form-group">
									<label for="dealing" class="control-label">{{ trans("messages.send-email") }}:</label>
									<div class="custom-control custom-switch custom-switch-enable-disable">
										<input type="checkbox" class="custom-control-input" id="send_email" name="send_email" value="1" <?php  echo ( ( ( isset($settingsInfo) ) && ( strlen($settingsInfo->t_send_email) > 0 ) && ( $settingsInfo->t_send_email == 0 ) ) ? "" :  'checked' )  ?>>
										<label class="custom-control-label status-product" for="send_email"><span></span></label>
									</div>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			<?php }?>
		<input type="hidden" name="remove_webiste_logo" id="remove_webiste_logo" value="no">
		<input type="hidden" name="remove_footer_logo" id="remove_footer_logo" value="no">
		<input type="hidden" name="remove_webiste_fav_icon" id="remove_webiste_fav_icon" value="no">
		<input type="hidden" name="remove_website_og_icon" id="remove_website_og_icon" value="no">
		<div class="text-center button-sticky-submit py-3 sticky-div bg-white border-top w-100">
			<button type="submit" class="btn bg-theme text-white btn-wide">{{ trans("messages.submit") }}</button>
		</div>
		{!! Form::close() !!}
	</div>
</div>
<script src="{{ asset ('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
    CKEDITOR.replace('time_off_policy');
    CKEDITOR.replace('leave_policy');
    //CKEDITOR.replace('address');
    
   
    
</script>
<script>
    $("#add-settings-form").validate({
        errorClass: "invalid-input",
        ignore: [],
        debug: false,
        rules: {
        	/* site_description: { noSpace : true},
            address: { noSpace : true},
            about_short_description : { noSpace : true},
            primary_mobile_no : { noSpace : true},
            secondary_mobile_no : { noSpace : true },
            other_mobile_no : { noSpace : true },
            whatsapp_no : { noSpace : true },
            email : { noSpace : true ,email_regex:true},
            working_hours : { noSpace : true },
            working_days : { noSpace : true },
            google_map : { noSpace : true },
            short_address : { noSpace : true },
           	facebook : { noSpace : true},
            instagram : { noSpace : true },
            youtube : { noSpace : true },
            linkedin : { noSpace : true  },
            twitter : { noSpace : true }, */
            site_title : { noSpace : true },
            site_keywords : { noSpace : true },
           	meta_author : { noSpace : true },
            site_name : { noSpace : true },
            powered_by : { noSpace : true },
            powered_by_link : { noSpace : true },
            default_cc_mail : { noSpace : true, email_regex:true},
            contact_receive_mail : { noSpace : true , email_regex:true},
            send_email_protocol : { noSpace : true },
            send_email_host : { noSpace : true },
            send_email_port : { noSpace : true },
            send_email_user : { noSpace : true , email_regex:true},
            send_email_password : { noSpace : true },
           
        },
        messages: {
        	/* about_short_description: {required: "{{ trans('messages.no-space-form-validation') }}"},
            site_description: {required: "{{ trans('messages.no-space-form-validation') }}"},
            address: { required: "{{ trans('messages.no-space-form-validation') }}"},
            primary_mobile_no:{ required: "{{ trans('messages.no-space-form-validation') }}"},
            secondary_mobile_no : { noSpace :"{{ trans('messages.no-space-form-validation') }}" },
        	other_mobile_no : { noSpace :"{{ trans('messages.no-space-form-validation') }}"},
        	whatsapp_no : { noSpace :"{{ trans('messages.no-space-form-validation') }}" },
        	email : { noSpace :"{{ trans('messages.no-space-form-validation') }}"  },
        	working_hours : { noSpace :"{{ trans('messages.no-space-form-validation') }}"  },
        	working_days : { noSpace :"{{ trans('messages.no-space-form-validation') }}"  },
        	google_map : { noSpace :"{{ trans('messages.no-space-form-validation') }}"  },
        	short_address : { noSpace :"{{ trans('messages.no-space-form-validation') }}"  },
        	address : { noSpace :"{{ trans('messages.no-space-form-validation') }}"  },
        	facebook : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	instagram : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	youtube : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	linkedin : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	twitter : { noSpace : "{{ trans('messages.no-space-form-validation') }}" }, */
        	site_title : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	site_keywords : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	meta_author : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	site_name : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	powered_by : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	powered_by_link : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	default_cc_mail : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	contact_receive_mail : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	send_email_protocol : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	send_email_host : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	send_email_port : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	send_email_user : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },
        	send_email_password : { noSpace : "{{ trans('messages.no-space-form-validation') }}" },

        	
        },
        submitHandler: function(form) {
        	var confirm_box = "";
            var confirm_box_msg = "";
            <?php if( isset($settingsInfo) && ( $settingsInfo->i_id > 0 ) ) { ?>
					confirm_box = "{{ trans('messages.update-settings') }}";
					confirm_box_msg = "{{ trans ( 'messages.confirm-update-settings-msg') }}";
			<?php } else {?>
					confirm_box = "{{ trans('messages.add-settings') }}";
					confirm_box_msg = "{{ trans ( 'messages.confirm-add-settings-msg') }}";
			<?php } ?>
			
			alertify.confirm(confirm_box,confirm_box_msg,function() {
        		showLoader()
            	form.submit();
			},function() {});
        }
    });

    function removeLogo(thisitem){
		$(thisitem).parents('.website-logo-div').find('.custom-file-label').html('');
		$(thisitem).parents('.website-logo-div').find('.logo_image-preview-div').hide();
		$(thisitem).hide();
	    $("[name='remove_webiste_logo']").val('yes');
	}

	function removeFooterLogo(thisitem){
		$(thisitem).parents('.website-footer-logo-div').find('.custom-file-label').html('');
		$(thisitem).parents('.website-footer-logo-div').find('.footer_logo_image-preview-div').hide();
		$(thisitem).hide();
	    $("[name='remove_footer_logo']").val('yes');
	}
	
	function removeFavIcon(thisitem){
		$(thisitem).parents('.website-fav-icon-div').find('.custom-file-label').html('');
		$(thisitem).parents('.website-fav-icon-div').find('.fav_icon_image-preview-div').hide();
		$(thisitem).hide();
		$("[name='remove_webiste_fav_icon']").val('yes');
	}

	function removeOgIcon(thisitem){
		$(thisitem).parents('.website-og-icon-div').find('.custom-file-label').html('');
		$(thisitem).parents('.website-og-icon-div').find('.og_icon_image-preview-div').hide();
		$(thisitem).hide();
		$("[name='remove_website_og_icon']").val('yes');
	}
</script>

@endsection