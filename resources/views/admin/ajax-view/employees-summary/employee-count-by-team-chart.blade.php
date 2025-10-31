<div class="card card-display border-0 px-2 py-3 h-100">
     <div class="card-body px-2 py-0">
       <h5 class="profile-details-title">{{ trans("messages.employee-count-by-team") }}</h5>
    	<div class="pb-3 pt-3 employee-chart-no-record">
     	<canvas id="team-wise-employee-chart" height="180" style="display: none"></canvas>
     	<p class="total-record-employee-team-count mb-0 text-center" style="display: none">{{ trans("messages.no-record-found") }}</p>
  	</div>
	</div>
 </div>
 <script>
 var team_wise_chart = null;
 var team_name_record_info = [];
 var team_record_count_info = [];
 var team_chart_color = [];
 var total_number_of_record_status = true;
 var team_stage_data = <?php echo (!empty($teamWiseCountDetails) ? preg_replace('/"([^"]+)"\s*:\s*/', '$1:', json_encode($teamWiseCountDetails))  : '[]') ?>;

 $(document).ready(function() {
     var team_details = team_stage_data;
     $(team_details).each(function(index, value) {
    	 team_record_count_info.push(value.count_info);
    	 team_name_record_info.push(value.team_name);
    	 team_chart_color.push('#'+  value.color_code);
    	 
     	if(value.count_info > 0 ){
     		total_number_of_record_status = false;
     	}
     });
     if(total_number_of_record_status != true ){
  		$('#team-wise-employee-chart').show();
  		$('.total-record-employee-team-count').hide();
  	}else {
      	$('.total-record-employee-team-count').show();
      	$('#team-wise-employee-chart').hide();
     }	
    teamWiseChart(team_name_record_info , team_record_count_info , team_chart_color ); 
 });
 var back_ground_color = [];
 function teamWiseChart(team_name_record_info,team_record_count_info , team_chart_color ){
	if( team_wise_chart != "" && team_wise_chart != null ){
		team_wise_chart.destroy();
 	}
	var ctx = document.getElementById("team-wise-employee-chart").getContext('2d');
    var designation_wise_chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels:team_name_record_info,
            datasets: [{
                label:'{{ trans("messages.numbers-of-employees") }}',
                data:team_record_count_info,
                backgroundColor: team_chart_color,
                hoverBackgroundColor: team_chart_color,
                borderWidth: 0,
                hoverOffset: 0,
            }, ]

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