<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Login;
use App\Helpers\Twt\Wild_tiger;
use App\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use App\Rules\CheckLastPassword;
use Illuminate\Support\Facades\Log;
class LoginController extends Controller
{
	public $loginCookieName;
	public $loginPage;
	public function __construct(){
		$this->dbObject = new BaseModel();
		$this->loginCookieName = config('constants.LOGIN_COOKIE_NAME');
		$this->loginPage = config('constants.LOGIN_URL');
		
	} 
	
    //
	public function  showLoginForm(Request $request){
		if( ( Session::has('isLoggedIn') ) && ( Session::get('isLoggedIn') != false ) ){
			if( Session::get('site_title') == config('constants.SITE_TITLE')  ){
				return redirect()->route(config('constants.SUCCESS_REDIRECT_MODULE'));
			}
		}
		
		if( (Cookie::has($this->loginCookieName.'_process_email')) &&  (Cookie::has( $this->loginCookieName. '_process_password')) ){
			$email = Cookie::get($this->loginCookieName.'_process_email');
			
			//$password = substr_replace($password, '', 1, 4);
			$checkLogin =  $this->dbObject->getSingleRecordById(  config('constants.LOGIN_MASTER_TABLE') . ' as lm' , 
				[ 'lm.i_id' , 'lm.v_name' , 'lm.v_email' , 'lm.t_is_active' , 'lm.v_role' , 'lm.v_email' , 'lm.v_password' ] ,
				[ 'lm.v_email' => $email , 'lm.t_is_deleted != ' => 1  ]  );
				
			if( empty($checkLogin)){
				
				Cookie::queue(Cookie::forget($this->loginCookieName.'_process_email'));
				Cookie::queue(Cookie::forget($this->loginCookieName.'_process_password'));
				Wild_tiger::setFlashMessage('danger', trans('messages.invalid-login') );
				return Redirect::back();
			}
			
			if( $checkLogin->t_is_active == config('constants.INACTIVE_STATUS') ){
				Wild_tiger::setFlashMessage('danger', trans('messages.error-inactive-account-login') );
				return redirect($this->loginPage);
			}
			
			$password = Cookie::get($this->loginCookieName.'_process_password');
			$password =  (!empty($password) ?  trim(Wild_tiger::decode($password)) : "" ) ;
			
			if ($password ==  $checkLogin->v_password ) {
			
				$loginHistoryId = [];
				$loginHistoryId['i_login_id'] = $checkLogin->i_id;
				$loginHistoryId['i_session_id'] = session()->get('_token');
					
				$insertLogin = $this->dbObject->insertTableData( config('constants.LOGIN_HISTORY_TABLE') , $loginHistoryId);
					
				Session::put('user_id', $checkLogin->i_id);
				Session::put('name', $checkLogin->v_name);
				Session::put('role', $checkLogin->v_role);
				Session::put('email', $checkLogin->v_email);
				Session::put('user_type', $checkLogin->v_user_type);
				Session::put('isLoggedIn', true);
				Session::put('site_title', config('constants.SITE_TITLE') );
			
				$trapPassword = $checkLogin->v_password;
				
				Cookie::queue(Cookie::make($this->loginCookieName.'_process_email', $checkLogin->v_email, 360));
				Cookie::queue(Cookie::make($this->loginCookieName.'_process_password', Wild_tiger::encode($trapPassword), 360));
					
				$redirectPath = (!empty(Session::get('url.intended')) ? Session::get('url.intended') : config('constants.SUCCESS_REDIRECT_MODULE') );
					
				return redirect($redirectPath);
			}
			
			
		}
		$data['pageTitle'] = trans('messages.login');
		return view('admin/login' , $data);
	}
	
	
	public function checkLogin(Request $request){
		$validator = Validator::make($request->all(), [
				'login_email' => 'required',
				'login_password' => 'required',
		],[
				'login_email.required' => __('messages.email-required') ,
				'login_password.required' =>__('messages.password-required'),
		]
		);
		 
		if ($validator->fails()) {
			return redirect('login')
			->withErrors($validator)
			->withInput();
		}
		
		$email = $request->input('login_email');
		$password = $request->input('login_password');
		 
		$checkLogin =  $this->dbObject->getSingleRecordWithJoinById(  config('constants.LOGIN_MASTER_TABLE') . ' as lm' , 
				[ 'lm.i_id' , 'em.i_id as employee_id' ,   'lm.v_name' , 'lm.v_email' , 'lm.v_role' , 'lm.v_email' , 'lm.v_password'   , 'lm.t_is_active' ] ,
				[
					[ 
						'tableName' => config('constants.EMPLOYEE_MASTER_TABLE') . ' as em',
						'condition' => 'lm.i_id = em.i_login_id',
						'type' => 'left'
					]  		
						
				],
				
				[ 'lm.v_email' => $email , 'lm.t_is_deleted != ' => 1  ]  );
		 
		if( empty($checkLogin)){
			Wild_tiger::setFlashMessage('danger', trans('messages.invalid-login') );
			return Redirect::back();
		}
		
		if( $checkLogin->t_is_active == config('constants.INACTIVE_STATUS') ){
			Wild_tiger::setFlashMessage('danger', trans('messages.error-inactive-account-login') );
			return redirect($this->loginPage);
		}
		
		
		if (password_verify($password, $checkLogin->v_password)) {
			
			$sendOtp = false;
			if( ( config('constants.SEND_LOGIN_OTP') == 1 ) && ( $checkLogin->v_role == config('constants.ROLE_ADMIN') ) ){
				$sendOtp = true;
			}
			if( in_array( $checkLogin->v_email , config('constants.ALLOWED_LOGIN_WITHOUT_OTP_EMAIL') ) ){
				$sendOtp = false;
			}
			
			//Log::info(print_r($checkLogin,true));
			//Log::info( print_r( config('constants.ALLOWED_LOGIN_WITHOUT_OTP_EMAIL') ,true ) );
			//Log::info('sendOtp = ' . $sendOtp );
			
			if( $sendOtp != false ) {
				$otp = generateOTP();
				
				if( config('constants.SEND_STATIC_OTP') == 1  ){
					$otp = config('constants.STATIC_OTP');
				}
				
				$otpData = [];
				$otpData['i_login_id'] = (!empty($checkLogin->i_id) ? $checkLogin->i_id :'');
				$otpData['d_verify_otp'] = $otp;
				$otpData['d_expired'] = strtotime('+' . config('constants.LOGIN_OTP_EXPIRE_TIME') . ' minutes');
				
				$this->dbObject->insertTableData( config('constants.LOGIN_VERIFY_OTP_TABLE') , $otpData);
				
				$mailData = [];
				$mailData['mailData'] = config('constants.SITE_TITLE');
				$mailData['otpInfo'] = $otp;
				$mailData['userName'] = $checkLogin->v_name;
				$mailData['sendCommonFooter'] = false;
				
				$otpInfo = [];
				$otpInfo['otpInfo'] = $otp;
				$otpInfo['sendCommonFooter'] = false;
				
				$mailHtml = view('login-send-otp-template', $otpInfo)->render();
				
				$config = [];
				$config ['to'] = $checkLogin->v_email;
				$config ['viewName'] = 'login-send-otp-template';
				$config ['mailData'] = $mailData;
				$config ['subject'] = trans('messages.login-otp-for') . ' ' .config('constants.SITE_TITLE');
				$config ['message'] = trans('messages.dear'). ' ' . $checkLogin->v_name.trans('messages.your-login-otp-is') . ' ' . $otp . ' ' . trans('messages.for-logging-in') . ' ' . config('constants.SITE_TITLE').".";
				
				$sendMail = [];
				
				try{
					$sendMail = sendMailSMTP($config);
				}catch(Exception $e){
					Log::error($e->getMessage());
				}
					
				if(  isset($sendMail['status']) && ( $sendMail['status'] != false ) ) {
					Session::put('verifyOtp', true);
					return redirect('login/verifyOtp/'.Wild_tiger::encode($checkLogin->i_id));
				} else {
					Wild_tiger::setFlashMessage('danger', trans('messages.error-send-otp-mail') );
					return Redirect::back();
				}
			} else {
				if( !in_array( $checkLogin->v_email ,  config('constants.ALLOWED_LOGIN_WITHOUT_OTP_EMAIL')  ) ){
					$loginHistoryId = [];
					$loginHistoryId['i_login_id'] = $checkLogin->i_id;
					$loginHistoryId['i_session_id'] = session()->get('_token');
					$loginHistoryId['dt_login_time'] = date('Y-m-d H:i:s');
					$insertLogin = $this->dbObject->insertTableData( config('constants.LOGIN_HISTORY_TABLE') , $loginHistoryId);
				}
					
				
					
				Session::put('user_id', $checkLogin->i_id);
				Session::put('user_employee_id', $checkLogin->employee_id);
				Session::put('name', $checkLogin->v_name);
				Session::put('role', $checkLogin->v_role);
				Session::put('email', $checkLogin->v_email);
				Session::put('isLoggedIn', true);
				Session::put('site_title', config('constants.SITE_TITLE') );
				
				//$trapPassword =  substr_replace( $checkLogin->v_password, str_random(4), 1, 0 );  ;
				$trapPassword = $checkLogin->v_password;
					
				if((!empty($request->input("conditions"))) && ($request->input("conditions") == "yes") ) {
					Cookie::queue(Cookie::make($this->loginCookieName.'_process_email', $checkLogin->v_email, 360));
					Cookie::queue(Cookie::make($this->loginCookieName.'_process_password', Wild_tiger::encode($trapPassword), 360));
				}
					
				$redirectPath = (!empty(Session::get('url.intended')) ? Session::get('url.intended') : config('constants.SUCCESS_REDIRECT_MODULE') );
					
				return redirect($redirectPath);
			}
			
			
		}
		Wild_tiger::setFlashMessage('danger', trans('messages.invalid-login') );
		return redirect('login')
			->withErrors($validator)
			->withInput();
		 
	}
	public function forgotpassword(){
		$data['pageTitle'] = trans('messages.forgot-password');
		return view( 'admin/forgot-password')->with($data);
	}
	
