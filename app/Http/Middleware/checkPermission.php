<?php

namespace App\Http\Middleware;

use App\Models\PermissionGroup;
use App\Models\PermissionMaster;
use App\Models\RolePermission;
use Closure;
use Illuminate\Http\Request;

class checkPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$module)
    {
        $result = false;
       
        if (session()->has('isLoggedIn') && session()->get('isLoggedIn') != false && session()->has('user_id') && !empty(session()->get('user_id'))) {
            $result = checkPermission($module);
        }
       
        if( in_array( session()->get('role') , [ config('constants.ROLE_ADMIN') ] ) ){
        	$result = true;
        }
       
        if(  isset($module[0]) && ( in_array( $module[0] , [ 'edit_village' , 'edit_city' , 'add_village' , 'add_city' ]  ) ) ){
        	if( session()->has('user_permission') && ( in_array(config('permission_constants.ALL_EMPLOYEE_LIST'), session()->get('user_permission')  ) ) && ( in_array(config('permission_constants.EDIT_EMPLOYEE_PERMISSION'), session()->get('user_permission')  ) ) ){
        		$result = true;
        	}
        }
        
        if ($result != false) {
            return $next($request);
        } else {
            return redirect('access-denied');
        }
    }
}
