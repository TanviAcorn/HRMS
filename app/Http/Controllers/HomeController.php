<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BaseModel;
use Mpdf;


class HomeController extends Controller
{
	public function slip()
	{
		$fontdata = [
			'poppins-regular' => [
				'R' => 'Poppins-Regular.ttf',
			],
		];

		$fontdata = [
			'poppins-medium' => [
				'R' => 'Poppins-Medium.ttf',
			],
		];
		
		$fontdata = [
			'poppins-bold' => [
				'R' => 'Poppins-Bold.ttf',
			],
		];

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
		$data = [];
		$html = view('pdf/sample-pdf')->with($data);

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

		$mpdf->SetWatermarkImage(
			('images/icon1.png'),
			0.1,
			'',
			//    array(160,10)
		);
		$mpdf->autoPageBreak = true;


		$header  = '';
		$header .= '<div class="main-page-border-outer vh100" style="border: 3px solid #000; padding: 100px;height:100%;width:100%">';
		$header .= '<div class="px-20" style="padding:3px;padding-left: 20px; padding-right: 20px;">';
		$footer = '';
		$footer = '<table cellpadding="20" cellspacing="0" style="width:100%; font-family: Poppins, sans-serif; vertical-align:top;">
			<tbody>
			
			<tr>
					<td style="text-align: right;"><strong>For, ACORN UNIVERSAL CONSULTANCY LLP</strong></td>
					<br><br>
				</tr>
				<tr>
					<td style="text-align: right;">Authorised Signatory</td>
					<br><br>s
				</tr>
				<tr>
					<td style="text-align: center;">This is computer generated payslip signature not required.</td>
					<br>
				</tr>
			</tbody>
		</table></div>';
		$footer .= '</div>';
		$footer .= '</div>';

		$mpdf->SetHTMLHeader($header);
		$mpdf->SetHTMLFooter($footer);
		// echo $html;die;
		// $mpdf->setFooter('{PAGENO}');
		// $css = FCPATH .'assets/css/pdf.css';
		// $stylesheet = file_get_contents( $css ); // external css
		// $mpdf->WriteHTML($stylesheet,1);

		$mpdf->showWatermarkImage = true;
		$mpdf->WriteHTML($html, 2);
		$mpdf->Output();
	}
    
    public function sampleForm(){
    	$data['pageTitle'] = trans ( 'messages.form');
    	return view( 'form/form')->with($data);
    }
    
    public function checkDbConnection(){
    	$this->dbObject = new BaseModel();
    	$userDetails =  $this->dbObject->selectData('users' , [ 'password' ]);
    	echo "<pre>";print_r($userDetails);die;
    }

	public function dashboard(){
        $data['pageTitle'] = trans('messages.dashboard');
        return view( 'admin/design/dashboard')->with($data);
    }
	public function login(){
        $data['pageTitle'] = trans('messages.login');
        return view( 'admin/login')->with($data);
    }
	public function changepassword(){
        $data['pageTitle'] = trans('messages.change-password');
        return view( 'admin/changepassword')->with($data);
    }
	
	public function update_category(){
        $data['pageTitle'] = trans('messages.category');
        return view( 'admin/update-category')->with($data);
    }
	
	public function category_list(){
        $data['pageTitle'] = trans('messages.category');
        return view( 'admin/category-list')->with($data);
    }
	
	public function add_category(){
        $data['pageTitle'] = trans('messages.category');
        return view( 'admin/add-category')->with($data);
    }
	public function login_history(){
        $data['pageTitle'] = trans('messages.category');
        return view( 'admin/login-history')->with($data);
    }
	public function forgot_password(){
        $data['pageTitle'] = trans('messages.forgot-password');
        return view( 'admin/design/forgot-password')->with($data);
       // return view( 'admin/forgot-password')->with($data);
    }
	
	public function reset_password(){
        $data['pageTitle'] = trans('messages.reset-password');
        return view( 'admin/reset-password')->with($data);
    }
	public function document_folder()
	{
		$data['pageTitle'] = trans('messages.document-folder');
		return view('admin/design/document-folder')->with($data);
	}