	public function sendForgotPasswordMail(Request $request){
	
		$validator = Validator::make($request->all(), [
				'login_email' => 'required',
		],[
				'login_email.required' => __('messages.required-login-email') ,
		]
		);
	
		if ($validator->fails()) {
			return redirect()->back()->withErrors ( $validator )->withInput ();
		}
	
		$email = $request->input('login_email');
	
		$checkLoginWhere = [];
		$checkLoginWhere['v_email'] = $email;
		$checkLoginWhere['t_is_deleted'] = 0;
		$checkLogin = Login::where($checkLoginWhere)->first();
	
		if( empty($checkLogin)){
			Wild_tiger::setFlashMessage('danger', trans('messages.email-not-register') );
			return Redirect::back();
		}
	
		if(isset($checkLogin->t_is_active) && ($checkLogin->t_is_active == config('constants.INACTIVE_STATUS') ) ){
			Wild_tiger::setFlashMessage('danger', trans('messages.error-inactive-account-login') );
			return Redirect::back();
		}
	
		$config = [];
			
		$subject = trans('messages.password-recovery-mail-subject' , [ 'module' => config('constants.SITE_TITLE') ] )  ;
	
	
		$config['to'] = $checkLogin->v_email;
		$config['subject'] = $subject;
	
		$encodedData = [];
		$encodedData['user_id'] = $checkLogin->i_id;
		$encodedData['user_email'] = $checkLogin->v_email;
		$encodedData['time'] = strtotime("+".config ( 'constants.FORGET_PASSWORD_CHECK_TIME')." minutes");
	
		$mailData['name'] = $checkLogin->v_name;
		$mailData['link'] = config('constants.LOGIN_URL') . '/newPassword/' . Wild_tiger::encode(json_encode($encodedData));
		
		$config['viewName'] = config('constants.ADMIN_FOLDER'). 'mailtemplate/forgot-password-mail';
		$config['mailData'] = $mailData;
		
		// send mail
		$sendMail = sendMailSMTP($config);
		
		if( (!empty($sendMail)) && ( $sendMail['status'] != false ) ){
			Session::flash('send_mail', true);
			Wild_tiger::setFlashMessage('success', trans ( 'messages.success-forgot-password-mail', ['module' => $checkLogin->v_email ] ) );
			return Redirect::back();
		}
	
		Session::flash('send_mail', false );
		Wild_tiger::setFlashMessage('danger', trans('messages.error-forgot-password-mail') );
		return Redirect::back();
	
	
	
	}
	
