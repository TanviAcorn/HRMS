<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Models\SettingsModel;
use App\Helpers\Twt\Wild_tiger;
class SettingsController extends MasterController {
	public function __construct(){
		parent:: __construct();
		$this->tableName = config("constants.SETTING_TABLE");
		$this->moduleName = trans('messages.settings');
		$this->folderName = config('constants.ADMIN_FOLDER'). 'settings/' ;
		$this->crudModel =  new SettingsModel();
		$this->settingsFolder = 'settings/';
		$this->redirectUrl = config('constants.SETTING_URL');
	
	}
	public function index(){
		
		if( session()->get('role') != config('constants.ROLE_ADMIN') ){
			return redirect(config('constants.DASHBORD_MASTER_URL'));
		}
		
		$data = $whereData = [];
		$data ['pageTitle'] = trans('messages.settings');
		$whereData['singleRecord'] = true;
		$settingsInfo = $this->crudModel->getRecordDetails($whereData);
		$data['settingsInfo'] = $settingsInfo ;
		return view($this->folderName . 'settings')->with($data);
	}
	
	public function add(Request $request){
		$successMessage =  trans('messages.success-create',['module'=> $this->moduleName]);
		$errorMessages = trans('messages.error-create',['module'=> $this->moduleName]);
		$result = false;
		
		$removeWebisteLogo = (!empty($request->input('remove_webiste_logo')) ? $request->input('remove_webiste_logo') : null);
		$removeFooterLogo = (!empty($request->input('remove_footer_logo')) ? $request->input('remove_footer_logo') : null);
		$removeWebisteFavIcon = (!empty($request->input('remove_webiste_fav_icon')) ? $request->input('remove_webiste_fav_icon') : null);
		$removeWebisteOgIcon = (!empty($request->input('remove_website_og_icon')) ? $request->input('remove_website_og_icon') : null);
		
		$recordData  = [];
		
		/* $recordData['v_primary_mobile_no'] = (!empty($request->input('primary_mobile_no')) ? ($request->input('primary_mobile_no')) : null);
		$recordData['v_secondary_mobile_no'] = (!empty($request->input('secondary_mobile_no')) ? ($request->input('secondary_mobile_no')) : null);
		$recordData['v_other_mobile_no'] = (!empty($request->input('other_mobile_no')) ? ($request->input('other_mobile_no')) : null);
		$recordData['v_whatsapp_number'] = (!empty($request->input('whatsapp_no')) ? $request->input('whatsapp_no') : null);
		$recordData['e_whatsapp_position'] = (!empty($request->input('whatsapp_no_position')) ? ($request->input('whatsapp_no_position')) : '');
		$recordData['v_email'] = (!empty($request->input('email')) ? ($request->input('email')): null);
		$recordData['v_working_hours'] = (!empty($request->input('working_hours')) ? $request->input('working_hours') : null);
		$recordData['v_working_days'] = (!empty($request->input('working_days')) ? $request->input('working_days') : null);
		
		$recordData['v_google_map'] = (!empty($request->input('google_map')) ? htmlentities($request->input('google_map')) : null);
		$recordData['v_short_address'] = (!empty($request->input('short_address')) ? $request->input('short_address') : null);
		$recordData['v_address'] = (!empty($request->input('address')) ? htmlentities($request->input('address')) : null);
		
		$recordData['v_facebook_link'] = (!empty($request->input('facebook')) ? ($request->input('facebook')) : null);
		$recordData['v_instagram_link'] = (!empty($request->input('instagram')) ? ($request->input('instagram')) : null);
		$recordData['v_youtube_link'] = (!empty($request->input('youtube')) ? ($request->input('youtube')) : null);
		$recordData['v_linkedin_link'] = (!empty($request->input('linkedin')) ? ($request->input('linkedin')) : null);
		$recordData['v_twitter_link'] = (!empty($request->input('twitter')) ? ($request->input('twitter')) : null); */
		
		
		$recordData['v_default_cc_mail'] = (!empty($request->input('default_cc_mail')) ? ($request->input('default_cc_mail')) : null);
		$recordData['v_contact_receive_mail'] = (!empty($request->input('contact_receive_mail')) ? ($request->input('contact_receive_mail')) : null);
		$recordData['v_send_email_protocol'] = (!empty($request->input('send_email_protocol')) ? ($request->input('send_email_protocol')) : null);
		$recordData['v_send_email_host'] = (!empty($request->input('send_email_host')) ? ($request->input('send_email_host')) : null);
		$recordData['i_send_email_port'] = (!empty($request->input('send_email_port')) ? ($request->input('send_email_port')) : null);
		$recordData['v_send_email_user'] = (!empty($request->input('send_email_user')) ? ($request->input('send_email_user')) : null);
		$recordData['v_send_email_password'] = (!empty($request->input('send_email_password')) ? ($request->input('send_email_password')) : null);
		
		$recordData['v_site_title'] = (!empty($request->input('site_title')) ? $request->input('site_title') : null);
		$recordData['v_site_keywords'] = (!empty($request->input('site_keywords')) ? $request->input('site_keywords') : null);
		$recordData['v_about_short_description'] = (!empty($request->input('time_off_policy')) ? htmlentities($request->input('time_off_policy')) : null);
		$recordData['v_site_description'] = (!empty($request->input('site_description')) ? $request->input('site_description') : null);
		
		
		$recordData['v_time_off_policy'] = (!empty($request->input('time_off_policy')) ? htmlentities($request->input('time_off_policy')) : null);
		$recordData['v_leave_policy'] = (!empty($request->input('leave_policy')) ? htmlentities($request->input('leave_policy')) : null);
		
		
		if( config('constants.SHOW_DEVELOPER_SETTINGS') == 1 ) {
			$recordData['d_version'] = (!empty($request->input('version')) ? ($request->input('version')) : null);
			$recordData['v_site_name'] = (!empty($request->input('site_name')) ? ($request->input('site_name')) : null);
			$recordData['v_meta_author'] = (!empty($request->input('meta_author')) ? ($request->input('meta_author')) : null);
			$recordData['v_powered_by'] = (!empty($request->input('powered_by')) ? ($request->input('powered_by')) : null);
			$recordData['v_powered_by_link'] = (!empty($request->input('powered_by_link')) ? ($request->input('powered_by_link')) : null);
			$recordData['t_contact_settings_tab'] = (!empty($request->input('contact_settings_tab')) ? ($request->input('contact_settings_tab')) : '');
			$recordData['t_social_links_tab'] = (!empty($request->input('social_links_tab')) ? ($request->input('social_links_tab')) : '');
			$recordData['t_smtp_connection_tab'] = (!empty($request->input('smtp_connection_tab')) ? ($request->input('smtp_connection_tab')) : '');
			$recordData['t_site_info_tab'] = (!empty($request->input('site_info_tab')) ? ($request->input('site_info_tab')) : '');
			$recordData['t_logo_settings_tab'] = (!empty($request->input('logo_settings_tab')) ? ($request->input('logo_settings_tab')) : '');
			$recordData['t_send_email'] = (!empty($request->input('send_email')) ? ($request->input('send_email')) : '');
		}
		
		$whereData = [] ;
		$whereData['singleRecord'] = true;
		$settingsInfo = $this->crudModel->getRecordDetails($whereData);
		
		$uploadLogoImage = null;
		if (!empty($request->file('logo_image'))){
			$fileUpload = $this->uploadFile($request, 'logo_image' ,$this->settingsFolder);
			if (isset($fileUpload['status']) && ($fileUpload['status'] != false)){
				$uploadLogoImage = $fileUpload['filePath'];
			}
		}
		
		$uploadFooterLogoImage = null;
		if (!empty($request->file('footer_logo_image'))){
			$fileUpload = $this->uploadFile($request, 'footer_logo_image',$this->settingsFolder);
			if (isset($fileUpload['status']) && ($fileUpload['status'] != false)){
				$uploadFooterLogoImage = $fileUpload['filePath'];
			}
		}
		
		$uploadFavIconImage = null;
		if (!empty($request->file('fav_icon_image'))){
			$fileUpload = $this->uploadFile($request, 'fav_icon_image' ,$this->settingsFolder);
			if (isset($fileUpload['status']) && ($fileUpload['status'] != false)){
				$uploadFavIconImage = $fileUpload['filePath'];
			}
		}
		
		$uploadOgIconImage = null;
		if (!empty($request->file('og_icon_image'))){
			$fileUpload = $this->uploadFile($request, 'og_icon_image' ,$this->settingsFolder);
			if (isset($fileUpload['status']) && ($fileUpload['status'] != false)){
				$uploadOgIconImage = $fileUpload['filePath'];
			}
		}
		
		$websiteLogo = (!empty($settingsInfo->v_website_logo) ? $settingsInfo->v_website_logo :null);
		$websiteFooterLogo = (!empty($settingsInfo->v_website_footer_logo) ? $settingsInfo->v_website_footer_logo :null);
		$websiteFavIcon = (!empty($settingsInfo->v_website_fav_icon) ? $settingsInfo->v_website_fav_icon :null);
		$websiteOgIcon = (!empty($settingsInfo->v_website_og_icon) ? $settingsInfo->v_website_og_icon :null);
		
		$recordData['v_website_logo'] =  ( (!empty($uploadLogoImage) ? $uploadLogoImage : ( (  (!empty($removeWebisteLogo)) && ( $removeWebisteLogo == 'yes' )  ) ?  null : $websiteLogo ) ) ) ;
		$recordData['v_website_footer_logo'] =  ( (!empty($uploadFooterLogoImage) ? $uploadFooterLogoImage : ( (  (!empty($removeFooterLogo)) && ( $removeFooterLogo == 'yes' )  ) ?  null : $websiteFooterLogo ) ) ) ;
		$recordData['v_website_fav_icon'] =  ( (!empty($uploadFavIconImage) ? $uploadFavIconImage : ( (  (!empty($removeWebisteFavIcon)) && ( $removeWebisteFavIcon == 'yes' )  ) ?  null : $websiteFavIcon ) ) ) ;
		$recordData['v_website_og_icon'] =  ( (!empty($uploadOgIconImage) ? $uploadOgIconImage : ( (  (!empty($removeWebisteOgIcon)) && ( $removeWebisteOgIcon == 'yes' )  ) ?  null : $websiteOgIcon) ) ) ;
			
		
		if(!empty($settingsInfo)){
			$successMessage =  trans('messages.success-update',['module'=> $this->moduleName]);
			$errorMessages = trans('messages.error-update',['module'=> $this->moduleName]);
			
			$result = $this->crudModel->updateTableData($this->tableName , $recordData , [ 'i_id'=>$settingsInfo->i_id ]);
		} else {
			$insertRecord = $this->crudModel->insertTableData($this->tableName , $recordData );
				
			if($insertRecord > 0){
				$result = true;
			}
		}
		
		
		if( $result != false ){
			Wild_tiger::setFlashMessage ( 'success', $successMessage  );
			return redirect($this->redirectUrl);
		}
		Wild_tiger::setFlashMessage ( 'danger', $errorMessage  );
		return redirect()->back()->withErrors ( $validator )->withInput ();
	}
}