	public function notifications()
	{
		$data['pageTitle'] = trans('messages.notifications');
		return view('admin/design/notifications')->with($data);
	}

	public function add_employee()
	{
		$data['pageTitle'] = trans('messages.add-employee');
		return view('admin/design/add-employee')->with($data);
	}

	
	public function salary_components()
	{
		$data['pageTitle'] = trans('messages.salary-components');
		return view('admin/design/salary-components')->with($data);
	}
	public function salary_groups()
	{
		$data['pageTitle'] = trans('messages.salary-groups');
		return view('admin/design/salary-groups')->with($data);
	}
	









	
	public function document_type()
	{
		$data['pageTitle'] = trans('messages.document-type');
		return view('admin/design/document-type')->with($data);
	}
	public function holiday_master()
	{
		$data['pageTitle'] = trans('messages.holiday-master');
		return view('admin/design/holiday-master')->with($data);
	}
	public function bank_master()
	{
		$data['pageTitle'] = trans('messages.bank-master');
		return view('admin/design/bank-master')->with($data);
	}
	public function incident_report()
	{
		$data['pageTitle'] = trans('messages.incident-report');
		return view('admin/design/incident-report')->with($data);
	}
	public function add_incident_report()
	{
		$data['pageTitle'] = trans('messages.add-incident-report');
		return view('admin/design/add-incident-report')->with($data);
	}
	public function city()
	{
		$data['pageTitle'] = trans('messages.city');
		return view('admin/design/city')->with($data);
	}
	public function probation_policy_master()
	{
		$data['pageTitle'] = trans('messages.probation-policy-master');
		return view('admin/design/probation-policy-master')->with($data);
	}
	public function employee()
	{
		$data['pageTitle'] = trans('messages.employee');
		return view('admin/design/employee')->with($data);
	}
	
	public function employee_report()
	{
		$data['pageTitle'] = trans('messages.employee-report');
		return view('admin/design/employee-report')->with($data);
	}
	public function salary_report()
	{
		$data['pageTitle'] = trans('messages.salary-report');
		return view('admin/design/salary-report')->with($data);
	}
	public function state()
	{
		$data['pageTitle'] = trans('messages.state');
		return view('admin/design/state')->with($data);
	}
	public function attendance_report()
	{
		$data['pageTitle'] = trans('messages.attendance-report');
		return view('admin/design/attendance-report')->with($data);
	}
	
	public function attendance_report_day_wise()
	{
		$data['pageTitle'] = trans('messages.attendance-report-day-wise');
		return view('admin/design/attendance-report-day-wise')->with($data);
	}

	public function shift_master()
	{
		$data['pageTitle'] = trans('messages.shift-master');
		return view('admin/design/shift-master')->with($data);
	}

	public function add_shift()
	{
		$data['pageTitle'] = trans('messages.add-shift');
		return view('admin/design/add-shift')->with($data);
	} 

	public function weekly_off_master()
	{
		$data['pageTitle'] = trans('messages.weekly-off-master');
		return view('admin/design/weekly-off-master')->with($data);
	}

	public function on_hold_salary_report()
	{
		$data['pageTitle'] = trans('messages.on-hold-salary-report');
		return view('admin/design/on-hold-salary-report')->with($data);
	} 
	
	public function document_report()
	{
		$data['pageTitle'] = trans('messages.document-report');
		return view('admin/design/document-report')->with($data);
	} 
	public function leave_report()
	{
		$data['pageTitle'] = trans('messages.leave-report');
		return view('admin/design/leave-report')->with($data);
	} 

	public function time_off_report()
	{
		$data['pageTitle'] = trans('messages.time-off-report');
		return view('admin/design/time-off-report')->with($data);
	}

	public function punch_report()
	{
		$data['pageTitle'] = trans('messages.punch-report');
		return view('admin/design/punch-report')->with($data);
	}