	public function newPassword($encodeEmail){
	
		if(!empty($encodeEmail)){
				
			$decodeEmail = trim(Wild_tiger::decode($encodeEmail));
				
			$decodeData = (!empty($decodeEmail) ? json_decode($decodeEmail,true) : [] );
				
			if(!empty($decodeData)){
	
				if($decodeData['time'] < strtotime('now') ){
					Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-link-expired' ) );
					return redirect (  config('constants.FORGOT_PASSWORD_URL')  );
				}
	
				$decodeEmail = $decodeData['user_email'];
	
				$checkLogin = Login::where( [ 'v_email' => $decodeEmail , 't_is_deleted' => 0 ]  )->first();
	
				if(!empty($checkLogin) && (!empty($decodeEmail))){
					$data['userId'] = $checkLogin->i_id;
					$data['pageTitle'] = trans('messages.reset-password');
					return view(config('constants.ADMIN_FOLDER') . 'reset-password', $data);
				} else {
					Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-email-register' ) );
					return redirect (config('constants.FORGOT_PASSWORD_URL') );
				}
	
	
	
			}
			Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-invalid-link' ) );
			return redirect ( config('constants.FORGOT_PASSWORD_URL') );
				
		}
	
		Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.system-error' ) );
		return redirect::back();
	}
	
	public function updatePassword(Request $request ){
	
		$validator = Validator::make($request->all(), [
				'new_password' => [ 'required' ,  new CheckLastPassword($request) ] ,
				'confirm_password' => 'required|same:new_password',
		],[
				'new_password.required' => __('messages.required-new-password') ,
				'confirm_password.required' => __('messages.required-confirm-password') ,
				'confirm_password.same' => __('messages.confirm-password-not-match') ,
		]
		);
	
		if ($validator->fails()) {
			return redirect::back()
			->withErrors($validator)
			->withInput();
		}
	
		$requestUserId =  (!empty($request->input('user_id')) ? (int)Wild_tiger::decode($request->input('user_id')) : '' );
	
		$newPassword  = $request->input('new_password');
		$confirmPassword  = $request->input('confirm_password');
		
		if( $requestUserId > 0 ){
				
			if(  $newPassword ==  $confirmPassword ){
	
				$masterUserData = Login::find ( $requestUserId );
	
				$masterUserData->v_password = password_hash($newPassword, PASSWORD_DEFAULT);;
					
				$updateUser = $masterUserData->save ();
	
				if ($updateUser != false) {
					Wild_tiger::setFlashMessage ( 'success', trans ( 'messages.success-update' , [ 'module' => trans('messages.password') ] ) );
					return redirect ( $this->loginPage  );
				}
	
				Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-update' , [ 'module' => trans('messages.password') ] ) );
				return redirect ( $this->loginPage );
			}
				
			Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.confirm-password-not-match' ) );
			return redirect ( $this->loginPage);
				
		}
	
		Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.system-error' ) );
		return redirect::back();
	}
	
