<html>

<head>
</head>

<body>

    <div>
        <table width="100%" style="padding: 20px 20px 0 20px;margin:20px !important;font-family: 'Poppins', sans-serif;">
            <thead style="padding-bottom: 10px;">
                <tr>
                    <th style="width:20%; border: 0;">

                    </th>
                    <th style="width:60%; border: 0;">
                        <img src="{{ config('constants.COMPANY_LOGO_PATH') }}" style="width:25%; padding-bottom:10px;">
                        <br />
                        <p style="font-size:12px;">{{ config('constants.COMPANY_ADDRESS') }}</p>
                    </th>
                    <th style="width:20%; text-align:right; border: 0;">
                        <h2>Pay Slip</h2>
                        <h4>{{ ( isset($recordInfo->dt_salary_month) ?  convertDateFormat($recordInfo->dt_salary_month , 'F-Y') : '' ) }}</h4>
                </tr>
            </thead>
        </table>
        <table width="100%" cellpadding="5" style="margin:10px 20px 20px 20px;text-align: left;border: 1px solid;font-family: 'Poppins', sans-serif;" cellspacing="2">
            <thead>
                <tr>
                    <th style="text-align: left;padding:5px;border-bottom: 1px solid;font-size: 18px;" colspan="2">
                        <strong>{{ ( isset($recordInfo->employee->v_employee_full_name) ?  $recordInfo->employee->v_employee_full_name : '' ) }}</strong>
                    </th>
                </tr>
            </thead>
            <tr>
                <td width="45%" style="vertical-align: top; max-width:45%;width:45%;">
                    <table style="padding: 0;border:none;text-align: left;float: left;border-collapse:collapse;width: 100%;" cellpadding="5" cellspacing="2">
                        <tbody>
                            <tr>
                                <td>Employee Code : </td>
                                <td>{{ ( isset($recordInfo->employee->v_employee_code) ?  $recordInfo->employee->v_employee_code : '' ) }}</td>
                            </tr>
                            <tr>
                                <td>Designation : </td>
                                <td>{{ ( isset($recordInfo->employee->designationInfo->v_value) ?  $recordInfo->employee->designationInfo->v_value : '' ) }}</td>
                            </tr>
                            <tr>
                                <td>Bank Name : </td>
                                <td>{{ ( isset($recordInfo->employee->bankInfo->v_value) ?  $recordInfo->employee->bankInfo->v_value : '' ) }}</td>
                            </tr>
                            <tr>
                                <td>Bank Account Number : </td>
                                <td>{{ ( isset($recordInfo->employee->v_bank_account_no) ?  $recordInfo->employee->v_bank_account_no : '' ) }}</td>
                            </tr>
                            <tr>
                                <td>Date of joining : </td>
                                <td>{{ ( isset($recordInfo->employee->dt_joining_date) ?  convertDateFormat ( $recordInfo->employee->dt_joining_date ) : '' ) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <td width="45%" style="border-left: 1px solid;vertical-align: top;max-width:47%;width:47%;">
                    <table style="padding: 0;border:none;text-align: left;float: left;border-collapse:collapse;width: 100%;" cellpadding="5" cellspacing="2">
                        <tbody>
                            <tr>
                                <td>Income Tax Number (PAN) : </td>
                                <td>{{ ( isset($recordInfo->employee->v_pan_no) ?   ( $recordInfo->employee->v_pan_no ) : '' ) }}</td>
                            </tr>
                            <tr>
                                <td>Universal Account Number (UAN) : </td>
                                <td>{{ ( isset($recordInfo->employee->v_uan_no) ?   ( $recordInfo->employee->v_uan_no ) : '' ) }}</td>
                            </tr>
                            <tr>
                                <td>Working Days :</td>
                                <td>{{ ( isset($recordInfo->d_paid_days_count) ?  $recordInfo->d_paid_days_count : 0 ) }}</td>
                            </tr>
                            <tr></tr>
                            <tr></tr>

                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        @php
        $earningHeadHtml  = "";
        $deductHeadHtml  = "";
        $earningHeadCount = $deductHeadCount =  [];
        @endphp
		@if( isset($recordInfo->generatedSalaryInfo) && (!empty($recordInfo->generatedSalaryInfo)) )
		 	@php $recordInfo->generatedSalaryInfo = $recordInfo->generatedSalaryInfo->sortBy('generateSalaryComponent.i_sequence'); @endphp
			@foreach($recordInfo->generatedSalaryInfo as $salaryDetail)
				<?php 
				if( isset($salaryDetail->generateSalaryComponent->e_salary_components_type) && (!empty($salaryDetail->generateSalaryComponent->e_salary_components_type))  ){
					switch($salaryDetail->generateSalaryComponent->e_salary_components_type){
						case config('constants.SALARY_COMPONENT_TYPE_EARNING'):
							$earningHeadCount[] = $salaryDetail->generateSalaryComponent->v_component_name;
							$earningHeadHtml .= '<tr>';
                            $earningHeadHtml .= '<td style="border-left: 0px solid;">'.( isset($salaryDetail->generateSalaryComponent->v_component_name)  ? $salaryDetail->generateSalaryComponent->v_component_name : '' ).'</td>';
                            $earningHeadHtml .= '<td style="border-left: 1px solid; text-align: right;">'. ( isset($salaryDetail->d_paid_amount) ? decimalAmount($salaryDetail->d_paid_amount) : '' )  .'</td>';
                            $earningHeadHtml .= '</tr>';
							break;
						case config('constants.SALARY_COMPONENT_TYPE_DEDUCTION'):
							$deductHeadCount[] = $salaryDetail->generateSalaryComponent->v_component_name;
							$deductHeadHtml .= '<tr>';
							$deductHeadHtml .= '<td style="border-left: 1px solid;">'.( isset($salaryDetail->generateSalaryComponent->v_component_name)  ? $salaryDetail->generateSalaryComponent->v_component_name : '' ).'</td>';
							$deductHeadHtml .= '<td style="border-left: 1px solid; text-align: right;">'. ( isset($salaryDetail->d_paid_amount) ? decimalAmount($salaryDetail->d_paid_amount) : '' )  .'</td>';
							$deductHeadHtml .= '</tr>';
							break;
					}	
				}
				?>
			@endforeach
		@endif	
		<?php $masterAdditionalDeductionRowCount = ( count($earningHeadCount) - count($deductHeadCount) ); ?>
        <table width="100%" style="margin:20px; height:100%;border:1px solid;border-collapse: collapse; table-layout: fixed">
            <tbody>
                <tr>
                    <td style="padding: 0;vertical-align:top;width:45%;max-width:45%;min-width:45%;">
                        <table width="100%" style="padding: 0;border:0;text-align: left;border-collapse: collapse;font-family: 'Poppins', sans-serif;margin:0px;overflow:wrap; table-layout: fixed" cellpadding="5" cellspacing="2">
                            <thead>
                                <tr style="background-color: #8d191a;">
                                    <th style="border-collapse:collapse;border-left: 0px solid;border-bottom: 1px solid;text-align: left;width:55%;max-width:55%;min-width:55%;font-size:15px;color: #fff; text-align: left;">
                                        <strong>Earnings</strong>
                                    </th>
                                    <th style="border-collapse:collapse;border-left: 1px solid;border-bottom: 1px solid;color: #fff;width:28%;max-width:28%;min-width:28%;font-size:15px; text-align: center;">
                                        <strong>Amount</strong>
                                    </th>
                                </tr>
                            </thead>
							<tbody>
                                <?php echo $earningHeadHtml ?>
                                <tr>
                                    <td style="border-left: 0px solid;word-break:break-all;border-bottom: 1px solid;border-top: 1px solid;font-size:14px;width:55%;max-width:55%;min-width:55%;border-collapse: collapse;">
                                        <strong>Total Earnings</strong>
                                    </td>
                                    <td style="border-left: 1px solid;font-size:14px;word-break:break-all;border-bottom: 1px solid;border-top: 1px solid; text-align: right;width:28%;max-width:28%;min-width:28%;border-collapse: collapse;">
                                        <strong>{{ ( isset($recordInfo->d_total_earning_amount) ?  decimalAmount($recordInfo->d_total_earning_amount) : '' ) }}</strong>
                                    </td>
                                </tr>


                            </tbody>
                        </table>
                    </td>

                    <td style="padding: 0;vertical-align:top;width:47%;max-width:47%;min-width:47%;">
                        <table width="100%" style="padding: 0;border:0;text-align: left;border-collapse: collapse;font-family: 'Poppins', sans-serif;margin:0px;overflow:wrap; table-layout: fixed;border-left:1px solid" cellpadding="5" cellspacing="2">
                            <thead>
                                <tr style="background-color: #8d191a;">
                                    <th style="border-collapse:collapse;border-bottom: 1px solid;font-size:15px;width:50%;max-width:50%;min-width:50%;text-align: left;color: #fff;">
                                        <strong>Deductions</strong>
                                    </th>
                                    <th style="border-collapse:collapse;border-left: 1px solid;border-bottom: 1px solid;font-size:15px;width:30%;max-width:30%;min-width:30%;color: #fff;text-align: center">
                                        <strong>Amount</strong>
                                    </th>
                                </tr>
                            </thead>


                            <tbody>
                                <?php echo $deductHeadHtml ?>
                                 <?php 
                                 if( $masterAdditionalDeductionRowCount > 0 ){
                                 	for($i = 1 ; $i <= $masterAdditionalDeductionRowCount ; $i++  ){
                                    	?>
                                        <tr>
                                        	<td style="border-left: 1px solid;border-bottom: 0px solid;word-break:break-all;border-top: 0px solid;width:50%;max-width:50%;min-width:50%;border-collapse: collapse;height:100%">&nbsp;</td>
                                            <td style="border-left: 1px solid;border-bottom: 0px solid;word-break:break-all;border-top: 0px solid; text-align: right;width:30%;max-width:30%;min-width:30%;border-collapse: collapse;height:100%">&nbsp;</td>
                                       	</tr>
                                        <?php
                                   	}
                                } 
                                ?>
                                <tr>
                                    <td style="border-left: 1px solid;font-size:14px;border-bottom: 1px solid;word-break:break-all;width:50%;max-width:50%;min-width:50%;border-collapse: collapse;border-top: 1px solid;">
                                        <strong>Total Deductions</strong>
                                    </td>
                                    <td style="border-left: 1px solid;font-size:14px;border-bottom: 1px solid;word-break:break-all;text-align: right;width:30%;max-width:30%;min-width:30%;border-collapse: collapse;border-top: 1px solid;">
                                        <strong>{{ ( isset($recordInfo->d_total_deduct_amount) ?  decimalAmount($recordInfo->d_total_deduct_amount) : '' ) }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border-left: 1px solid;word-break:break-all;width:50%;max-width:50%;min-width:50%;border-collapse: collapse;font-size:14px;">
                                        <strong>Net Amount</strong>
                                    </td>
                                    <td style="border-left: 1px solid;word-break:break-all;text-align: right;width:30%;max-width:30%;min-width:30%;border-collapse: collapse;font-size:14px;">
                                        <strong>₹ {{ ( isset($recordInfo->d_net_pay_amount) ?  decimalAmount($recordInfo->d_net_pay_amount) : '' ) }}</strong>
                                    </td>
                                </tr>

                            </tbody>

                        </table>
                    </td>

                </tr>
            </tbody>
        </table>
		<div class="amount" style="margin:50px 20px 0px 20px;font-family: 'Poppins', sans-serif;font-size:16px;">
            <strong>Amount (in words)</strong>
        </div>
        <div class="amount" style="margin:10px 20px 20px 20px;font-family: 'Poppins', sans-serif;">INR : {{ ( isset($recordInfo->d_net_pay_amount) ?  convertAmountIntoWord($recordInfo->d_net_pay_amount) : '' ) }}</div>
	</div>
</body>
</html>