	public function profile()
	{
		$data['pageTitle'] = trans('messages.profile');
		return view('admin/design/profile')->with($data);
	}
	public function statutaroy_bonus_report()
	{
		$data['pageTitle'] = trans('messages.statutaroy-bonus-report');
		return view('admin/design/statutory-bonus-report')->with($data);
	}
	public function verify_otp()
	{
		$data['pageTitle'] = trans('messages.verify-otp');
		return view('admin/design/verify-otp')->with($data);
	}





    // public function slip(){
    // 	$fontdata = [
    // 			'poppins-regular' => [
    // 					'R' => 'Poppins-Regular.ttf',
    // 			],
    // 	];
    	
    // 	$fontdata = [
    // 			'poppins-medium' => [
    // 					'R' => 'Poppins-Medium.ttf',
    // 			],
    // 	];
    	
    // 	$fontdata = [
    // 			'poppins-bold' => [
    // 					'R' => 'Poppins-Bold.ttf',
    // 			],
    // 	];
    	
    // 	// $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    	
    // 	// $fontDirs = $defaultConfig['fontDir'];
    // 	// $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    // 	// $fontDirs = $defaultConfig['fontDir'];
    // 	// $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    // 	// $fontData = $defaultFontConfig['fontdata'];
    // 	$fontData += [
    // 			'poppins-regular' => [
    // 					'R' => 'Poppins-Regular.ttf',
    // 			]
    // 	];
    // 	$fontData += [
    // 			'poppins-medium' => [
    // 					'R' => 'Poppins-Medium.ttf',
    // 			]
    // 	];
    // 	$fontdata = [
    // 			'poppins-bold' => [
    // 					'R' => 'Poppins-Bold.ttf',
    // 			],
    // 	];
    // 	$data = [];
    // 	$html = view ( 'pdf/sample-pdf')->with ( $data );
    	
    // 	$mpdf = new \Mpdf\Mpdf([
    // 			'mode' => 'c',
    // 			'format' => 'A4',
    // 			'margin_left' => 0,
    // 			'margin_right' => 0,
    // 			'margin_top' => 0,
    // 			'margin_bottom' => 0,
    // 			'margin_header' => 0,
    // 			'margin_footer' => 0,
    // 			'fontDir' => array_merge($fontDirs, [
    // 					dirname(dirname(__DIR__)).'/assets/css/fonts/',
    // 			]),
    // 			'fontdata' => $fontData,
    // 			'mode' => 'utf-8',
    // 	]);
    	
    // 	$mpdf->WriteHTML($html,2);
    // 	$mpdf->Output();
    // }
    

































































	// ---------------------------------------mubassir-------------------------

	

	
	
	
	
