<?php
namespace App\Helpers\Twt;

use Illuminate\Support\Facades\Session;
use Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;



class Wild_tiger{
	
	// private key for encryption
	public static $key = 'HRMS_ACORN'; //Config::get('constants.ENCRYPTION_KEY');
	
	/**
	 * This function used to display message
	 *
	 * @param string $type
	 *            'message type'
	 * @param string $message
	 *            text'
	 */
	public static function setFlashMessage($type, $message)
	{
		
		$output = '<div class="alert alert-' . $type . ' alert-dismissible text-center" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>' . ucwords( $message )  . '</div>';
		Session::flash('message', $output );
	}
	
	public static function readMessage()
	{
		if (Session::has('message')){
			echo Session::get('message');
		}
		
	}
	
	public static function createdReadableLink($link)
	{
		$link =str_replace(" ", "_", $link);
		return $link;
	}
	
	public static function sendMailSMTP( $data )
	{
		$mailResult = false;
		try{
			$result = Mail::send($data['viewName'],$data['mailData'], function ( $message ) use ($data) {
				// Prefer Laravel's mail.from, fallback to constants
				$fromAddress = config('mail.from.address') ?: config('constants.SEND_EMAIL_USER');
				$fromName    = config('mail.from.name') ?: config('constants.SEND_EMAIL_TITLE');
				if (!empty($fromAddress)) {
					$message->from($fromAddress, $fromName);
				}

				// Recipient: use provided 'to' first, then fallback
				$to = $data['to'] ?? null;
				if (empty($to)) {
					$to = config('constants.CONTACT_RECEIVE_EMAIL');
				}
				if (empty($to)) {
					throw new \InvalidArgumentException('Recipient email address is missing');
				}
				$message->to($to);

				// Optional cc/bcc
				if (!empty($data['cc'])) { $message->cc($data['cc']); }
				if (!empty($data['bcc'])) { $message->bcc($data['bcc']); }

				$message->subject($data['subject']);
			});
			// Basic diagnostics
			try {
				Log::info('sendMailSMTP dispatched', [
					'from' => (config('mail.from.address') ?: config('constants.SEND_EMAIL_USER')),
					'to'   => ($data['to'] ?? config('constants.CONTACT_RECEIVE_EMAIL')),
					'subject' => ($data['subject'] ?? ''),
				]);
			} catch (\Throwable $logEx) {
				// ignore logging failures
			}
			$mailResult = true;
		}catch(\Exception $e){
			$mailResult = false;
			$result['msg'] = $e->getMessage();
			try { Log::error('sendMailSMTP failed: '.$e->getMessage()); } catch (\Throwable $logEx) {}
		}
		//var_dump($mailResult);
		if( $mailResult != false ){
			$result['status'] = true;
		}else{
			$result['status'] = false;
		}
		//$result['status'] = true;
		return $result;
	}
	
	
	/**
	 * This function used to encode input text
	 *
	 * encode input value
	 *
	 * @param string $plainText
	 *            value'
	 * @return string
	 */
	public static function encode($plainText)
	{  	
		if (empty($plainText)) {
			return '';
		}
		$ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plainText, $cipher, self::$key, $options = OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, self::$key , $as_binary = true);
		$ciphertext = self::safebase64_encode($iv . $hmac . $ciphertext_raw);
		return $ciphertext;
	}
	
	/**
	 * This function used to decode input text
	 *
	 * @param string $plainText
	 *            input text'
	 * @return string
	 */
	public static function decode($plainText)
	{
		if (empty($plainText)) {
			return '';
		}
		$c = self::safebase64_decode($plainText);
		$ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
		$minLength = $ivlen + 32; // IV + HMAC

		// Validate that we have enough data
		if (strlen($c) < $minLength) {
			Log::error('Wild_tiger decode: Insufficient data length', [
				'input_length' => strlen($plainText),
				'decoded_length' => strlen($c),
				'minimum_required' => $minLength,
				'input_text' => substr($plainText, 0, 100) . (strlen($plainText) > 100 ? '...' : '')
			]);
			return '';
		}

		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len = 32);
		$ciphertext_raw = substr($c, $ivlen + $sha2len);
		$original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, self::$key, $options = OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, self::$key , $as_binary = true);
		if (hash_equals($hmac, $calcmac)) // PHP 5.6+ timing attack safe comparison
		{
			return $original_plaintext . "\n";
		}

		// Log if decryption failed but data length was correct
		Log::warning('Wild_tiger decode: HMAC verification failed', [
			'input_length' => strlen($plainText),
			'decoded_length' => strlen($c)
		]);

		return '';
	}
	
	/**
	 * safe64_encode value
	 *
	 * @param string $val
	 * @return string
	 */
	public static function safebase64_encode($val)
	{
		// return strtr ( base64_encode ( $val ), '+/=', '-_ ' );
		return rtrim(strtr(base64_encode($val), '+/', '-_'), '=');
	}
	
	/**
	 * safe64_decode value
	 *
	 * @param string $val
	 * @return string
	 */
	public static function safebase64_decode($val)
	{
		// return base64_decode ( strtr ( $val, '-_ ', '+/=' ) );
		return base64_decode(str_pad(strtr($val, '-_', '+/'), strlen($val) % 4, '=', STR_PAD_RIGHT));
	}
	
	public static function curlRequest($url, $postData = []  , $headerData = [] )
	{
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		if(!empty($postData)){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}
		
	
		// in real life you should use something like:
		// curl_setopt($ch, CURLOPT_POSTFIELDS,
		// http_build_query(array('postvar1' => 'value1')));
	
		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		if(!empty($headerData)){
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headerData );
			curl_setopt( $ch,CURLOPT_USERPWD, config('constants.MATRIX_API_USERNAME') . ":"  .config('constants.MATRIX_API_PASSWORD'));
		}
	
		$result = [];
		$result['status'] = false;
		$curlResponse = curl_exec($ch);
		if ($curlResponse === false) {
			// echo 'Curl error: ' . curl_error($ch);
			$server_output = false;
			$result['status'] = false;
			$result['msg'] = curl_error($ch);
		} else {
	
			$server_output = $curlResponse;
	
			$result['status'] = true;
			$result['msg'] = $server_output;
		}
	
		curl_close($ch);
	
		return $result;
	}
	
	public static function curlGetRequest($url, $headerData = [] )
	{
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData );
		
		//echo '<pre>';print_r($headerData);
		
		if(!empty($headerData)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData );
		}
		
		
		
	
		// in real life you should use something like:
		// curl_setopt($ch, CURLOPT_POSTFIELDS,
		// http_build_query(array('postvar1' => 'value1')));
	
		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
		$result = [];
		$result['status'] = false;
		$curlResponse = curl_exec($ch);
		
		if ($curlResponse === false) {
			// echo 'Curl error: ' . curl_error($ch);
			$server_output = false;
			$result['status'] = false;
			$result['msg'] = curl_error($ch);
		} else {
	
			$server_output = $curlResponse;
			
			$result['status'] = true;
			$result['msg'] = $server_output;
		}
	
		curl_close($ch);
	
		return $result;
	}
	
	
	public static function uriSpaceReplace($name){
		
		$name = str_replace("-", "_", $name);
		$cipherText  = strtolower(str_replace(" ", "-", $name ) ).'.html';
		$URI_Encoded = urlencode($cipherText);
		return $URI_Encoded;
	}
	
	public static function uriDashReplace($name){

		//remove .html
		$removeHTML = substr($name, 0, -5);

		//replace Dash(-) with white Space
		$plainText = strtolower(str_replace("-", " ", $removeHTML ) );

		$plainText = str_replace("_", "-", $plainText);

		$URI_Decoded = urldecode($plainText);

		return $URI_Decoded;
	}
	
	
	
	
	public static function enumText($value) {
		$result = "";
		if(!empty($value)){
			$result =  ucwords(str_replace("_"," ", $value));
		}



		return $result;
	}
	
	
	public static  function getfieldValue($array , $fieldName , $columnName ){
		
		$SearchKey = array_search($fieldName , array_column($array ,$columnName ));
		
		$finalData = [];
		
		if( strlen($SearchKey) > 0 ){
			
			$fieldData = $array[$SearchKey]['v_field_value'];
			
			$finalData = (!empty($fieldData) ? json_decode($fieldData,true) : [] );
		}
		
		return $finalData;
		
	}
	
	public static  function dbDate($inputDate , $inputFormat = 'd/m/Y'){
	
		$dbDate = null;
		
		if(!empty($inputDate)){
			
			$inputDate = str_replace("/", "-", $inputDate);
			
			$inputFormat = (!empty($inputFormat) ? $inputFormat : config('constants.MONTH_DATE_FORMAT') );
			
			$dbDate = \DateTime::createFromFormat($inputFormat, $inputDate)->format('Y-m-d');
		
		}
	
		return $dbDate;
	
	}
	
	public static  function leadTentativeValue($inputDate ){
	
		$dbDate = null;
	
		if(!empty($inputDate)){
				
			$inputDate = str_replace("/", "-", $inputDate);
				
			$dbDate = \DateTime::createFromFormat('Y-m-d', $inputDate)->format('M-y');
	
		}
	
		return $dbDate;
	
	}
	
	public static  function dbDateTime($inputDate){
	
		$dbDate = null;
	
		if(!empty($inputDate)){
				
			$dbDate = date('Y-m-d H:i:s' , strtotime($inputDate));
				
		}
	
		return $dbDate;
	
	}
	
	public static  function clientDate($inputDate , $format = 'd-m-Y' ){
	
		$dbDate = null;
	
		if(!empty($inputDate)){
				
			$dbDate = date($format , strtotime($inputDate));
				
		}
	
		return $dbDate;
	
	}
	
	public static  function clientDateTime($inputDate){
	
		$dbDate = null;
	
		if(!empty($inputDate)){
				
			$dbDate = date('d-m-Y h:i A' , strtotime($inputDate));
				
		}
	
		return $dbDate;
	
	}
	
	public static function twt_array_sort($array, $on, $order=SORT_ASC){
	
		$new_array = array();
		$sortable_array = array();
	
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
	
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
	
			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}
	
		return $new_array;
	}
	
	public static function  decimalAmount($value){
		
		$result = "";
		if(!empty($value)){
			$value = round($value,2);
			$result = number_format(  $value , 0 , "." , "," );
			//$fmt = new \NumberFormatter($locale = 'en_IN', NumberFormatter::DECIMAL);
			//$result = $fmt->format($value);
		} else {
			$result = 0.00;
		}
		
		
		
		return $result;
		
	}
	
	public static function getAcademicYear(){
	
		$date = date('Y-m-d');
		$time = strtotime($date);
		$year = date('Y', $time);
		if(date('n', $time) > 3){
			$ayear = ($year).'-'.($year + 1);
		}
		else{
			$ayear = ($year - 1).'-'.$year;
		}
	
		return $ayear;
	}
	
	
	
	
	
	
	public static function objectToArray($value){
		
		$result = json_decode(json_encode($value) , true);
		
		return $result;
		
	}
	public function getAllDateOfMonth($month , $year = null ){
	
		$year = (!empty($year) ? $year : date('Y'));;
	
		$start_date = "01-".$month."-".$year;
		$start_time = strtotime($start_date);
	
		$end_time = strtotime("+1 month", $start_time);
		$dates = [];
		for($i=$start_time; $i<$end_time; $i+=86400)
		{
			$dates[] = date('Y-m-d', $i);
		}
	
		return $dates;
	}
	
	public function getAllDateOfSalaryMonth($month , $year = null ){
	
		$format = 'Y-m-d';
		
		$year = (!empty($year) ? $year : date('Y'));;
	
		$startDate = attendanceStartDate($month, $year);
		$endDate = attendanceEndDate($month, $year);
		
		$allDates = getDatesFromRange($startDate,$endDate);
		
		// Return the array elements
		return $allDates;
	}
	
	public function getWeekOfMonth($salaryAddDates ){
		$sundayDates = [];
		$excludeDays = [];
		$excludeDays = [ 'first' , 'second' , 'third' ,  'fourth' ];
		
		if(!empty($salaryAddDates)){
			foreach($salaryAddDates as $salaryAddDate){
				$newDate = new \DateTime($salaryAddDate);
				if(($newDate->format("D") == "Sun") || ($newDate->format("D") == "Sat")){
	    			$sundayDates [] =  $newDate->format("Y-m-d");
	    			if( strtotime($salaryAddDate) >= strtotime(config('constants.WEEK_OFF_START_DATE')) ){
	    				foreach($excludeDays as $excludeDay){
	    					if( strtotime( $excludeDay .' friday of '.date('F' , strtotime($salaryAddDate)).' '.date('Y' , strtotime($salaryAddDate) )) == strtotime($salaryAddDate)) //Check that the day is Sunday here
	    					{
	    						$sundayDates [] =  $newDate->format("Y-m-d");
	    					}
	    				}
	    			}
	    		}
	    		
			}
		}
	
		return $sundayDates;
	}
	public static function DefaultExcelRow(){
		$data = [ 'A' , 'B' , 'C' , 'D' , 'E' ,'F' , 'G' , 'H' ,'I' , 'J' , 'K' , 'L' , 'M' , 'N' , 'O' , 'P' , 'Q' , 'R' , 'S' , 'T' , 'U' , 'V' , 'W' , 'X' , 'Y' , 'Z','AA' , 'AB' , 'AC' , 'AD','AE' , 'AF' , 'AG' , 'AH' , 'AI' , 'AJ' , 'AK' , 'AL' , 'AM' , 'AN' , 'AO' , 'AP' , 'AQ' , 'AR' , 'AS' , 'AT' , 'AU' , 'AV' , 'AW' , 'AX' , 'AY' , 'AZ' , 'BA' , 'BB' , 'BC' , 'BD' , 'BE' , 'BF' , 'BG' , 'BH' , 'BI' , 'BJ' , 'BK' , 'BL' , 'BM' , 'BN' , 'BO' , 'BP' ];
		return $data;
	}
}

