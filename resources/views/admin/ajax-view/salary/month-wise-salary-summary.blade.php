<div class="card card-display border-0 px-2 py-3 h-100">
	<div class="card-body px-2 py-0">
		<h5 class="profile-details-title">{{ trans("messages.month-wise-salary") }}</h5>
	    <div class="pb-2 pt-2">
	    	<canvas id="month-wise-salary-summary" height="80"></canvas>
	    	<p class="total-record-month-wise-salary-count mb-0 text-center" style="display: none">{{ trans("messages.no-record-found") }}</p>
	   	</div>
	</div>
</div>
<script>
 var month_wise_salary_chart = null;
 var month_name_info = [];
 var month_wise_salary_info = [];
 var total_number_of_record_status = true;
 var month_wise_salary_data = <?php echo (!empty($monthWiseSalaryDetails) ? preg_replace('/"([^"]+)"\s*:\s*/', '$1:', json_encode($monthWiseSalaryDetails))  : '[]') ?>;

 $(document).ready(function() {
     var month_wise_details = month_wise_salary_data;
     $(month_wise_details).each(function(index, value) {
        var salary_month =  value.dt_salary_month;
        if( salary_month != "" && salary_month != null  ){
        	salary_month = moment(salary_month,'YYYY-MM-DD').format('MMM');
        }
       
    	month_name_info.push(salary_month);
    	month_wise_salary_info.push(convertAmountIntoDouble(value.salary_amount));
     	if(value.salary_amount > 0 ){
     		total_number_of_record_status = false;
     	}
     });
     if(total_number_of_record_status != true ){
  		$('#month-wise-salary-summary').show();
  		$('.total-record-month-wise-salary-count').hide();
  	}else {
      	$('.total-record-month-wise-salary-count').show();
      	$('#month-wise-salary-summary').hide();
     }	
     monthWiseSalaryChart(month_name_info , month_wise_salary_info); 
 });
 var back_ground_color = [];
 function monthWiseSalaryChart(month_name_info,month_wise_salary_info ){
	if( month_wise_salary_chart != "" && month_wise_salary_chart != null ){
		month_wise_salary_chart.destroy();
 	}
 	
 	var allMonthName = getAllMonthName();
 	var back_ground_color = [];
	$(month_name_info).each(function(index, value) {
		if( $.inArray(value,allMonthName) != -1 ){
			back_ground_color.push(month_wise_chart_colors[$.inArray(value,allMonthName)]);
		} else {
			back_ground_color.push("#d6a6dd");
		}
	});
 	
 	var ctx = document.getElementById("month-wise-salary-summary").getContext('2d');
    var month_wise_salary_chart = new Chart(ctx, {
    	type: 'bar',
        data: {
        	labels: month_name_info,
            datasets: [{
                label: '{{ trans("messages.salary") }}' + ' ' + '{{ config("constants.SALARY_CURRENCY_SYMBOL") }}',
                data: month_wise_salary_info,
                backgroundColor: back_ground_color,
                hoverBackgroundColor: back_ground_color,
            }]

        },
        options: {
            layout: {
                padding: {
                    bottom: 5,
                },

            },
            legend: {
                display: false,
               
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        display: false,
                        beginAtZero: true,
                    },
                }],
                yAxes: [{
                    ticks: {
                        display: false,
                        beginAtZero: true,
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    }
                }],
            }
        }
    });
 }
</script>