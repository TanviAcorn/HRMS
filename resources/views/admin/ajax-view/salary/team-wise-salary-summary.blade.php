<div class="card card-display border-0 px-2 py-3 h-100">
	<div class="card-body px-2 py-0">
		<h5 class="profile-details-title">{{ trans("messages.team-wise-salary") }}</h5>
	    <div class="pb-2 pt-2">
	    	<canvas id="team-wise-salary-chart" height="180"></canvas>
	    	<p class="total-record-team-wise-salary-chart mb-0 text-center" style="display: none">{{ trans("messages.no-record-found") }}</p>
	   	</div>
	</div>
</div>
<script>
 var team_wise_salary_chart = null;
 var team_name_info = [];
 var team_wise_salary_info = [];
 var team_wise_color_code_info = [];
 var total_number_of_record_status = true;
 var team_wise_salary_data = <?php echo (!empty($teamWiseSalaryDetails) ? preg_replace('/"([^"]+)"\s*:\s*/', '$1:', json_encode($teamWiseSalaryDetails))  : '[]') ?>;
//console.log("team_wise_salary_data");
//console.log(team_wise_salary_data);
 $(document).ready(function() {
     var team_wise_details = team_wise_salary_data;
     $(team_wise_details).each(function(index, value) {
    	team_name_info.push(value.v_value);
    	team_wise_salary_info.push(convertAmountIntoDouble(value.salary_amount));
    	team_wise_color_code_info.push('#' + value.v_chart_color);
    	if(value.salary_amount > 0 ){
     		total_number_of_record_status = false;
     	}
     });
     if(total_number_of_record_status != true ){
  		$('#team-wise-salary-chart').show();
  		$('.total-record-team-wise-salary-chart').hide();
  	}else {
      	$('.total-record-team-wise-salary-chart').show();
      	$('#team-wise-salary-chart').hide();
     }	
     teamWiseSalaryChart(team_name_info , team_wise_salary_info , team_wise_color_code_info ); 
 });
 var back_ground_color = [];
 function teamWiseSalaryChart(team_name_info,team_wise_salary_info , team_wise_color_code_info  ){
	if( team_wise_salary_chart != "" && team_wise_salary_chart != null ){
		team_wise_salary_chart.destroy();
 	}
 	//console.log("team_name_info");
	//console.log(team_name_info);

	//console.log("team_wise_salary_info");
	//console.log(team_wise_salary_info);
	
	var ctx = document.getElementById("team-wise-salary-chart").getContext('2d');
    var team_wise_salary_chart = new Chart(ctx, {
    	type: 'doughnut',
        data: {
            labels:team_name_info,
            datasets: [{
                label: '{{ trans("messages.salary") }}' + ' ' + '{{ config("constants.SALARY_CURRENCY_SYMBOL") }}',
                data: team_wise_salary_info,
                backgroundColor: team_wise_color_code_info,
                hoverBackgroundColor: team_wise_color_code_info,
                borderWidth: 0,
                hoverOffset: 0,
            }]

        },
        options: {
            layout: {
                padding: {
                    bottom: 0,
                },

            },
            legend: {
                display: true,
                position: 'bottom',
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