	public function verifyOtp($id)
	{
		if (session()->has('verifyOtp') && session()->get('verifyOtp') != false && config('constants.SEND_LOGIN_OTP') == 1){
			$data = [];
			$data['pageTitle'] = trans('messages.verify-otp');
			$recordId = (int)Wild_tiger::decode($id);
			if($recordId > 0){
				$data['userId'] = $recordId;
				return view(config('constants.ADMIN_FOLDER') . 'verify-otp' , $data);
			}
		}
		
		return redirect(config('constants.SUCCESS_REDIRECT_MODULE'));
	}
	
	public function checkotp(Request $request)
	{
		$errorFound = true;
		if(!empty($request->post())){
			$userId = (!empty($request->user_id) ? (int)Wild_tiger::decode($request->user_id) : 0  );
			if(!empty($userId)){
				$errorFound = false;
				$formValidation = $where = [];
				$formValidation['login_otp'] = ['required'];
					
				$checkValidation = Validator::make($request->all(),$formValidation,
						[
								'login_otp.required' => __('messages.required-otp') ,
						]
				);
				if ($checkValidation->fails ()) {
					return redirect()->back()->withErrors($checkValidation)->withInput();
				}
				
				$loginOtp = (!empty($request->login_otp) ? trim($request->login_otp) : "");
				
				$where['i_login_id'] = $userId;
				$where['t_is_deleted != '] = 1;
				$where['order_by'] = ['i_id' => 'desc'];
				
				$checkOtpInfo =  $this->dbObject->getSingleRecordById(  config('constants.LOGIN_VERIFY_OTP_TABLE') ,[ 'i_id' , 'i_login_id' , 'd_verify_otp', 'd_expired'] ,$where);
				
				if(!empty($checkOtpInfo)){
					if (!empty($checkOtpInfo->d_expired) && $checkOtpInfo->d_expired > strtotime('now')){
						if((!empty($checkOtpInfo->d_verify_otp)) && ($checkOtpInfo->d_verify_otp == $loginOtp)){
							$checkLogin =  $this->dbObject->getSingleRecordWithJoinById( config('constants.LOGIN_MASTER_TABLE') . ' as lm' ,
									[ 'lm.i_id' , 'em.i_id as employee_id' ,   'lm.v_name' , 'lm.v_email' , 'lm.v_role' , 'lm.v_email' , 'lm.v_password'   , 'lm.t_is_active' ] ,
									[
											[
													'tableName' => config('constants.EMPLOYEE_MASTER_TABLE') . ' as em',
													'condition' => 'lm.i_id = em.i_login_id',
													'type' => 'left'
											]
						
									], ['lm.i_id' => $userId, 'lm.t_is_deleted' => 0 ] );
						
							Session::put('user_id', $checkLogin->i_id);
							Session::put('user_employee_id', $checkLogin->employee_id);
							Session::put('name', $checkLogin->v_name);
							Session::put('role', $checkLogin->v_role);
							Session::put('email', $checkLogin->v_email);
							Session::put('isLoggedIn', true);
							Session::put('site_title', config('constants.SITE_TITLE') );
						
							$loginHistoryId = [];
							$loginHistoryId['i_login_id'] = $checkLogin->i_id;
							$loginHistoryId['i_session_id'] = session()->get('_token');
							$loginHistoryId['dt_login_time'] = date('Y-m-d H:i:s');
								
							$insertLogin = $this->dbObject->insertTableData( config('constants.LOGIN_HISTORY_TABLE') , $loginHistoryId);
						
							$trapPassword = $checkLogin->v_password;
						
							//Cookie::queue(Cookie::make($this->loginCookieName.'_process_email', $checkLogin->v_email, 360));
							//Cookie::queue(Cookie::make($this->loginCookieName.'_process_password', Wild_tiger::encode($trapPassword), 360));
						
							$redirectPath = (!empty(Session::get('url.intended')) ? Session::get('url.intended') : config('constants.SUCCESS_REDIRECT_MODULE') );
						
							Session::forget('verifyOtp');
						
							return redirect ($redirectPath);
						} else{
							Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.otp-not-match' ) );
							return redirect ('login');
						}
					} else {
						Wild_tiger::setFlashMessage ('danger', trans('messages.expired-otp-message'));
						return redirect(config('constants.LOGIN_URL'));
					}
				}
			}
		}
		
		if( $errorFound != false ){
			return redirect(config('constants.404_PAGE'));
		}
	}
	 
	
	/*  public function forgotpassword(){
		$data['pageTitle'] = trans('messages.forgot-password');
		return view('forgot-password' , $data);
	}
	
	public function sendForgotPasswordMail(Request $request){
		
		$validator = Validator::make($request->all(), [
					'login_email' => 'required',
			],[
					'login_email.required' => __('messages.required-login-email') ,
			]
		);
		
		if ($validator->fails()) {
			return redirect('login')
			->withErrors($validator)
			->withInput();
		}
		
		$email = $request->input('login_email');
		
		$checkLogin = Login::where('v_email',$email)->first();
		
		$checkLogin =  $this->dbObject->getSingleRecordById(  config('constants.LOGIN_MASTER_TABLE') . ' as lm' , 
				[ 'lm.i_id' , 'lm.v_name' , 'lm.v_email' , 'lm.v_role' , 'lm.v_email' , 'lm.v_password' ] ,
				[ 'lm.v_email' => $email , 'lm.t_is_deleted != ' => 1  ]  );
		
		if( empty($checkLogin)){
			Wild_tiger::setFlashMessage('danger', trans('messages.email-not-register') );
			return Redirect::back();
		}
		
		if(isset($checkLogin->t_is_active) && ($checkLogin->t_is_active == 0 ) ){
			Wild_tiger::setFlashMessage('danger', trans('messages.error-login-disable-user') );
			return Redirect::back();
		}
		
		$config = [];
		 
		$subject = trans('messages.password-recovery-mail-subject' , [ 'module' => config( 'constants.SITE_TITLE' ) ] )  ;
		
		
		$config['v_receive_email'] = config ( 'constants.CONTACT_RECEIVE_EMAIL' );
		$config['v_subject'] = $subject;
		
		$encodedData = [];
		$encodedData['user_id'] = $checkLogin->i_id;
		$encodedData['user_email'] = $checkLogin->v_email;
		$encodedData['time'] = strtotime("+".config ( 'constants.FORGET_PASSWORD_CHECK_TIME')." minutes");
		
		$mailData['name'] = $checkLogin->v_name;
		$mailData['link'] =config('app.url') . 'login/newPassword/' . Wild_tiger::encode(json_encode($encodedData));
		
		$config['viewName'] = 'mail/forget-password-mail';
		$config['mailData'] = $mailData;
		 
		// send mail
		$sendMail = Wild_tiger::sendMailSMTP($config);
		
		if( (!empty($sendMail)) && ( $sendMail['status'] != false ) ){
			Session::flash('send_mail', true);
			Wild_tiger::setFlashMessage('success', trans ( 'messages.success-forgot-password-mail', [
					'module' => $checkLogin->v_email
			] ) );
			return Redirect::back();
		}
		
		Session::flash('send_mail', false );
		Wild_tiger::setFlashMessage('success', trans('messages.error-forgot-password-mail') );
		return Redirect::back();
		
		
		
	}
	
	public function newPassword($encodeEmail){
		
		if(!empty($encodeEmail)){
			
			$decodeEmail = trim(Wild_tiger::decode($encodeEmail));
			
			$decodeData = (!empty($decodeEmail) ? json_decode($decodeEmail,true) : [] );
			
			if(!empty($decodeData)){
				//var_dump(date('d-m-Y H:i:s' ,strtotime('now')));
				//var_dump(date('d-m-Y H:i:s' ,$decodeData['time']));
				//var_dump(strtotime('now'));
				//var_dump($decodeData['time']);die;
				if($decodeData['time'] < strtotime('now') ){
					Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-link-expired' ) );
					return redirect ( 'forgotpassword' );
				}
				
				$decodeEmail = $decodeData['user_email'];
				
				$checkLogin = Login::where( [ 'v_email' => $decodeEmail , 't_is_deleted' => 0 ]  )->first();
				
				if(!empty($checkLogin) && (!empty($decodeEmail))){
					$data['user_id'] = $checkLogin->i_id;
					$data['pageTitle'] = trans('messages.reset-password');
					return view('reset-password' , $data);
				} else {
					Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-email-register' ) );
					return redirect ( 'forgotpassword' );
				}
				
				
				
			}
			Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-invalid-link' ) );
			return redirect ( 'forgotpassword' );
			
		}
		
		Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.system-error' ) );
		return redirect::back();
	}
	
	public function updatePassword(Request $request ){
		
		$validator = Validator::make($request->all(), [
				'new_password' => 'required',
				'confirm_password' => 'required|same:new_password',
		],[
				'new_password.required' => __('messages.required-new-password') ,
				'confirm_password.required' => __('messages.required-confirm-password') ,
		]
		);
		
		if ($validator->fails()) {
			return redirect::back()
			->withErrors($validator)
			->withInput();
		}
		
		$requestUserId =  (!empty($request->input('user_id')) ? (int)Wild_tiger::decode($request->input('user_id')) : '' );
		
		$newPassword  = $request->input('new_password');
		$confirmPassword  = $request->input('confirm_password');
		
		if( $requestUserId > 0 ){
			
			if(  $newPassword ==  $confirmPassword ){
				
				$masterUserData = Login::find ( $requestUserId );
				
				$masterUserData->v_decode_password = $newPassword;
				$masterUserData->v_password = password_hash($newPassword, PASSWORD_DEFAULT);;
			
				$updateUser = $masterUserData->save ();
				
				if ($updateUser != false) {
					Wild_tiger::setFlashMessage ( 'success', trans ( 'messages.success-update-password' ) );
					return redirect ( 'login' );
				}
				
				Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.error-update-password' ) );
				return redirect ( 'login' );
			}
			
			Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.confirm-password-not-match' ) );
			return redirect ( 'login' );
			
		}
		
		Wild_tiger::setFlashMessage ( 'danger', trans ( 'messages.system-error' ) );
		return redirect::back();
	}  */
}