	public function my_leaves()
	{
		$data['pageTitle'] = trans('messages.my-leaves');
		return view('admin/design/my-leaves')->with($data);
	}
	public function my_time_off()
	{
		$data['pageTitle'] = trans('messages.my-time-off');
		return view('admin/design/my-time-off')->with($data);
	}
	public function attendance_summary()
	{
		$data['pageTitle'] = trans('messages.attendance-summary');
		return view('admin/design/attendance-summary')->with($data);
	}
	public function leave_summary()
	{
		$data['pageTitle'] = trans('messages.leave-summary');
		return view('admin/design/leave-summary')->with($data);
	}
	public function timeoff_summary()
	{
		$data['pageTitle'] = trans('messages.time-off-summary');
		return view('admin/design/timeoff-summary')->with($data);
	}
	public function employees_summary()
	{
		$data['pageTitle'] = trans('messages.employees-summary');
		return view('admin/design/employees-summary')->with($data);
	}
	public function salary_summary()
	{
		$data['pageTitle'] = trans('messages.salary-summary');
		return view('admin/design/salary-summary')->with($data);
	}
	public function incident_summary()
	{
		$data['pageTitle'] = trans('messages.incident-summary');
		return view('admin/design/incident-summary')->with($data);
	}
	public function my_salary()
	{
		$data['pageTitle'] = trans('messages.my-salary');
		return view('admin/design/my-salary')->with($data);
	}
	public function my_documents()
	{
		$data['pageTitle'] = trans('messages.my-documents');
		return view('admin/design/my-documents')->with($data);
	}
	public function my_payslip()
	{
		$data['pageTitle'] = trans('messages.my-payslip');
		return view('admin/design/my-payslip')->with($data);
	}
	public function my_attendance()
	{
		$data['pageTitle'] = trans('messages.my-attendance');
		return view('admin/design/my-attendance')->with($data);
	}
	public function salary_increment_report()
	{
		$data['pageTitle'] = trans('messages.salary-increment-report');
		return view('admin/design/salary-increment-report')->with($data);
	}
	public function add_roles_permissions()
	{
		$data['pageTitle'] = trans('messages.add-roles-permissions');
		return view('admin/design/add-roles-permissions')->with($data);
	}
	public function roles_permissions()
	{
		$data['pageTitle'] = trans('messages.roles-permissions');
		return view('admin/design/roles-permissions')->with($data);
	}
	public function salary_report_for_account_team()
	{
		$data['pageTitle'] = trans('messages.salary-report-for-account-team');
		return view('admin/design/salary-report-for-account-team')->with($data);
	}
	public function employee_duration_report()
	{
		$data['pageTitle'] = trans('messages.employee-duration-report');
		return view('admin/design/employee-duration-report')->with($data);
	}
	public function leave_report_month_wise_count()
	{
		$data['pageTitle'] = trans('messages.leave-report-month-wise-count');
		return view('admin/design/leave-report-month-wise-count')->with($data);
	}
	// public function email_demo()
	// {
	// 	$data['pageTitle'] = trans('messages.email-demo');
	// 	return view('admin/design/email-demo')->with($data);
	// }

	public function email_demo()
	{
		$mailData = [];
		$mailData['partner_name'] = 'The WildTigers';
		$mailData['receipt_name'] = 'The WildTigers';
		$mailData['first_name'] = 'The';
		$mailData['last_name'] = 'WildTigers';
		$mailData['mobile'] = '7600797896';
		$mailData['email'] = 'ritesh.k.twt@gmail.com';
		$mailData['v_website'] = 'https://www.thewildtigers.com/';

		$salesTeamTemplate = View('admin/design/email-demo', $mailData)->render();

		//var_dump($salesTeamTemplate);die;

		//echo ($salesTeamTemplate);die;
		$config['mailData'] = $mailData;
		$config['viewName'] = 'admin/design/email-demo';
		$config['v_mail_content'] = $salesTeamTemplate;
		$config['subject'] = "demo mail";
		$config['to'] = 'mubassir.m.twt@gmail.com';

		$sendMail = sendMailSMTP($config);

		var_dump($sendMail);
		die;

	}

	public function email_table()
	{
		$mailData = [];
		$mailData['partner_name'] = 'The WildTigers';
		$mailData['receipt_name'] = 'The WildTigers';
		$mailData['first_name'] = 'The';
		$mailData['last_name'] = 'WildTigers';
		$mailData['mobile'] = '7600797896';
		$mailData['email'] = 'ritesh.k.twt@gmail.com';
		$mailData['v_website'] = 'https://www.thewildtigers.com/';

		$salesTeamTemplate = View('admin/design/email-table', $mailData)->render();

		//var_dump($salesTeamTemplate);die;

		// echo ($salesTeamTemplate);die;
		$config['mailData'] = $mailData;
		$config['viewName'] = 'admin/design/email-table';
		$config['v_mail_content'] = $salesTeamTemplate;
		$config['subject'] = "demo mail";
		$config['to'] = 'deep.suthar.twt@gmail.com';

		$sendMail = sendMailSMTP($config);

		var_dump($sendMail);
		die;

	}

