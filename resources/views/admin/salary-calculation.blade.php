<script>

function getSalaryGroupDetail(){

	var deduction_employer_from_employee = $.trim($("[name='deduction_employer_from_employee']:checked").val());
	//console.log("deduction_employer_from_employee = " + deduction_employer_from_employee );
	if( deduction_employer_from_employee != "" && deduction_employer_from_employee != null ){
		$.ajax({
			type: "POST",
			url: '{{ config("constants.EMPLOYEE_MASTER_URL") }}' + '/getSalaryGroup',
			data: { 
				'deduction_employer_from_employee':deduction_employer_from_employee,
			},
			beforeSend: function() {
				//block ui
				showLoader();
			},
			success: function(response) {
				hideLoader();
				if( response != "" && response != null ){
					var previous_value = $.trim($("[name='salary_group']").find('option:selected').attr('data-id'));
					$("[name='salary_group']").html(response);
					if( previous_value != "" && previous_value != null ){
						$("[name='salary_group'] option[data-id='" + previous_value + "']").prop("selected", true);
					}
					totalEarning();
					totalDeduction();
				}
			},
			error: function() {
				hideLoader();
			}
		});
	} else {
		$("[name='salary_group']").html("");
		totalEarning();
		totalDeduction();
	}
}

function totalEarning(){
	var monthly_total = 0;
	var yearly_total = 0;
	$(".earning-row").each(function(){
		var monthly_value = $.trim($(this).find(".monthly-column").val());
		if( parseFloat(monthly_value)  > 0.00 ){
			monthly_total = ( parseFloat(monthly_total) + parseFloat(monthly_value) );
		}
	})
	monthly_total = ( parseFloat(monthly_total) > 0.00 ? parseFloat(monthly_total).toFixed(2) : 0.00 );
	if(parseFloat(monthly_total) > 0 ){
		yearly_total = ( parseFloat(monthly_total) * 12 );
		yearly_total = ( parseFloat(yearly_total) > 0.00 ? parseFloat(yearly_total).toFixed(2) : 0.00 );
	}
	//console.log("monthly_total = " + monthly_total );
	//console.log("yearly_total = " + yearly_total );
	$(".total-month-earning-amount").show();
	$(".total-month-earning-amount").html(displayValueIntoIndianCurrency(monthly_total));
	$(".total-yearly-earning").html(displayValueIntoIndianCurrency(yearly_total));
	calculateNetPay()
 }

 function totalDeduction(){
	var monthly_total = 0;
	var yearly_total = 0;
	$(".deduction-row").each(function(){
		var monthly_value = $.trim($(this).find(".monthly-column").val());
		if( parseFloat(monthly_value)  > 0.00 ){
			monthly_total = ( parseFloat(monthly_total) + parseFloat(monthly_value) );
		}
	})
	monthly_total = ( parseFloat(monthly_total) > 0.00 ? parseFloat(monthly_total).toFixed(2) : 0.00 );
	if(parseFloat(monthly_total) > 0 ){
		yearly_total = ( parseFloat(monthly_total) * 12 );
		yearly_total = ( parseFloat(yearly_total) > 0.00 ? parseFloat(yearly_total).toFixed(2) : 0.00 );
	}
	//console.log("monthly_total = " + monthly_total );
	//console.log("yearly_total = " + yearly_total );
	$(".total-month-deduction-amount").show();
	$(".total-month-deduction-amount").html(displayValueIntoIndianCurrency(monthly_total));
	$(".total-yearly-deduction").html(displayValueIntoIndianCurrency(yearly_total));
	calculateNetPay();
 }

 function calculateNetPay(){

	$(".net-pay-table-div").show(); 
	var total_monthly_earning =  $(".total-month-earning-amount").html();
	var total_monthly_deduction =  $(".total-month-deduction-amount").html(); 
	var total_yearly_earning =  $(".total-yearly-earning").html();
	var total_yearly_deduction =  $(".total-yearly-deduction").html();

	//console.log("total_monthly_earning = " + total_monthly_earning );
	//console.log("total_monthly_deduction = " + total_monthly_deduction );
	//console.log("total_yearly_earning = " + total_yearly_earning );
	//console.log("total_yearly_deduction = " + total_yearly_deduction );

	total_monthly_earning = removeCommaFromValue(total_monthly_earning);
	total_monthly_deduction = removeCommaFromValue(total_monthly_deduction);
	total_yearly_earning = removeCommaFromValue(total_yearly_earning);
	total_yearly_deduction = removeCommaFromValue(total_yearly_deduction);

	//console.log("total_monthly_earning = " + total_monthly_earning );
	//console.log("total_monthly_deduction = " + total_monthly_deduction );
	//console.log("total_yearly_earning = " + total_yearly_earning );
	//console.log("total_yearly_deduction = " + total_yearly_deduction );
	
	total_monthly_earning = ( parseFloat(total_monthly_earning) > 0.00 ? parseFloat(total_monthly_earning).toFixed(2) : 0.00 );
	total_monthly_deduction = ( parseFloat(total_monthly_deduction) > 0.00 ? parseFloat(total_monthly_deduction).toFixed(2) : 0.00 );
	total_yearly_earning = ( parseFloat(total_yearly_earning) > 0.00 ? parseFloat(total_yearly_earning).toFixed(2) : 0.00 );
	total_yearly_deduction = ( parseFloat(total_yearly_deduction) > 0.00 ? parseFloat(total_yearly_deduction).toFixed(2) : 0.00 );

	//console.log("total_monthly_earning = " + total_monthly_earning );
	//console.log("total_monthly_deduction = " + total_monthly_deduction );
	//console.log("total_yearly_earning = " + total_yearly_earning );
	//console.log("total_yearly_deduction = " + total_yearly_deduction );
	
	var monthly_net_pay = parseFloat(total_monthly_earning) - parseFloat(total_monthly_deduction);
	var yearly_net_pay = parseFloat(total_yearly_earning) - parseFloat(total_yearly_deduction);

	//console.log("monthly_net_pay = " + monthly_net_pay );
	//console.log("yearly_net_pay = " + yearly_net_pay );

	monthly_net_pay = ( parseFloat(monthly_net_pay) > 0.00 ? parseFloat(monthly_net_pay).toFixed(2) : 0.00 );
	yearly_net_pay = ( parseFloat(yearly_net_pay) > 0.00 ? parseFloat(yearly_net_pay).toFixed(2) : 0.00 );

	//console.log("yearly_net_pay = " + yearly_net_pay );;
	$(".monthly-net-pay-amount").html(displayValueIntoIndianCurrency(monthly_net_pay));
	$(".yearly-net-pay-amount").html(displayValueIntoIndianCurrency(yearly_net_pay));
	$(".net-pay-div").show();
	 
 }
</script>