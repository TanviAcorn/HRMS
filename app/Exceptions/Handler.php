<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\Error_exception_info_model;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\SettingsModel;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
    	/*
        $this->reportable(function (Throwable $e) {
            //
        });
        */
        
        	if( config('constants.SHOW_ERROR_EXCEPTION') != false ){
        		$this->reportable(function (Throwable $e) {
        			//
        		});
        	} else {
        		//NotFoundHttpException
        		$this->renderable(function (CustomTWTException $e, $request) {
        			if ($e instanceof NotFoundHttpException) {
        					
        			}
        		});
        	}
    }
    
    public function render($request, Throwable $e)
    {
    	if( config('constants.SHOW_ERROR_EXCEPTION') != false ){
    		return parent::render($request, $e);
    	} else {
    		
    		if ( $e instanceof  NotFoundHttpException  )
    		{
    			$data['settingsInfo'] = SettingsModel::where('t_is_deleted' , 0 )->first();
    			$data['pageTitle'] = trans ( 'messages.page-not-found');
    			 
    			return response()->view('errors.404', $data );
    		} else {
    			 
    			$this->model = new Error_exception_info_model();
    
    			$errorInfo = [];
    			$errorInfo['v_server_info'] = ( isset($_SERVER) ? json_encode($_SERVER) : null );
    			$errorInfo['v_session_info'] = ( isset($_SESSION) ? json_encode(session()->all()) : null );
    			$errorInfo['v_request_info'] = ( isset($request) ? json_encode($request->all()) : null );
    			$errorInfo['v_error_info'] = ( isset($e) ? json_encode($e) : null );
    			$errorInfo['v_error_message'] = ( isset($e) ? $e->getMessage() : null );
    
    			if( !empty($e->getFile()) && !empty($e->getLine()) && (!empty($e->getMessage())) ){
    				$errorInfo['v_error_message'] = $e->getMessage() . ' on Line no '.$e->getLine() . ' In ' .$e->getFile();
    			}
    
    			
    			if(!empty(array_filter($errorInfo))){
    				$this->model->insertTableData(config('constants.ERROR_EXCEPTION_INFO_TABLE'),$errorInfo);
    			}
    			
    			return response()->view('errors.exception-page', []);
    		}
    
    
    	}
    	 
    }
}
