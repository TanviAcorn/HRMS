<?php

use App\BaseModel;
use App\Models\RolePermission;
use App\Models\PermissionMaster;
use Illuminate\Support\Facades\Log;
use App\Models\ReviseSalaryMaster;
use App\EmployeeModel;

function excleColumn($index = 0 ){

	$array = array('AA');
	$current = 'AA';
	while ($current != 'ZZ') {
	    $array[] = ++$current;
	}
	return $array[$index];
}

/* function monthStartDate(){

	$result = date('Y-m-01');
	return $result;

}

function monthEndDate(){

	$result = date('Y-m-t');
	return $result;

} */

function threeNumberSeries($value) {
	
	$result = sprintf("%'03d", $value);
	return $result;
}


function dbDate($value, $dbFormat = true)
{
	$result = null;
	if(!empty($value)){
		$value = str_replace("/", "-", $value);
		$result = date('Y-m-d', strtotime($value));
	}
	return $result;
}

function dbTime($value, $dbFormat = true)
{
	$result = null;
	if(!empty($value)){
		$value = str_replace("/", "-", $value);
		$result = date('H:i:s', strtotime($value));
	}
	return $result;
}

function enumText($value) {
	$result = '';
	if(!empty($value)){
		$result = ucwords(str_replace("_",  " ", $value));
	}
	return $result;
}

function clientDateTime($value)
{
	$result = "";
	if(!empty($value)){
		$result = date('d-m-Y h:i A', strtotime($value));
	}

	return $result;
}

function clientDate($value)
{
	$result = "";
	if(!empty($value)){
		$result = date('d-m-Y', strtotime($value));
	}

	return $result;
}

function apiResponse($status , $messages , $data = [] ){
	$result = [];
	$result['status_code'] = $status;
	$result['message'] = $messages;
	
	//Log::info(print_r($data,true));
	
	if(!empty($data)){
		$result['data'] = (!empty($data) ? $data : null );
	}
	header('Content-Type: application/json');
	echo json_encode($result);die;
}

function last_query(){
	echo BaseModel::last_query();
}

function clientTime($value)
{
	$result = "";
	if(!empty($value)){
		$result = date('h:i A', strtotime($value));
	}

	return $result;
}
function clientCalendarTime($value)
{
	$result = "";
	if(!empty($value)){
		$result = date('g:i A', strtotime($value));
	}

	return $result;
}

function  decimalAmount($value){

	$result = 0;
	if(!empty($value)){
		$value = round($value,2);
		$result = number_format(  $value , 0 , "." , "," );
		$fmt = new \NumberFormatter($locale = 'en_IN', NumberFormatter::DECIMAL);
		$result = $fmt->format($value);
	} else {
		$result = 0.00;
	}

	return $result;
}

function  objectToArray($value){

	$result  = [];
	if(!empty($value)){
		$result = json_decode(json_encode($value) , true ); 
	}
	return $result;
}
function getSelectionYesNoRecordInfo(){
	$data  = [];
	
	$data[config('constants.SELECTION_YES')] = trans('messages.yes');
	$data[config('constants.SELECTION_NO')] = trans('messages.no');
	
	return $data;
}
function getMonthWeeksDaysInfo(){
	$data  = [];
	
	$data[config('constants.MONTH_DURATION')] = trans('messages.months');
	$data[config('constants.WEEKS_DURATION')] = trans('messages.weeks');
	$data[config('constants.DAYS_DURATION')] = trans('messages.days');
	
	return $data;
}
function getSalaryComponentsTypeInfo(){
	$data  = [];

	$data[config('constants.SALARY_COMPONENT_TYPE_EARNING')] = trans('messages.earning');
	$data[config('constants.SALARY_COMPONENT_TYPE_DEDUCTION')] = trans('messages.deduction');
	
	return $data;
}

function genderMaster(){
	$data  = [];

	$data[config('constants.GENDER_MALE')] = trans('messages.male');
	$data[config('constants.GENDER_FEMALE')] = trans('messages.female');

	return $data;
}

function bloodGroupMaster(){
	$data  = [];

	$data[config('constants.A_PLUS_BLOOD_GROUP')] = trans('messages.a-positive');
	$data[config('constants.A_MINUS_BLOOD_GROUP')] = trans('messages.a-negative');
	$data[config('constants.B_PLUS_BLOOD_GROUP')] = trans('messages.b-positive');
	$data[config('constants.B_MINUS_BLOOD_GROUP')] = trans('messages.b-negative');
	$data[config('constants.O_PLUS_BLOOD_GROUP')] = trans('messages.o-positive');
	$data[config('constants.O_MINUS_BLOOD_GROUP')] = trans('messages.o-negative');
	$data[config('constants.AB_PLUS_BLOOD_GROUP')] = trans('messages.ab-positive');
	$data[config('constants.AB_MINUS_BLOOD_GROUP')] = trans('messages.ab-negative');
	

	return $data;
}