	public function lateness_email()
	{
		$mailData = [];
		$mailData['partner_name'] = 'The WildTigers';
		$mailData['receipt_name'] = 'The WildTigers';
		$mailData['first_name'] = 'The';
		$mailData['last_name'] = 'WildTigers';
		$mailData['mobile'] = '7600797896';
		$mailData['email'] = 'ritesh.k.twt@gmail.com';
		$mailData['v_website'] = 'https://www.thewildtigers.com/';

		$salesTeamTemplate = View('admin/design/lateness-email', $mailData)->render();

		//var_dump($salesTeamTemplate);die;

		echo ($salesTeamTemplate);die;
		$config['mailData'] = $mailData;
		$config['viewName'] = 'admin/design/lateness-email';
		$config['v_mail_content'] = $salesTeamTemplate;
		$config['subject'] = "Lateness alert!";
		$config['to'] = 'mubassir.m.twt@gmail.com';

		$sendMail = sendMailSMTP($config);

		var_dump($sendMail);
		die;
	}

	public function missing_punch_mail ()
	{
		$mailData = [];
		$mailData['partner_name'] = 'The WildTigers';
		$mailData['receipt_name'] = 'The WildTigers';
		$mailData['first_name'] = 'The';
		$mailData['last_name'] = 'WildTigers';
		$mailData['mobile'] = '7600797896';
		$mailData['email'] = 'ritesh.k.twt@gmail.com';
		$mailData['v_website'] = 'https://www.thewildtigers.com/';

		$salesTeamTemplate = View('admin/design/missing-punch-mail', $mailData)->render();

		//var_dump($salesTeamTemplate);die;

		echo ($salesTeamTemplate);die;
		$config['mailData'] = $mailData;
		$config['viewName'] = 'admin/design/missing-punch-mail';
		$config['v_mail_content'] = $salesTeamTemplate;
		$config['subject'] = "Seems punch missed!";
		$config['to'] = 'mubassir.m.twt@gmail.com';

		$sendMail = sendMailSMTP($config);

		var_dump($sendMail);
		die;
	}
	public function birthdays_email()
	{
		$mailData = [];
		$mailData['partner_name'] = 'The WildTigers';
		$mailData['receipt_name'] = 'The WildTigers';
		$mailData['first_name'] = 'The';
		$mailData['last_name'] = 'WildTigers';
		$mailData['mobile'] = '7600797896';
		$mailData['email'] = 'mitesh.prajapati191@gmail.com';
		$mailData['v_website'] = 'https://www.thewildtigers.com/';

		$salesTeamTemplate = View('welcome', $mailData)->render();

		//var_dump($salesTeamTemplate);die;

		//echo ($salesTeamTemplate);die;
		$config['mailData'] = $mailData;
		$config['viewName'] = 'welcome';
		$config['v_mail_content'] = $salesTeamTemplate;
		$config['subject'] = "Hope you are doing well.!";
		$config['to'] = 'deep.s.twt@gmail.com';
		//dd($config);
		$sendMail = sendMailSMTP($config);

		var_dump($sendMail);
		die;
	}
	public function work_anniversary_email()
	{
		$mailData = [];
		$mailData['partner_name'] = 'The WildTigers';
		$mailData['receipt_name'] = 'The WildTigers';
		$mailData['first_name'] = 'The';
		$mailData['last_name'] = 'WildTigers';
		$mailData['mobile'] = '7600797896';
		$mailData['email'] = 'ritesh.k.twt@gmail.com';
		$mailData['v_website'] = 'https://www.thewildtigers.com/';

		$salesTeamTemplate = View('admin/design/work-anniversary-email', $mailData)->render();

		//var_dump($salesTeamTemplate);die;

		echo ($salesTeamTemplate);die;
		$config['mailData'] = $mailData;
		$config['viewName'] = 'admin/design/work-anniversary-email';
		$config['v_mail_content'] = $salesTeamTemplate;
		$config['subject'] = "Hope you are doing well..!";
		$config['to'] = 'mubassir.m.twt@gmail.com';

		$sendMail = sendMailSMTP($config);

		var_dump($sendMail);
		die;
	}
	public function salary_calculation()
	{
		$data['pageTitle'] = trans('messages.salary-calculation');
		return view('admin/design/salary-calculation')->with($data);
	}



}
