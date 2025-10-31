<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Request;
use App\BaseModel;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	public $attributes;
    public function handle($request, Closure $next)
    {
       if( ( Session::has('isLoggedIn') ) && ( Session::get('isLoggedIn') != false ) ){
    	if( Session::get('site_title') == config('constants.SITE_TITLE')  ){
    			$request->loggedUserId = ( Session::has('user_id') ? Session::get('user_id') : 0 ) ;
    			
    			if( session()->get('role') == config('constants.ROLE_USER') ){
    				$getLoginUserInfo = BaseModel::getSingleRecordById( config('constants.EMPLOYEE_MASTER_TABLE') , [ 'i_id' ] , [ 'i_leader_id' => session()->get('user_employee_id') , 'e_employment_status != '  => config('constants.RELIEVED_EMPLOYMENT_STATUS')  ]  );
    				if(!empty($getLoginUserInfo)){
    					session()->put('is_supervisor' , true);
    				}
    			}
    			
    			if( in_array(  session()->get('role') , [ config('constants.ROLE_ADMIN') , config('constants.ROLE_HR_TEAM') ] ) ){
    				session()->put('is_supervisor' , true);
    			}
    			
    			return $next($request);
    		}
    	}
    	
    	Session::put('url.intended',Request::url());
    	return redirect('login');
    }
}