function employmentStatusMaster(){
	$data  = [];

	$data[config('constants.PROBATION_EMPLOYMENT_STATUS')] = trans('messages.in-probation');
	$data[config('constants.CONFIRMED_EMPLOYMENT_STATUS')] = trans('messages.confirmed');
	$data[config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')] = trans('messages.in-notice-period');
	$data[config('constants.RELIEVED_PROBATION_EMPLOYMENT_STATUS')] = trans('messages.relieved');
	
	return $data;
}

function convertDateFormat($value, $defaultDate = 'jS M, Y' ){
	$result = "";
	if(!empty($value)){
		$result = date($defaultDate, strtotime($value));
	}

	return $result;
}

function relationInfo(){
	$data  = [];

	$data[config('constants.EMPLOYEE_RELATION_FATHER')] = trans('messages.father');
	$data[config('constants.EMPLOYEE_RELATION_MOTHER')] = trans('messages.mother');
	$data[config('constants.EMPLOYEE_RELATION_SPOUSE')] = trans('messages.spouse');
	$data[config('constants.EMPLOYEE_RELATION_GRAND_MOTHER')] = trans('messages.grand-mother');
	$data[config('constants.EMPLOYEE_RELATION_GRAND_FATHER')] = trans('messages.grand-father');
	$data[config('constants.EMPLOYEE_RELATION_BROTHER')] = trans('messages.brother');
	$data[config('constants.EMPLOYEE_RELATION_SISTER')] = trans('messages.sister');
	$data[config('constants.EMPLOYEE_RELATION_UNCLE')] = trans('messages.uncle');
	$data[config('constants.EMPLOYEE_RELATION_AUNT')] = trans('messages.aunt');
	$data[config('constants.EMPLOYEE_RELATION_SON')] = trans('messages.son');
	$data[config('constants.EMPLOYEE_RELATION_DAUGHTER')] = trans('messages.daughter');
	return $data;
}

function sendMailSMTP($data)
{
	$mailResult = false;
	
	$dbObject = new BaseModel();
	$settingInfo = $dbObject->getSingleRecordById(config('constants.SETTING_TABLE') , [ '*' ]);
	//echo "<pre>";print_r($settingInfo);
	try {
		
		// Get email configuration from database settings or fallback to config/env
		$emailHost = ( isset($settingInfo->v_send_email_host) && (!empty($settingInfo->v_send_email_host)) ) ?  $settingInfo->v_send_email_host :  config('constants.SEND_EMAIL_HOST');
		$emailPort = ( isset($settingInfo->i_send_email_port) && (!empty($settingInfo->i_send_email_port)) ) ?  $settingInfo->i_send_email_port :  config('constants.SEND_EMAIL_PORT');
		$emailUser = ( isset($settingInfo->v_send_email_user) && (!empty($settingInfo->v_send_email_user)) ) ?  $settingInfo->v_send_email_user :  config('constants.SEND_EMAIL_USER');
		$emailPassword = ( isset($settingInfo->v_send_email_password) && (!empty($settingInfo->v_send_email_password)) ) ?  $settingInfo->v_send_email_password :  config('constants.SEND_EMAIL_PASS');
		$emailEncryption = config('constants.SEND_EMAIL_ENCRYPTION');
		
		// Log email configuration for debugging
		\Log::info('OTP Email Configuration:', [
			'host' => $emailHost,
			'port' => $emailPort,
			'user' => $emailUser,
			'encryption' => $emailEncryption,
			'to' => $data['to'] ?? 'not set'
		]);
		
		// Setup your gmail mailer
		$transport = new Swift_SmtpTransport($emailHost, $emailPort, $emailEncryption);
		$transport->setUsername($emailUser);
		$transport->setPassword($emailPassword);
		
		// Any other mailer configuration stuff needed...
		$gmail = new Swift_Mailer($transport);
		$data['from_email'] = $emailUser;
		//$data['receiverEmail'] = ( ( isset($settingInfo->v_contact_receive_mail) && (!empty($settingInfo->v_contact_receive_mail)) ) ?  $settingInfo->v_contact_receive_mail :  config('constants.CONTACT_US_INQUIRY_RECEIVE_MAIL')  ) ;
		//$data['ccEmail'] = ( ( isset($settingInfo->v_default_cc_mail) && (!empty($settingInfo->v_default_cc_mail)) ) ?  $settingInfo->v_default_cc_mail :  ''  ) ;
		//$data['mailTitle'] = ( ( isset($settingInfo->v_site_name) && (!empty($settingInfo->v_site_name)) ) ?  $settingInfo->v_site_name :  config('constants.SITE_TITLE')  ) ;
		
		/*
		// Setup your gmail mailer
		$transport = new Swift_SmtpTransport(config('constants.SEND_EMAIL_HOST'), config('constants.SEND_EMAIL_PORT'), null);
		$transport->setUsername(config('constants.SEND_EMAIL_USER'));
		$transport->setPassword(config('constants.SEND_EMAIL_PASS'));
		// Any other mailer configuration stuff needed...
		$gmail = new Swift_Mailer($transport);
		$data['from_email'] = config('constants.SEND_EMAIL_USER');
		*/
		
		// Set the mailer as gmail
		Mail::setSwiftMailer($gmail);

		if( strtolower($data['to']) == strtolower(config('constants.SYSTEM_ADMIN_EMAIL')) ){
			$data['to'] = config('constants.HR_RECEIVER_EMAIL_ID');
		}
		
		$result = Mail::send((!empty($data['viewName']) ? $data['viewName'] : []), (!empty($data['mailData']) ? $data['mailData'] : []), function ($message) use ($data) {
			$message->from($data['from_email'], config('constants.SEND_EMAIL_TITLE'));
			if( config('constants.SEND_EMAIL_ORIGINAL_USER') == 1 ){
				$message->to($data['to']);
			} else {
				$message->to(config('constants.DEFAULT_RECEIVER_NAME'));
			}
			
			
			$message->subject($data['subject']);
			if (!empty($data['mail_content'])) {
				$message->setBody($data['mail_content'], 'text/html');
			}
			if (!empty($data['attachment'])) {
				$data['attachment'] = json_decode($data['attachment'], true);
				if (!empty($data['attachment'])) {
					foreach ($data['attachment'] as $attchment) {
						$message->attach(($attchment));
						//unlink(public_path($attchment));
					}
				}
			}
		});
		$mailResult = true;
		\Log::info('OTP Email sent successfully to: ' . ($data['to'] ?? 'unknown'));
	} catch (\Exception $e) {
		$mailResult = false;
		$result['msg'] = $e->getMessage();
		\Log::error('OTP Email sending failed: ' . $e->getMessage(), [
			'to' => $data['to'] ?? 'not set',
			'exception' => $e->getTraceAsString()
		]);
	}
	//var_dump($mailResult);
	//var_dump($result);die;
	//var_dump($mailResult);
	if ($mailResult != false) {
		$result['status'] = true;
	} else {
		$result['status'] = false;
	}
	//$result['status'] = true;
	return $result;
}

function maritalStatusInfo(){
	$data  = [];

	$data[config('constants.MARRIED_STATUS')] = trans('messages.married');
	$data[config('constants.UNMARRIED_STATUS')] = trans('messages.unmarried');
	$data[config('constants.WIDOW_WIDOWER_STATUS')] = trans('messages.widow-widower');
	$data[config('constants.DIVORCE_STATUS')] = trans('messages.divorce');
	return $data;
}
function typeOfShiftInfo(){
	$data  = [];

	$data[config('constants.MORNING_SHIFT')] = trans('messages.morning-shift');
	$data[config('constants.AFTERNOON_SHIFT')] = trans('messages.afternoon-shift');
	$data[config('constants.NIGHT_SHIFT')] = trans('messages.night-shift');

	return $data;
}

function timeOffSelectionInfo(){
	$data  = [];

	$data[config('constants.ADJUSTMENT_TIME_OFF')] = trans('messages.adjustment');
	$data[config('constants.OFFICIAL_WORK_TIME_OFF')] = trans('messages.official-work');

	return $data;
}

if (! function_exists('createSlug')) {

	function createSlug($title) {

		// Convert all dashes/underscores into separator
		$flip = $separator = '-';

		$title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

		// Replace @ with the word 'at'
		$title = str_replace('@', $separator.'at'.$separator, $title);

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', twt_lower($title));

		// Replace all separator characters and whitespace by a single separator
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return $title;
	}


}

if (! function_exists('twt_lower')) {

	function twt_lower($value) {
		return mb_strtolower($value, 'UTF-8');
	}
}

if (! function_exists('getDatesFromRange')) {
	function getDatesFromRange($start, $end, $format = 'Y-m-d') {

		// Declare an empty array
		$array = array();

		// Variable that store the date interval
		// of period 1 day
		$interval = new DateInterval('P1D');

		$realEnd = new DateTime($end);
		$realEnd->add($interval);

		$period = new DatePeriod(new DateTime($start), $interval, $realEnd);

		// Use loop to store date into array
		foreach($period as $date) {
			$array[] = $date->format($format);
		}

		// Return the array elements
		return $array;
	}
}

if (! function_exists('yearDetails')) {

	function yearDetails( $minYear = null ) {
		$startYear = config("constants.SYSTEM_START_YEAR");
		if(!empty($minYear)){
			$startYear = $minYear;
		}
		$currentYear = date("Y");
		
		if( $minYear > $currentYear ){
			$currentYear = $minYear;
		}
		
		$allYearArray = [];
		
		for ($i = $startYear; $i <= $currentYear; $i++) {
			$allYearArray[$i] = "Jan ". $i ." - Dec " .  ( $i ) ;
			
		}
		//echo "<pre>";print_r($allYearArray);die;
		return $allYearArray;
	}
}
if (! function_exists('monthListBetweenDates')) {

	function monthListBetweenDates($startDate , $endDate ) {

		$date1  = $startDate;
		$date2  = $endDate;
		$output = [];
		$time   = strtotime($date1);
		$last   = date('m-Y', strtotime($date2));

		do {
			$month = date('m-Y', $time);
			$storeMonth = date('Y-m', $time);
			$total = date('t', $time);

			$output[] = [
					'month' => $storeMonth,
					'total' => $total,
			];

			$time = strtotime('+1 month', $time);
		} while ($month != $last);

		return $output;
	}
}
if (! function_exists('currentSystemYear')) {

	function currentSystemYear() {
		$result  = date("Y") . '-' . ( date("Y") + 1 ) ;
		$result = date("Y");
		return $result;
	}
}

if (! function_exists('getYearStartDate')) {

	function getYearStartDate($year) {
		$result = null;
		if(!empty($year)){
			$yearInfo  = explode("-",$year);
			$startYear = isset($yearInfo[0]) ? $yearInfo[0] : date("Y");
			$startDate = date( $startYear . "-01-01");
			if(!empty($startDate)){
				$result = $startDate;
			}	
		}
		return $result;
	}
}

if (! function_exists('getYearEndDate')) {

	function getYearEndDate($year) {
		$result = null;
		if(!empty($year)){
			$yearInfo  = explode("-",$year);
			$endYear = isset($yearInfo[0]) ? $yearInfo[0] : date("Y");
			$endDate = date( $endYear . "-12-31");
			if(!empty($endDate)){
				$result = $endDate;
			}
		}
		return $result;
	}
}
function stausInfo(){
	$data  = [];

	$data[config('constants.PENDING_STATUS')] = trans('messages.pending');
	$data[config('constants.APPROVED_STATUS')] = trans('messages.approved');
	$data[config('constants.REJECTED_STATUS')] = trans('messages.rejected');
	$data[config('constants.CANCELLED_STATUS')] = trans('messages.cancelled');
	return $data;
}
function typeInfo(){
	$data  = [];

	$data[config('constants.ADJUSTMENT_STATUS')] = trans('messages.adjustment');
	$data[config('constants.OFFICIAL_WORK_TIME_OFF')] = trans('messages.official-work');
	
	return $data;
}

if (! function_exists('getMonthStartDate')) {

	function getMonthStartDate($month) {
		$result = null;
		if(!empty($month)){
			$date  = "01-".$month;
			$startDate = dbDate( $date);
			if(!empty($startDate)){
				$result = $startDate;
			}
		}
		return $result;
	}
}

if (! function_exists('getMonthEndDate')) {

	function getMonthEndDate($month) {
		$result = null;
		if(!empty($month)){
			$date  = "01-".$month;
			$endDate  = date('Y-m-t',strtotime($date));
			if(!empty($endDate)){
				$result = $endDate;
			}
		}
		return $result;
	}
}

if (! function_exists('monthStartDate')) {

	function monthStartDate($date = null ) {
		$result = null;
		$inputDate = (!empty($date) ? $date : date('Y-m-d'));
		if(!empty($inputDate)){
			$result  = date('Y-m-01', strtotime($inputDate) );
		}
		return $result;
	}
}

if (! function_exists('monthEndDate')) {

	function monthEndDate($date = null) {
		$result = null;
		$inputDate = (!empty($date) ? $date : date('Y-m-d'));
		if(!empty($inputDate)){
			$result  = date('Y-m-t', strtotime($inputDate) );
		}
		return $result;
	}
}

if (! function_exists('getInitialLetter')) {

	function getInitialLetter($string = null ) {
		$result = null;
		if(!empty($string)){
			$pos = strpos($string, " ");
			if( $pos !== false ){
				$result = ucwords( $string[0] ) . ucwords( $string[$pos + 1] ) ;
			} else {
				$result = ucwords( $string[0] ) ;
			}
		}
		return $result;
	}
}

if (! function_exists('weekDayDetails')) {

	function weekDayDetails() {
		$data = [];
		$data['monday'] = trans('messages.monday');
		$data['tuesday'] = trans('messages.tuesday');
		$data['wednesday'] = trans('messages.wednesday');
		$data['thursday'] = trans('messages.thursday');
		$data['friday'] = trans('messages.friday');
		$data['saturday'] = trans('messages.saturday');
		$data['sunday'] = trans('messages.sunday');
		return $data;
	}
}

if (! function_exists('getNextWeekDay')) {
	function getNextWeekDay($startDate, $weekDay) {
		$result = null;
		$date = new DateTime($startDate);
		$date->modify('next ' . $weekDay );
        $result = $date->format('Y-m-d');
        return $result;
	}
}

if (! function_exists('employeeProfilePicView')) {
	function employeeProfilePicView( $employeeData ) {
		//Log::info('employee data ');
		//Log::info(print_r($employeeData,true));
		$profilePic = ( ( isset($employeeData['profile_pic'] ) && (!empty($employeeData['profile_pic']))  && file_exists( config('constants.FILE_STORAGE_PATH') . config('constants.UPLOAD_FOLDER') . $employeeData['profile_pic'] ) )  ? config('constants.FILE_STORAGE_PATH_URL') .  config('constants.UPLOAD_FOLDER') . $employeeData['profile_pic'] : "" );
		//Log::info('profile pic =  '.$profilePic);
		$employeeName = ( isset($employeeData['employee_name']) ? $employeeData['employee_name'] : "" );
		$employeeCode = ( isset($employeeData['employee_code']) ? $employeeData['employee_code'] : "" );
		$html = "";
		if( (!empty($profilePic)) ){
			$html.= '<div class="member-img-card">';
			$html.= '<img src="'.$profilePic.'" alt="icon" class="member-img">';
			$html.= '</div>';
		} else {
			$html .= '<div class="member-img-card member-img-none bg-3">';
			$html .= '<p class="member-img-text" title="'.getInitialLetter($employeeName). (!empty($employeeCode) ? ' ('.$employeeCode .')' : '' ).'">'. getInitialLetter($employeeName). (!empty($employeeCode) ? ' ('.$employeeCode .')' : '' ) . '</p>';
			$html .= '</div>';
		}
		return $html;
	}
}

if (! function_exists('getAppliedLeaveMonth')) {
	function getAppliedLeaveMonth($leaveDate) {

		$date = date('d' , strtotime($leaveDate));
		
		$salaryCycleStartDate = config('constants.SALARY_CYCLE_START_DATE');
		if( $date >= $salaryCycleStartDate ){
			$leaveMonth = date('Y-m-01', strtotime('+1 month', strtotime($leaveDate)));
		} else {
			$leaveMonth = date('Y-m-01' , strtotime( $leaveDate ) );
		}
		
		// Return the array elements
		return $leaveMonth;
	}
}

if (! function_exists('diffBetweenTimeIntoHours')) {
	function diffBetweenTimeIntoHours($strtaTime, $endTime) {

		$ts1 = strtotime($strtaTime);
		$ts2 = strtotime($endTime);
		$diff = abs($ts1 - $ts2) / 3600;
		$diff = round($diff,2);
		// Return the array elements
		return $diff;
	}
}

if (! function_exists('errorLogEntry')) {
	function errorLogEntry( $event , $data = [] )
	{
		//Log::info('event = ' .$event  );
		//Log::info(print_r($data,true));
	}
}

if (! function_exists('randomPassword')) {
	function randomPassword($length = 8 ) {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < $length; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
}

if (! function_exists('convertNumberIntoWords')) {
	function convertNumberIntoWords($value = null) {
		$data = [];
		for($i = 1 ; $i <= 60 ;$i++){
			$fmt = new \NumberFormatter($locale = 'en_IN', NumberFormatter::SPELLOUT);
			$result = $fmt->format($i);
			$data[$i] = ucwords(str_replace("-", " ", $result));
		}
		
		/* $data[2] = "Two";
		$data[3] = "Three";
		$data[4] = "Four";
		$data[5] = "Fifth";
		$data[6] = "Six";
		$data[7] = "Seven"; */
		
		//echo "<pre>";print_r($data);
		
		return $data;
	}
}
function employmentDateStatus(){
	$data  = [];

	$data[config('constants.JOINING_DATE_STATUS')] = trans('messages.joining-date');
	$data[config('constants.PROBATION_PERIOD_STATUS')] = trans('messages.probation-period');
	$data[config('constants.NOTICE_PERIOD_STATUS')] = trans('messages.notice-period');
	$data[config('constants.LAST_WORKING_DATE_STATUS')] = trans('messages.last-working-date');

	return $data;
}

if (! function_exists('diffBetweenTimeIntoSecond')) {
	function diffBetweenTimeIntoSecond($endTime , $strtaTime) {
		$result = [];
		$result = strtotime($endTime) - strtotime($strtaTime);
		
		return $result;
	}
}

function convertSecondIntoHour($second , $format = "H:i") {
	$result = null;
	if(!empty($second)){
		$result =  gmdate($format, $second);
	}
	return $result;
}

if (! function_exists('date_compare')) {
	function date_compare($element1, $element2 , $keyName = 'compare_date') {
		$datetime1 = strtotime($element1['compare_date']);
		$datetime2 = strtotime($element2['compare_date']);
		return $datetime1 - $datetime2 ;
		return $datetime2 - $datetime1 ;
	}
}

if (! function_exists('action_date')) {
	function action_date($element1, $element2 , $keyName = 'action_date') {
		$datetime1 = strtotime($element1['action_date']);
		$datetime2 = strtotime($element2['action_date']);
		return $datetime1 - $datetime2 ;
		return $datetime2 - $datetime1 ;
	}
}

if (! function_exists('dashboardEventDate')) {
	function dashboardEventDate($date = 'today', $format = 'd F') {
		$dateFormat = date('Y-m-d', strtotime($date));
	    $readDate = '';
	    if ($dateFormat == date('Y-m-d')) {
	        $readDate = 'Today';
	    } elseif ($dateFormat == date('Y-m-d', strtotime('tomorrow'))) {
	        $readDate = 'Tomorrow';
	    } elseif ($dateFormat == date('Y-m-d', strtotime('yesterday'))) {
	        $readDate = 'Yesterday';
	    } else {
	    	
	    	if(date('m-d',strtotime("+1 days")) == substr($dateFormat,5,5) ){
	    		$readDate = 'Tomorrow';
	    	} else {
	    		$readDate = date($format, strtotime($date));
	    	}
	    	
	        
	    }
	
	    return $readDate;
	}
}
if(! function_exists("differenceTimeAndHours")){
	function differenceTimeAndHours($startTime = null ,$endTime = null){
		$totalHourseAndMinute = "";
		$startTime = new \DateTime($startTime);
		$endTime = new \DateTime($endTime);
		$difference = $startTime->diff($endTime);
		//echo "<Pre> difference";print_r($difference);
		$hours = (!empty($difference->h) ? ($difference->h.' ' . ( $difference->h > 1 ? ' hrs ' :  ' hr '  ) ) : '' );
		$minute = (!empty($difference->i) ? ($difference->i.' ' . ( $difference->i > 1 ? ' mins' :  ' min'  ) ) : '' );
		
		if( (!empty($hours)) && (!empty($minute))  ){
			$totalHourseAndMinute =  $hours . ' ' . $minute;
		} else {
			$totalHourseAndMinute = (!empty($hours) ? $hours : '' ) . (!empty($minute) ? $minute : '');
		}
		return $totalHourseAndMinute;
	}
}


if(! function_exists("convertSecondIntoHourMinute")){
	function convertSecondIntoHourMinute($seconds , $startTime = null , $endTime = null ){
		
		if(!empty($startTime) && (!empty($endTime))){
			$seconds = strtotime($startTime) - strtotime($endTime);
		}
		//var_dump($seconds);echo "<br><br>";
		$hours = floor($seconds / 3600);
		$seconds -= $hours * 3600;
		$minutes = floor($seconds / 60);
		$seconds -= $minutes * 60;
		
		//var_dump($hours);echo "<br><br>";
		//var_dump($minutes);echo "<br><br>";
		
		$result = "";
		if( (!empty($hours)) && (!empty($minutes))  ){
			$result =  $hours . ( $hours > 1 ? ' hrs ' :  ' hr '  )  . $minutes . ( $minutes > 1 ? ' mins ' :  ' min'  );
		} else {
			$result = (!empty($hours) ? $hours . ( $hours > 1 ? ' hrs ' :  ' hr '  ) : '' ) . (!empty($minutes) ? $minutes . ( $minutes > 1 ? ' mins ' :  ' min'  )  : '');
		}
		return $result;
		
	}
}

if(! function_exists("checkPermission")){
	function checkPermission($module){
		$result = false;
		if (session()->has('role') && !empty(session()->get('role')) && session()->get('role') == config('constants.ROLE_ADMIN')) {
			$result = true;
		} else {
			$whereData = [];
			// $whereData['v_name'] = trim($module);
			$whereData['t_is_active'] = 1;
			$whereData['t_is_deleted'] = 0;
			
			if(is_string($module)){
				$module = explode(',', $module);
			}			

			$getPermissionDetails = PermissionMaster::where($whereData)->whereIn('v_name', $module)->get();
			$getUserRole = EmployeeModel::select('i_id', 'i_role_permission')->where('i_login_id', session()->get('user_id'))->first();

			if (isset($whereData['v_name'])) {
				unset($whereData['v_name']);
			}
			
			//session()->put('show_all' , false);
			if (isset($getPermissionDetails) && !empty($getPermissionDetails) && count($getPermissionDetails) > 0 && isset($getUserRole) && !empty($getUserRole) && isset($getUserRole->i_role_permission) && !empty($getUserRole->i_role_permission)) {
				$getRolePermissionInfo = RolePermission::where($whereData)
					->where('i_id', $getUserRole->i_role_permission)
					->first();
				
				if( isset($getRolePermissionInfo->v_permission_ids) && (!empty($getRolePermissionInfo->v_permission_ids)) ){
					session()->put('user_permission' , explode("," , $getRolePermissionInfo->v_permission_ids  ));
				}
				
				//echo "<pre> getRolePermissionInfo   ";print_r($getRolePermissionInfo);
				//echo "<pre> getPermissionDetails   ";print_r($getPermissionDetails);
				foreach ($getPermissionDetails as $getPermissionDetail){
					if (isset($getRolePermissionInfo) && !empty($getRolePermissionInfo) && !empty($getRolePermissionInfo->v_permission_ids) && in_array($getPermissionDetail->i_id, (explode(',', $getRolePermissionInfo->v_permission_ids)))) {
						if(  isset($getPermissionDetail->i_all_record_id) && (!empty($getPermissionDetail->i_all_record_id)) && in_array( $getPermissionDetail->i_all_record_id ,   explode(',', $getRolePermissionInfo->v_permission_ids) ) ){
							//session()->put('show_all' , true);
						}
						$result = true;
					}					
				}
			}
		}
		return $result;
	}
}

if(! function_exists("generatePDF")){
	function generatePDF( $html , $fileName , $type = "view" ){
		
		$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
		
		$fontDirs = $defaultConfig['fontDir'];
		$defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
		$fontDirs = $defaultConfig['fontDir'];
		$defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];
		$fontData += [
				'poppins-regular' => [
						'R' => 'Poppins-Regular.ttf',
				]
		];
		$fontData += [
				'poppins-medium' => [
						'R' => 'Poppins-Medium.ttf',
				]
		];
		$fontdata = [
				'poppins-bold' => [
						'R' => 'Poppins-Bold.ttf',
				],
		];
		
		$mpdf = new \Mpdf\Mpdf([
				'mode' => 'c',
				'format' => 'A4',
				'margin_left' => 3,
				'margin_right' => 3,
				'margin_top' => 3,
				'margin_bottom' => 3,
				'margin_header' => 3,
				'margin_footer' => 3,
				'fontDir' => array_merge($fontDirs, [
						dirname(dirname(__DIR__)) . '/assets/css/fonts/',
				]),
				'fontdata' => $fontData,
				'mode' => 'utf-8',
		]);
		
		$mpdf->SetWatermarkImage( ('images/icon1.png'), 0.1, '');
		$mpdf->autoPageBreak = true;
		
		
		
		$header  = '';
		$header .= '<div class="main-page-border-outer vh100" style="border: 3px solid #000; padding: 100px;height:100%;width:100%">';
		$header .= '<div class="px-20" style="padding:3px;padding-left: 20px; padding-right: 20px;">';
		$footer = '';
		$footer = '<table cellpadding="20" cellspacing="0" style="width:100%; font-family: Poppins, sans-serif; vertical-align:top;">
			<tbody>';
		
			/*  $footer .= '<tr>
					<td style="text-align: right;"><strong>For, '.config('constants.COMPANY_NAME').'</strong></td>
					<br><br>
				</tr>';
		$footer .=		'<tr>
					<td style="text-align: right;">Authorised Signatory</td>
					<br><br>s
				</tr>';
				*/
		$footer .=		'<tr>
					<td style="text-align: center;">This is computer generated Pay Slip signature not required.</td>
					<br>
				</tr>'; 
		$footer .=	'</tbody>
		</table></div>';
		$footer .= '</div>';
		$footer .= '</div>';
		
		if(!empty($fileName)){
			$mpdf->SetTitle(basename($fileName));
		}
		
		$mpdf->SetHTMLHeader($header);
		$mpdf->SetHTMLFooter($footer);
		$mpdf->showWatermarkImage = true;
		$mpdf->WriteHTML($html, 2);
		
		
		//var_dump($type);die;
		
		switch($type){
			case config('constants.VIEW_PDF'):
				$mpdf->Output($fileName . '.pdf','I');
				break;
			case config('constants.DOWNLOAD_PDF'):
				$mpdf->Output ( $fileName . '.pdf' , 'D');
				break;
			case config('constants.STORE_PDF'):
				$storeFolderPath = config('constants.FILE_STORAGE_PATH') . config('constants.UPLOAD_FOLDER') . 'pay-slip/' ;
				if (! file_exists( $storeFolderPath )) {
					mkdir( $storeFolderPath , 0777, true);
				}
				//var_dump($storeFolderPath);die;
				$storeFilePath = $storeFolderPath . $fileName . '.pdf';
				$mpdf->Output ( $storeFilePath , 'F');
				$response = [];
				$response['status'] = true;
				$response['filePath'] = $storeFilePath;
				return $response;
				break;
			default:
				$mpdf->Output($fileName,'I');
		}
		
	}
}

if (! function_exists('leaveMonthReportValue')) {
	function leaveMonthReportValue($date) {
		$result = "";
		if(!empty($date)){
			$result = date("M" , strtotime("-1 month" , strtotime($date))) . ' - ' .date("M Y" , strtotime($date));
		}

		return $result;
	}
}

if (! function_exists('leaveDateMonth')) {
	function leaveDateMonth($date) {
		$result = "";
		if( date("d" , strtotime($date)) <= 15 ){
			if( ( strtotime($date) >= strtotime( date("Y-m-16" , strtotime("-1 month" , strtotime($date))) ) ) && ( strtotime($date) <= strtotime( date("Y-m-15" , strtotime($date) ) )  ) ){
				$result = date("Y-m-01" , strtotime($date));
			}
		} else {
			if( ( strtotime($date) >= strtotime( date("Y-m-16" , strtotime($date)) ) ) && ( strtotime($date) <= strtotime( date("Y-m-15" , strtotime( "+1 month" , strtotime(date("Y-m-15" , strtotime($date) ))  ) ) )  ) ){
				$result = date("Y-m-01" , strtotime( "+1 month" , strtotime( $date ) ) );
			}
		}

		return $result;
	}
}

if (! function_exists('financialYearMonthList')) {

	function financialYearMonthList($startYear, $endYear )
	{
		$allMonthNames = [];
		if( strtotime(getMonthStartDate("April-".$startYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['April'] = date('Y-m-d' , strtotime("April-".$startYear)) ;
		}
		if( strtotime(getMonthStartDate("May-".$startYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['May'] = date('Y-m-d' , strtotime("May-".$startYear)) ; //"May-".$startYear;
		}
		if( strtotime(getMonthStartDate("June-".$startYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['June'] = date('Y-m-d' , strtotime("June-".$startYear)) ;// "June-".$startYear;
		}
		if( strtotime(getMonthStartDate("July-".$startYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['July'] = date('Y-m-d' , strtotime("July-".$startYear)) ;// "July-". $startYear;
		}
		if( strtotime(getMonthStartDate("August-".$startYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['August'] = date('Y-m-d' , strtotime("August-".$startYear)) ;// "August-".$startYear;
		}
		if( strtotime(getMonthStartDate("September-".$startYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['September'] = date('Y-m-d' , strtotime("September-".$startYear)) ;// "September-".$startYear;
		}
		if( strtotime(getMonthStartDate("October-".$startYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['October'] = date('Y-m-d' , strtotime("October-".$startYear)) ;// "October-".$startYear;
		}
		if( strtotime(getMonthStartDate("November-".$startYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['November'] = date('Y-m-d' , strtotime("November-".$startYear)) ;// "November-".$startYear;
		}

		if( strtotime(getMonthStartDate("December-".$startYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['December'] = date('Y-m-d' , strtotime("December-".$startYear)) ;// "December-".$startYear;
		}

		if( strtotime(getMonthStartDate("January-".$endYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['January'] = date('Y-m-d' , strtotime("January-".$endYear)) ;// "January-".$endYear;
		}
		if( strtotime(getMonthStartDate("February-".$endYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['February'] = date('Y-m-d' , strtotime("February-".$endYear)) ;// "February-".$endYear;
		}
		if( strtotime(getMonthStartDate("March-".$endYear)) <= strtotime(date('Y-m-t', strtotime("+1 month")))  ){
			$allMonthNames['March'] = date('Y-m-d' , strtotime("March-".$endYear)) ;// "March-".$endYear;
		}

		return $allMonthNames;
	}
}


if (! function_exists('yearAllMonthDetails')) {

	function yearAllMonthDetails($startYear, $endYear )
	{
		$allMonthNames = [];
		$allMonthNames['April'] = date('Y-m-01' , strtotime("April-".$startYear)) ;
		$allMonthNames['May'] = date('Y-m-01' , strtotime("May-".$startYear)) ;
		$allMonthNames['June'] = date('Y-m-01' , strtotime("June-".$startYear)) ;
		$allMonthNames['July'] = date('Y-m-01' , strtotime("July-".$startYear)) ;
		$allMonthNames['August'] = date('Y-m-01' , strtotime("August-".$startYear)) ;
		$allMonthNames['September'] = date('Y-m-01' , strtotime("September-".$startYear)) ;
		$allMonthNames['October'] = date('Y-m-01' , strtotime("October-".$startYear)) ;
		$allMonthNames['November'] = date('Y-m-01' , strtotime("November-".$startYear)) ;
		$allMonthNames['December'] = date('Y-m-01' , strtotime("December-".$startYear)) ;
		$allMonthNames['January'] = date('Y-m-01' , strtotime("January-".$endYear)) ;// "January-".$endYear;
		$allMonthNames['February'] = date('Y-m-01' , strtotime("February-".$endYear)) ;// "January-".$endYear;
		$allMonthNames['March'] = date('Y-m-01' , strtotime("March-".$endYear)) ;// "January-".$endYear;
		
		return $allMonthNames;
	}
}

if (! function_exists('getCurrentFinancialYear')) {
	function getCurrentFinancialYear($yearFormat = "Y" , $inputDate = null ){
		$date = (!empty($inputDate) ? $inputDate : date('Y-m-d') )  ;
		$time = strtotime($date);
		$year = date($yearFormat, $time);
		if(date('n', $time) > 3){
			$ayear = ($year).'-'.($year + 1);
		}
		else{
			$ayear = ($year - 1).'-'.$year;
		}

		return $ayear;
	}
}


function attendanceStatus(){
	$data  = [];

	$data[config('constants.ABSENT_STATUS')] = trans('messages.absent');
	$data[config('constants.PRESENT_STATUS')] = trans('messages.present');
	$data[config('constants.HALF_LEAVE_STATUS')] = trans('messages.half-leave');
	return $data;
}

if (! function_exists('attendanceStartDate')) {
	function attendanceStartDate($month , $year) {

		$startDate = date( 'Y-m-d' , strtotime("-1 month" , strtotime($year.'-'.$month.'-'.config('constants.SALARY_CYCLE_START_DATE') )) );
		$endDate = $year.'-'.$month.'-'.( config('constants.SALARY_CYCLE_START_DATE') - 1 );
		return $startDate;
	}
}

if (! function_exists('attendanceEndDate')) {
	function attendanceEndDate($month , $year) {

		$endDate = $year.'-'.$month.'-'.( config('constants.SALARY_CYCLE_START_DATE') - 1 );
		return $endDate;
	}
}
function holdAmountStatusDetails(){
	$data  = [];

	$data[config('constants.PENDING_STATUS')] = trans('messages.pending');
	$data[config('constants.PAID_STATUS')] = trans('messages.paid');
	$data[config('constants.NOT_TO_PAY_STATUS')] = trans('messages.not-to-pay');
	$data[config('constants.DONATED_STATUS')] = trans('messages.donated');

	return $data;
}

function getHoldAmountInfo($recordDetail){
	$data  = [];
	$data['totalOnHoldSalaryAmount'] = 0;
	$data['deductOnHoldSalaryAmount'] = 0;
	$data['leftOnHoldSalaryAmount'] = 0;
	$data['expectedReleaseDate'] = null;
	$data['releaseDate'] = null;
	
	$allExepctedReleaseDate = [];
	$totalOnHoldSalaryAmount = 0;
	$decuctOnHoldSalaryAmount = 0;
	$expectedReleasedDate = "";
	$allExepctedReleaseDate = [];
	$allReleaseDate = [];
	if( isset($recordDetail->onHoldSalaryInfo) && (!empty($recordDetail->onHoldSalaryInfo)) ){
		foreach($recordDetail->onHoldSalaryInfo as $onHoldSalaryAmount){
			if ( isset($onHoldSalaryAmount->d_amount) && (!empty($onHoldSalaryAmount->d_amount)) ){
				$allExepctedReleaseDate[]=  $onHoldSalaryAmount->dt_month;
				$totalOnHoldSalaryAmount +=  $onHoldSalaryAmount->d_amount;
			}
		}
	}
	//echo "<pre>";print_r($recordDetail);
	if( isset($recordDetail->generatedSalaryMaster) && (!empty($recordDetail->generatedSalaryMaster)) ){
		foreach( $recordDetail->generatedSalaryMaster  as $salaryMaster){
			if( $salaryMaster->t_is_salary_generated == 1  ){
				if( isset($salaryMaster->generatedSalaryInfo) && (!empty($salaryMaster->generatedSalaryInfo)) ){
					//echo "<pre>";print_r($salaryMaster->generatedSalaryInfo);die;
					foreach( $salaryMaster->generatedSalaryInfo  as $generatedSalaryAmount ){
						if ( isset($generatedSalaryAmount->d_paid_amount) && (!empty($generatedSalaryAmount->d_paid_amount)) && ( $generatedSalaryAmount->i_component_id == config('constants.ON_HOLD_SALARY_COMPONENT_ID') ) ){
							if( isset($salaryMaster->dt_salary_month)  && (!empty($salaryMaster->dt_salary_month)) ){
								$allReleaseDate[]=  $salaryMaster->dt_salary_month;
							}
							$decuctOnHoldSalaryAmount +=  $generatedSalaryAmount->d_paid_amount;
						}
					}
				}
			}
		}
	}
	//echo "<pre>";print_r($allReleaseDate);die;
	$expectedReleasedDate = (!empty($allExepctedReleaseDate) ? max($allExepctedReleaseDate) : "" );
	$leftAmount = ( ( $totalOnHoldSalaryAmount - $decuctOnHoldSalaryAmount ) > 0 ? $totalOnHoldSalaryAmount - $decuctOnHoldSalaryAmount : 0 ) ;
	
	$data['totalOnHoldSalaryAmount'] = $totalOnHoldSalaryAmount;
	$data['deductOnHoldSalaryAmount'] = $decuctOnHoldSalaryAmount;
	$data['leftOnHoldSalaryAmount'] = $leftAmount;
	$data['expectedReleaseDate'] = $expectedReleasedDate;
	$data['releaseDate'] = null;
	if( isset($recordDetail->e_hold_salary_payment_status) && ( $recordDetail->e_hold_salary_payment_status == config('constants.PAID_STATUS')) ){
		$data['releaseDate'] = (!empty($allReleaseDate) ? max($allReleaseDate) : "" );;
	}

	return $data;
}

function convertAmountIntoWord($value){

	$result = "";
	if(!empty($value)){
		$value = round($value,2);
		$result = NumberintoWords($value);
		//$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
		//$result = $f->format($value);
		//$result = (!empty($result) ? ucwords($result) . ' Rupees Only.' : "");
	} else {
		$result = "";
	}

	return $result;
}

if (! function_exists('attendanceDayWiseReportInfo')) {
	function attendanceDayWiseReportInfo( $recordDetail , $monthAllDates , $employeeWiseSuspendRecordDetails = [] , $employeeWiseWeekOffDates = [] , $monthHolidayDates = [] ) {
	//var_dump($recordDetail->i_id);echo "<br><br>";
		$allAttedanceDetails = ( isset($recordDetail->attendanceEmployeeInfo) ? $recordDetail->attendanceEmployeeInfo : [] );
		$allAttedanceDates = (!empty($allAttedanceDetails ) ?  array_column(objectToArray($allAttedanceDetails), 'dt_date') : [] );
		//echo "<pre>";print_r($allAttedanceDetails);
		$allSuspendDates = ( isset($employeeWiseSuspendRecordDetails[$recordDetail->i_id]) ? $employeeWiseSuspendRecordDetails[$recordDetail->i_id] : [] );
		$allWeekOffDates = ( isset($employeeWiseWeekOffDates[$recordDetail->i_id]) ? $employeeWiseWeekOffDates[$recordDetail->i_id] : [] );
		$allHolidayDates = ( isset($monthHolidayDates) ? $monthHolidayDates : [] );
		$absentCount = 0;
		$halfLeaveCount = 0;
		$dateWiseStatus = [];
		
		//echo "<pre>";print_r($allSuspendDates);
	//	echo "<pre>";print_r($allWeekOffDates);
		//echo "<pre>";print_r($allHolidayDates);
		
		$employeeJoiningDate = (!empty($recordDetail->dt_joining_date) ? $recordDetail->dt_joining_date : null );
		$releaseDate = (!empty($recordDetail->dt_release_date) ? $recordDetail->dt_release_date : null );
		
		
		
		$employeeLeaveDetails = ( isset($recordDetail->myLeaveMaster) ? $recordDetail->myLeaveMaster : [] ); 
		$employeeLeaveDates = [];
		$allAppliedLeaveDates = [];
		$allAppliedHalfLeaveDates = [];
		if(!empty($employeeLeaveDetails)){
			foreach($employeeLeaveDetails as $employeeLeaveDetail){
				if( in_array( $employeeLeaveDetail->e_status , [ config('constants.APPROVED_STATUS') , config('constants.PENDING_STATUS') ] ) ){
					if( strtotime($employeeLeaveDetail->dt_leave_from_date) !=  strtotime($employeeLeaveDetail->dt_leave_to_date) ){
						$leaveDateRanges = getDatesFromRange($employeeLeaveDetail->dt_leave_from_date,$employeeLeaveDetail->dt_leave_to_date);
						if(!empty($leaveDateRanges)){
							foreach($leaveDateRanges as $leaveDateRange){
								if(in_array( $leaveDateRange , $monthAllDates )){
									
									if( ( strtotime($employeeLeaveDetail->dt_leave_from_date) == strtotime( $leaveDateRange ) ) ||  ( strtotime($employeeLeaveDetail->dt_leave_to_date) ==  strtotime( $leaveDateRange ) ) ){
										if($employeeLeaveDetail->e_from_duration == config('constants.FIRST_HALF_LEAVE')){
											$allAppliedLeaveDates[] = $leaveDateRange;
										} else {
											$allAppliedHalfLeaveDates[] = $leaveDateRange;
										}
										
										if($employeeLeaveDetail->e_to_duration	 == config('constants.FIRST_HALF_LEAVE')){
											$allAppliedHalfLeaveDates[] = $leaveDateRange;
										} else {
											$allAppliedLeaveDates[] = $leaveDateRange;
										}
									} else {
										$allAppliedLeaveDates[] = $leaveDateRange;
									}
									
									
								}
							}
						}
					} else {
						if(in_array( $employeeLeaveDetail->dt_leave_from_date , $monthAllDates )){
							if( in_array( $employeeLeaveDetail->e_duration , [ config('constants.FIRST_HALF_LEAVE') , config('constants.SECOND_HALF_LEAVE') ]  ) ){
								$allAppliedHalfLeaveDates[] = $employeeLeaveDetail->dt_leave_from_date;
							} else {
								$allAppliedLeaveDates[] = $employeeLeaveDetail->dt_leave_from_date;
							}
						}
					
					}
				}  
			}
		}
		
		//echo "<pre>";print_r($monthAllDates);
		//echo "<pre>";print_r($allSuspendDates);
		//echo "<pre>";print_r($allHolidayDates);
		//echo "<pre>";print_r($allWeekOffDates); 
		
		if(count($monthAllDates) > 0 ){
			foreach($monthAllDates as $monthAllDate){
				$displayStatus = "";
				if((!empty($employeeJoiningDate)) && (strtotime($employeeJoiningDate) > strtotime($monthAllDate))){
					$dateWiseStatus[$monthAllDate] = $displayStatus;
					continue;
				}
				
				if((!empty($releaseDate)) && (strtotime($releaseDate) <= strtotime($monthAllDate))){
					$dateWiseStatus[$monthAllDate] = $displayStatus;
					continue;
				}
				
			    
                                		
                if( in_array( $monthAllDate ,  $allHolidayDates ) ){
                	//$displayStatus = (!empty($displayStatus) ? PHP_EOL  .  config('constants.HOLIDAY_SYMBOL') : config('constants.HOLIDAY_SYMBOL')  ) ;
                	$displayStatus = config('constants.HOLIDAY_SYMBOL');
                	$dateWiseStatus[$monthAllDate] = $displayStatus;
                	continue;
              	}
                                		
                if( in_array( $monthAllDate ,  $allWeekOffDates ) ){
                	if( strtolower( date('D' , strtotime($monthAllDate) ) ) == "sun" ){
                		//$displayStatus = (!empty($displayStatus) ? PHP_EOL .  trans('messages.sun') : trans('messages.sun')  ) ;
                		$displayStatus = trans('messages.sun');
                	} else {
                		$displayStatus = (!empty($displayStatus) ? PHP_EOL .  config('constants.WEEKOFF_SYMBOL') : config('constants.WEEKOFF_SYMBOL')  ) ;
                		//$displayStatus = config('constants.WEEKOFF_SYMBOL');
                	}
                	$dateWiseStatus[$monthAllDate] = $displayStatus;
                	continue;
                	
                }
                
                
                                		
                if( in_array( $monthAllDate , $allAttedanceDates ) ){
                	$searchKey = array_search($monthAllDate , $allAttedanceDates );
                    if( ( strlen($searchKey) > 0 ) && ( isset($allAttedanceDetails[$searchKey]->e_status) ) && (!empty($allAttedanceDetails[$searchKey]->e_status)) ){
                    	switch($allAttedanceDetails[$searchKey]->e_status){
                        	case config('constants.ABSENT_STATUS'):
                            	
                        		if( in_array( $monthAllDate ,  $allSuspendDates ) ){
                        			//$displayStatus = (!empty($displayStatus) ? PHP_EOL .  config('constants.SUSPEND_SYMBOL') : config('constants.SUSPEND_SYMBOL')  ) ;
                        			$displayStatus = config('constants.SUSPEND_SYMBOL');
                        			$dateWiseStatus[$monthAllDate] = $displayStatus;
                        			$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
                        		} else {
                        			$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
                        			$displayStatus = config('constants.ABSENT_SYMBOL');
                        		}
                        		
                        		
                                break;
                          	case config('constants.HALF_LEAVE_STATUS'):
                            	$halfLeaveCount = $halfLeaveCount + config('constants.HALF_LEAVE_VALUE');
                                $displayStatus = config('constants.HALF_LEAVE_SYMBOL');
                            	break;
                       	}
                    } else {
                    	
                    	if( in_array( $monthAllDate ,  $allSuspendDates ) ){
                    		//$displayStatus = (!empty($displayStatus) ? PHP_EOL .  config('constants.SUSPEND_SYMBOL') : config('constants.SUSPEND_SYMBOL')  ) ;
                    		$displayStatus = config('constants.SUSPEND_SYMBOL');
                    		$dateWiseStatus[$monthAllDate] = $displayStatus;
                    		$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
                    		continue;
                    	}
                    	
                    	$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
                    	$displayStatus = config('constants.ABSENT_SYMBOL');
                    }
            	} else {
            		if(in_array($monthAllDate , $allAppliedHalfLeaveDates)){
            			$halfLeaveCount = $halfLeaveCount + config('constants.HALF_LEAVE_VALUE');
            			$displayStatus = config('constants.HALF_LEAVE_SYMBOL');
            		} else {
            			$absentCount = $absentCount + config('constants.FULL_LEAVE_VALUE');
            			$displayStatus = config('constants.ABSENT_SYMBOL');
            		} 
            		
            	}
            	$dateWiseStatus[$monthAllDate] = $displayStatus;
			}
		}
		//echo "<pre>";print_r($dateWiseStatus);die;
		$result = [];
		$result['dateWiseStatus'] = $dateWiseStatus;
		$result['absentCount'] = $absentCount;
		$result['halfLeaveCount'] = $halfLeaveCount;
		
		return $result;
	}
}

if (! function_exists('assignMonthWiseSalaryInfo')) {
	function assignMonthWiseSalaryInfo( $employeeId , $salaryMonth ){
		
		$month = date('m' , strtotime($salaryMonth) );
		$year = date('Y' , strtotime($salaryMonth) );
		
		$startDate = attendanceStartDate( $month , $year);
		$endDate = attendanceEndDate( $month , $year);
		
		
		$salaryInfoQuery = ReviseSalaryMaster::with(['assignSalaryInfo' , 'assignSalaryInfo.assignSalaryComponent' ])->where('i_employee_id' ,  $employeeId )->where('t_is_deleted' , 0 );
		$salaryInfoQuery->whereRaw("( ( dt_effective_date >= '".$startDate."' and  dt_effective_date <= '".$endDate."' ) or dt_effective_date < '".$startDate."' )");
		$salaryInfoQuery->orderBy('dt_effective_date' , 'desc');
		$salaryInfo = $salaryInfoQuery->first();
		return $salaryInfo;
	}
}

if (! function_exists('form16ReportInfo')) {
	function form16ReportInfo( $earningComponentDetails , $deductComponentDetails , $getAllEmployeeDetail , $salaryStartMonth , $salaryEndMonth ){
		$result = [];
		$displayData = [];
		$exportData = [];
		
		$totalLeave = 0;
	    $totalEarning = 0;
	    $totalDeduction = 0;
	    if(!empty($earningComponentDetails)){
	    	foreach($earningComponentDetails as $earningComponentDetail){
	    		$displayData['salary_'.$earningComponentDetail->i_id] = 0;
	    		$exportData[$earningComponentDetail->v_component_name] = 0;
			}
		}
		
		$exportData['total_earnings'] = 0;
	    				
	    if(!empty($deductComponentDetails)){
	    	foreach($deductComponentDetails as $deductComponentDetail){
	    		$displayData['salary_'.$deductComponentDetail->i_id] = 0;
	    		$exportData[$deductComponentDetail->v_component_name] = 0;
			}
		}
		/* var_dump($salaryStartMonth);
		var_dump($salaryEndMonth); */
		if( isset($getAllEmployeeDetail->generatedSalaryMaster) && (!empty($getAllEmployeeDetail->generatedSalaryMaster)) ){
			foreach($getAllEmployeeDetail->generatedSalaryMaster as $salaryMaster){
				if( $salaryMaster->t_is_salary_generated == 1 ){
					/* var_dump($salaryMaster->dt_salary_month);
					var_dump(( strtotime($salaryMaster->dt_salary_month) >= strtotime($salaryStartMonth)));
					var_dump(( strtotime($salaryMaster->dt_salary_month) <= strtotime($salaryEndMonth)));echo "<br><br>"; */
					if( ( strtotime($salaryMaster->dt_salary_month) >= strtotime($salaryStartMonth) ) && ( strtotime($salaryMaster->dt_salary_month) <= strtotime($salaryEndMonth) )  ){
						if( isset($salaryMaster->generatedSalaryInfo) && (!empty($salaryMaster->generatedSalaryInfo)) ){
							foreach($salaryMaster->generatedSalaryInfo as $salaryInfo ){
								
								
								if( isset($salaryInfo->d_paid_amount) && (!empty($salaryInfo->d_paid_amount)) ){
									if( isset($salaryInfo->generateSalaryComponent->e_salary_components_type) && (!empty($salaryInfo->generateSalaryComponent->e_salary_components_type)) ){
										switch($salaryInfo->generateSalaryComponent->e_salary_components_type){
											case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
												$totalEarning += $salaryInfo->d_paid_amount;
												$result[$salaryInfo->generateSalaryComponent->i_id] = $salaryInfo->d_paid_amount;
												$displayData['salary_'.$salaryInfo->generateSalaryComponent->i_id] += $salaryInfo->d_paid_amount;;
												$exportData[$salaryInfo->generateSalaryComponent->v_component_name] += $salaryInfo->d_paid_amount;;
												//$rowData[$earningComponentDetail->i_component_id] += $salaryInfo->d_paid_amount;
												break;
											case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
												$result[$salaryInfo->generateSalaryComponent->i_id] = $salaryInfo->d_paid_amount;
												$totalDeduction += $salaryInfo->d_paid_amount;
												$displayData['salary_'.$salaryInfo->generateSalaryComponent->i_id] += $salaryInfo->d_paid_amount;;
												$exportData[$salaryInfo->generateSalaryComponent->v_component_name] += $salaryInfo->d_paid_amount;;
												//$rowData[$earningComponentDetail->i_component_id] += $salaryInfo->d_paid_amount;
												break;
										}
									}
								
								}
							}
						}
					}
				}
			}
		}
	    //echo "<pre>";print_r($displayData);die;				
		
    	$displayData['d_total_earning'] = $totalEarning ;
    	$displayData['d_total_deduct'] = $totalDeduction;
    	$displayData['d_total_net_pay'] = ( $totalEarning - $totalDeduction );
    	
    	$exportData['total_earnings'] = $displayData['d_total_earning'] ;
    	$exportData['total_deductions'] = $displayData['d_total_deduct'] ;
    	$exportData['total_net_pay'] = $displayData['d_total_net_pay'] ;
    	
    	$response = [];
    	$response['display'] = $displayData;
    	$response['export'] = $exportData;
    	return $response;
    	
	}
}

if (! function_exists('statutoryBonusReportInfo')) {
	function statutoryBonusReportInfo( $allMonths , $getAllEmployeeDetail ){
		$result = [];
		$displayData = [];
		$exportData = [];

		$totalLeave = 0;
		$totalEarning = 0;
		$totalDeduction = 0;
		$totalPresentDays = 0;
    	$totalBasicSalary = 0;
    	if(!empty($allMonths)){
    		foreach($allMonths as $allMonth){
    			$displayData[$allMonth.'_present_day'] = 0;
    			$displayData[$allMonth.'_basic_salary'] = 0;
    			$exportData[convertDateFormat($allMonth,  'M-Y').'_present_day'] = 0;
    			$exportData[convertDateFormat($allMonth,  'M-Y').'_basic_salary'] = 0;
    		}
   		}
   		if( isset($getAllEmployeeDetail->generatedSalaryMaster) && (!empty($getAllEmployeeDetail->generatedSalaryMaster)) ){
   			foreach($getAllEmployeeDetail->generatedSalaryMaster as $salaryMaster){
   				if( $salaryMaster->t_is_salary_generated == 1 ){
   					$salaryMonth  = (isset($salaryMaster->dt_salary_month) ? $salaryMaster->dt_salary_month : '' );
   					if(!empty($salaryMonth)){
   						if( isset($salaryMaster->d_paid_days_count) && (!empty($salaryMaster->d_paid_days_count)) ){
   							$totalPresentDays += $salaryMaster->d_paid_days_count;
   							$displayData[$salaryMonth.'_present_day'] = $salaryMaster->d_paid_days_count;
   							$exportData[convertDateFormat($salaryMonth,  'M-Y').'_present_day'] = $salaryMaster->d_paid_days_count;
   						}
   						if( isset($salaryMaster->generatedSalaryInfo) && (!empty($salaryMaster->generatedSalaryInfo)) ){
   							foreach($salaryMaster->generatedSalaryInfo as $salaryInfo ){
   								if( isset($salaryInfo->d_paid_amount) && (!empty($salaryInfo->d_paid_amount)) ){
   									if( isset($salaryInfo->generateSalaryComponent->i_id) && (!empty($salaryInfo->generateSalaryComponent->i_id)) && ( $salaryInfo->generateSalaryComponent->i_id == config('constants.BASIC_SALARY_COMPONENT_ID') ) ){
   										$totalBasicSalary += $salaryInfo->d_paid_amount;
   										$displayData[$salaryMonth.'_basic_salary'] = $salaryInfo->d_paid_amount;
   										$exportData[convertDateFormat($salaryMonth,  'M-Y').'_basic_salary'] = $salaryInfo->d_paid_amount;
   									}
   								}
   							}
   						}
   					}
   				}
   			}
   		}    		
    				
		$displayData['d_total_present_days'] = $totalPresentDays ;
    	$displayData['d_total_basic_salary'] = $totalBasicSalary;
    	
    	$exportData['total_present'] = $displayData['d_total_present_days'];
    	$exportData['total_salary'] = $displayData['d_total_basic_salary'];
		 
		$response = [];
		$response['display'] = $displayData;
		$response['export'] = $exportData;
		return $response;
		 
	}
}
if (! function_exists('lastAllowedDate')) {
	function lastAllowedDate($employeeInfo){
		$result = null;
		$lastSalaryGeneratedMonth = ( isset($employeeInfo->latestGeneratedSalary->dt_salary_month) ? $employeeInfo->latestGeneratedSalary->dt_salary_month : null );
		
		if(!empty($lastSalaryGeneratedMonth)){
			$lastMonthEndDate = attendanceEndDate( date('m' , strtotime($lastSalaryGeneratedMonth)) , date('Y' , strtotime($lastSalaryGeneratedMonth)) );;
			$result = date("Y-m-d", strtotime("+1 day" , strtotime($lastMonthEndDate) ));
		}
		return $result;
	}
}
if (! function_exists('diffBetweenTimeOnlyIntoSecond')) {
	function diffBetweenTimeOnlyIntoSecond($startTime = null , $endTime = null  ){
		$startTime = date('Y-m-d') . ' ' . $startTime ;
		$endTime = date('Y-m-d') . ' ' . $endTime;
		
		$time_diff = 0;
		$diff =  abs(strtotime($startTime) - strtotime($endTime));
		//echo "diff = ".$diff;echo "<br><br>";
		//console.log("related_field_name = " + related_field_name );
		if( $startTime != "" && $startTime != null && $endTime != "" && $endTime != null  ){
		
			$timeStart = strtotime($startTime);
			$timeEnd = strtotime($endTime);
		
			//echo "timeStart  = ".$timeStart;echo "<br><br>";
			//echo "timeEnd  = ".$timeEnd;echo "<br><br>";
				
			if($timeStart > $timeEnd){
		
				$start_diff = (int)( strtotime( date('Y-m-d') . ' 23:59:59' ) - $timeStart );
				$end_diff =  (int) ( $timeEnd - strtotime ( date('Y-m-d') . ' 00:00:00') );
		
				//echo "start_diff  = ".$start_diff;echo "<br><br>";
				//echo "end_diff  = ".$end_diff;echo "<br><br>";
		
				$time_diff = ( $start_diff + $end_diff );
				//echo "time_diff  = ".$time_diff;echo "<br><br>";
				$diff = abs($time_diff);
				//echo "diff  = ".$diff;echo "<br><br>";
				//console.log("diff = " + diff );
				$seconds = $diff; //ignore any left over units smaller than a second
				$seconds = ( $seconds > 0 ? ( $seconds  + 60 ) : $seconds ) ;
			} else {
				$time_diff = $timeStart - $timeEnd;
				$diff = abs($time_diff);
				$seconds = $diff;
					
			}
		} else {
			$seconds = 0;
		}
		return $seconds;
	}
}
if (! function_exists('diffBetweenTime')) {
	function diffBetweenTime($startTime = null , $endTime = null  ){
		
		$seconds = diffBetweenTimeOnlyIntoSecond($startTime, $endTime);
		$result = convertSecondIntoHourMinute($seconds);
		return $result;
		
		return $timeDiff;
	}
}

if (! function_exists('salaryIncrementReportInfo')) {
	function salaryIncrementReportInfo( $incrementHeaders , $recordDetail ){
		$result = [];
		$displayData = [];
		$exportData = [];

		if(!empty($incrementHeaders)){
			foreach($incrementHeaders as $incrementHeader){
				$displayData[$incrementHeader] = 0; 
				$exportData[$incrementHeader] = 0;
			}
		}
		
		if( isset($recordDetail->employeeReviseSalary) && (!empty($recordDetail->employeeReviseSalary)) ){
			foreach($recordDetail->employeeReviseSalary as $salaryInfo ){
				$salaryMonth = date('M' ,  strtotime($salaryInfo->dt_effective_date ) );
				$effectativeDate = null;
				if( in_array( strtolower($salaryMonth) , [ 'jan' , 'feb' , 'march' , 'apr' ] ) ){
					$effectativeDate = date('Y-m-d' , strtotime("first day of january this year" , strtotime( $salaryInfo->dt_effective_date ) ));
				}
				
				if( in_array( strtolower($salaryMonth) , [ 'may' , 'jun' , 'jul'  ] ) ){
					$effectativeDate = date('Y-m-d' , strtotime("first day of july this year" , strtotime( $salaryInfo->dt_effective_date ) ));
				}
				
				if( in_array( strtolower($salaryMonth) , [ 'aug' , 'sep' , 'oct' , 'nov' , 'dec'  ] ) ){
					$effectativeDate = date('Y-m-d' , strtotime("first day of january next year" , strtotime( $salaryInfo->dt_effective_date ) ));
				}
				//echo "date  = ".$salaryInfo->dt_effective_date;echo "<br><br>";
				//echo "ssss = ".$effectativeDate;echo "<br><br>";
				if(!empty($effectativeDate)){
					if( isset($displayData[$effectativeDate]) ){
						$displayData[$effectativeDate] = (!empty($salaryInfo->d_total_earning) ? $salaryInfo->d_total_earning : 0 );
						$exportData[$effectativeDate] = (!empty($salaryInfo->d_total_earning) ? $salaryInfo->d_total_earning : 0 );
					}
				}
			}
		}
		
		$response = [];
		$response['display'] = $displayData;
		$response['export'] = $exportData;
		return $response;
			
	}
}

if (! function_exists('salaryIncrementReportHeader')) {
	function salaryIncrementReportHeader( $inputYear = null ){
		$result = [];
		$selectedYear = (!empty($inputYear) ?  $inputYear :  date('Y'));
		
		$result[] =  date('Y-m-d' , strtotime("first day of july next year" , strtotime(date($selectedYear . '-01-01')) ));
		$result[] =  date('Y-m-d' , strtotime("first day of january next year" , strtotime(date($selectedYear . '-01-01')) ));
		
		for($i= 0 ; $i <= config('constants.SALARY_INCREMENT_RECORD_DISPLAY_YEAR_COUNT') ; $i++ ){
			$result[] =  date('Y-m-d' , strtotime("first day of july this year" , strtotime("-" . $i . " year" , strtotime(date($selectedYear . '-01-01'))) ));
			$result[] =  date('Y-m-d' , strtotime("first day of january this year" , strtotime("-" . $i . " year" , strtotime(date($selectedYear . '-01-01')) ) ));
		}
		
		$result = array_reverse($result);
		return $result;
			
	}
}
if (!function_exists('employeeStatusDropdownValue')){
	function employeeStatusDropdownValue(){
		$result = [];
		$result[config('constants.WORKING_EMPLOYMENT_STATUS')] = trans('messages.working');
		$result[config('constants.PROBATION_EMPLOYMENT_STATUS')] = trans('messages.in-probation');
		$result[config('constants.CONFIRMED_EMPLOYMENT_STATUS')] = trans('messages.confirmed');
		$result[config('constants.NOTICE_PERIOD_EMPLOYMENT_STATUS')] = trans('messages.notice-period');
		$result[config('constants.RELIEVED_EMPLOYMENT_STATUS')] = trans('messages.relieved');
		return $result;
	}
}
if (!function_exists('employeeStatusFilter')){
	function employeeStatusFilter( $selecteadValue = null , $allPermissionId = null ,  $fieldName  = 'search_employment_status' , $additionalClassName = null ){
		$empStatusDropdownDetails = employeeStatusDropdownValue();
		
		$result = '';
		$result .= '<div class="form-group">';
		$result .= '<label class="control-label" for="search_employment_status">'.trans("messages.employment-status").'</label>';
		$result .= '<select name="'.$fieldName.'" class="form-control select2 '.$additionalClassName.' " data-all-permission-id="'.$allPermissionId.'" onchange="getStatusWiseEmployeeDetails(this);">';
		$result .= '<option value="">'.trans("messages.select").'</option>';
		
		foreach ($empStatusDropdownDetails as $key => $value){
			$selected = "";
			if( !empty($selecteadValue) && ( $selecteadValue ==  $key ) ){
				$selected = "selected='selected'";
			}
			$result .= '<option value="'.$key.'" '.$selected.' >'.$value.'</option>';
		}
		
		$result .= '</select>';
		$result .= '</div>';
		
		return $result;
	}
}
if (!function_exists('statusWiseEmployeeList')){
	function statusWiseEmployeeList( $fieldName  = 'search_employee_name' , $recordDetails = [] , $selectedUserId = null , $additionalClassName = null ){
		$html = '';
		$html .= '<div class="form-group">';
		$html .= '<label class="control-label" for="'.$fieldName.'">'.trans("messages.employee-name-code").'</label>';
		$html .= '<select name="'.$fieldName.'" class="form-control select2 status-wise-emp-div '.$additionalClassName.' " onchange="filterData();">';
		$html .= '<option value="">'.trans("messages.select").'</option>';
		
		if(!empty($recordDetails)){
			foreach($recordDetails as $recordDetail){
				$selected = "";
				if( (!empty($selectedUserId) && ( $selectedUserId == $recordDetail->i_id )) ){
					$selected = "selected='selected'";
				}
				$encodeEmployeeId = Wild_tiger::encode($recordDetail->i_id);
				$employeeText = $recordDetail->v_employee_full_name . ( ( isset($recordDetail->v_employee_code )  && (!empty($recordDetail->v_employee_code ))) ?  ' ('.$recordDetail->v_employee_code . ')' : '' );
				$html .= '<option value="'.$encodeEmployeeId.'" '.$selected.' >'.$employeeText.'</option>';
			}
		}
		
		$html .= '</select>';
		$html .= '</div>';
		$html .= '';
		return $html;
	}
}

if (!function_exists('dayWiseSalaryHeadAmount')){
	function dayWiseSalaryHeadAmount($headValue , $presentDay ){
		$result = round( ( ( $headValue * $presentDay ) / config('constants.SALARY_COUNT_DAYS') ) , 0 );
		return $result;
	}
}

if (!function_exists('workingHoursByTotalAndBreakTime')){
	function workingHoursByTotalAndBreakTime($recordDetail = []){
		$workingHours = '';
		if( (!empty($recordDetail->t_start_time)) && (!empty($recordDetail->t_end_time)) ){
			$workingHours = convertSecondIntoHourMinute( diffBetweenTimeOnlyIntoSecond( $recordDetail->t_start_time , $recordDetail->t_end_time ) -  ( (isset($recordDetail->t_total_break_time) && (!empty($recordDetail->t_total_break_time))) ? ( strtotime($recordDetail->t_total_break_time) - strtotime('TODAY') ) : 0 ) );
		}
		return $workingHours;
	}
}

if (!function_exists('storeChartColor')){
	function storeChartColor($value = null){
		$result = null;
		if(!empty($value)){
			$result = str_replace("#", "", $value);
		}
		return $result;
	}
}

if (! function_exists('detectDevice')) {

	function detectDevice(){
		$useragent= (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '' ) ;
		$device = '';
		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
			$device = 'mobile';
		} else {
			$device = 'desktop';
		}
		return $device;
	}
}

if (! function_exists('getPFValue')) {

	function getPFValue($presentDay, $assignSalaryDetails = []){
		
		//echo "<pre>sss";print_r($assignSalaryDetails);die;
		$pfValue = 0;
		$totalEarningAmount = 0;
		$totalEarningForPFAmount = 0;
		$hraAmount = 0;
		if( !empty($assignSalaryDetails) ){
			foreach( $assignSalaryDetails->assignSalaryInfo as $assignSalaryInfo ){
				if( isset($assignSalaryInfo->assignSalaryComponent->e_salary_components_type) && ( $assignSalaryInfo->assignSalaryComponent->e_salary_components_type == config('constants.SALARY_COMPONENT_TYPE_EARNING') ) ){
					if( isset($assignSalaryInfo->d_amount) && (!empty($assignSalaryInfo->d_amount)) ){
						if( $assignSalaryInfo->assignSalaryComponent->i_id == config('constants.HRA_SALARY_COMPONENT_ID') ){
							$hraAmount = $assignSalaryInfo->d_amount;
							$totalEarningAmount += $assignSalaryInfo->d_amount;
							$totalEarningForPFAmount += $assignSalaryInfo->d_amount;
						} else {
							if( isset($assignSalaryInfo->assignSalaryComponent->e_consider_for_pf_calculation) && ( $assignSalaryInfo->assignSalaryComponent->e_consider_for_pf_calculation == config('constants.SELECTION_YES') )  ){
								$totalEarningForPFAmount += $assignSalaryInfo->d_amount;
							} 
							$totalEarningAmount += $assignSalaryInfo->d_amount;
						}
					}
				}
			}
		}
		$totalEarningAmount = dayWiseSalaryHeadAmount( $totalEarningAmount ,  $presentDay );
		$totalEarningForPFAmount = dayWiseSalaryHeadAmount( $totalEarningForPFAmount ,  $presentDay );
		$hraAmount = dayWiseSalaryHeadAmount( $hraAmount ,  $presentDay );
		
		//var_dump($totalEarningForPFAmount);
		//var_dump($hraAmount);
		
		$calcualteAmount = ( $totalEarningForPFAmount -  $hraAmount );
		//var_dump($calcualteAmount);
		//echo $calcualteAmount;die;
		$pfValue = (!empty($calcualteAmount) ? round( ( $calcualteAmount * 0.12 ) , 2 ) : 0 );
		
		///var_dump($pfValue);
		if( $pfValue > config('constants.MAXIMUM_ALLOWED_PF_AMOUNT') ){
			$pfValue = config('constants.MAXIMUM_ALLOWED_PF_AMOUNT');
		} else {
			$pfValue = round($pfValue,0);
		}
		return $pfValue;
		
	}
}


if (! function_exists('displayOnTime')) {

	function displayOnTime($recordDetail){
		$result = [];
		$result['arrivalInfo'] = "";
		$result['departureInfo'] = "";
		$arrivalStatus = "";
		$departureStatus = "";
		if( isset($recordDetail->t_start_time) && (isset($recordDetail->t_original_start_time)) && (!empty($recordDetail->t_start_time)) && (!empty($recordDetail->t_original_start_time)) ){
			$differnce =  strtotime($recordDetail->t_start_time) - strtotime($recordDetail->t_original_start_time);
			if( $differnce < 0 ){
				$arrivalStatus = trans('messages.early');
			} else if( $differnce <= config('constants.ON_TIME_BUFFER_DURATION_INTO_SEC') ){
				$arrivalStatus = trans('messages.on-time');
			} else {
				$arrivalStatus = trans('messages.late');
			}
		}
		
		if( isset($recordDetail->t_end_time) && (isset($recordDetail->t_original_end_time)) && (!empty($recordDetail->t_end_time)) && (!empty($recordDetail->t_original_end_time)) ){
			$differnce =  strtotime($recordDetail->t_end_time) - strtotime($recordDetail->t_original_end_time);
			
			
			if( $differnce > 0 ){
				$departureStatus = trans('messages.late');
			} else {
				if( $differnce < 0 ){
					$departureStatus = trans('messages.early');
				} else {
					$departureStatus = trans('messages.on-time');
				}
			}
		}
		$result['arrivalInfo'] = $arrivalStatus;
		$result['departureInfo'] = $departureStatus;
		//echo "<pre>";print_r($result);
		return $result;
	}
}

function NumberintoWords(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
	$hundred = null;
	$digitsLength = strlen($no);
	$i = 0;
	$str = array();
	$words = array(0 => '', 1 => 'one', 2 => 'two',
			3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
			7 => 'seven', 8 => 'eight', 9 => 'nine',
			10 => 'ten', 11 => 'eleven', 12 => 'twelve',
			13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
			16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
			19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
			40 => 'forty', 50 => 'fifty', 60 => 'sixty',
			70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
	$digits = array('', 'hundred','thousand','lakh', 'crore');
	while( $i < $digitsLength ) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += $divider == 10 ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str [] = ($number < 21) ? ( isset($words[$number]) ? $words[$number] : '' )  .' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
		} else $str[] = null;
	}
	$rupees = implode('', array_reverse($str));
	$paise = ($decimal > 0) ?   ($words[$decimal / 10] . " " . $words[$decimal % 10])  : '';
	
	if(!empty($paise)){
		$rupees .=  ' Point '. $paise . ' Rupees Only.';
	} else {
		$rupees .= ' Rupees Only.';
	}
	return ucwords( $rupees);
}



if (!function_exists('getUploadAsset')) {
	function getUploadAsset($asset = null)
	{
		$data = '';

		if (!empty($asset) && file_exists(config('constants.FILE_STORAGE_PATH') . config('constants.UPLOAD_FOLDER') . $asset)) {
			$data = config('constants.FILE_STORAGE_PATH_URL') . config('constants.UPLOAD_FOLDER') . $asset;
		}

		return $data;
	}
}

if (!function_exists('roleList')){
	function roleList(){
		$result = [];
		$result[config('constants.ROLE_USER')] = trans('messages.user');
		$result[config('constants.ROLE_HR_TEAM')] = trans('messages.hr-team');
		//$result[config('constants.ROLE_ADMIN')] = trans('messages.admin');
		
		return $result;
	}
}

if (!function_exists('arrivalDepartureList')){
	function arrivalDepartureList(){
		$result = [];
		$result[config('constants.EARLY_STATUS')] = trans('messages.early');
		$result[config('constants.ON_TIME_STATUS')] = trans('messages.on-time');
		$result[config('constants.LATE_STATUS')] = trans('messages.late');
		//$result[config('constants.ROLE_ADMIN')] = trans('messages.admin');

		return $result;
	}
}

if (!function_exists('removeSession')){
function removeSession($requestUserId){

	$allSessionFiles = glob(storage_path( 'framework/sessions/*'));
	//Log::info('requested_user_id = ' . $requestUserId );
	//Log::info(print_r($allSessionFiles , true));
	if(!empty($allSessionFiles)){
		foreach($allSessionFiles as $allSessionFile){
			$fileData = file_get_contents($allSessionFile);
			$fileArray = unserialize($fileData);
			//Log::info( 'file_name = ' . basename($allSessionFile) );
			//Log::info(print_r($fileArray , true));
			//Log::info('loop_id = ' . $requestUserId );
			//Log::info('session_id = ' . $fileArray['user_id'] );
			//Log::info(print_r( session()->all() , true ) );
			if( (!empty($fileArray))  && (isset($fileArray['user_id'])) && ( $requestUserId ==  $fileArray['user_id'] ) ){
				//Log::info('session file = ' . $allSessionFile );
				unlink($allSessionFile);
				//break;
			}
		}
	}
	return true;
}
}

if (! function_exists('generateOTP')) {
	function generateOTP($length = 6) {
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}
}
?>