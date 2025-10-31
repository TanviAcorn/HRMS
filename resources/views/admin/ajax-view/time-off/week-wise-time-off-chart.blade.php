<div class="card card-display border-0 px-0 pt-3 pb-0 h-100">
	<div class="card-body py-0">
		<h5 class="profile-details-title" id="exampleModalLabel">{{ trans('messages.weekly-pattern') }}</h5>
    	<div class="pb-4 pt-2">
        	<canvas id="week-wise-time-off-chart" height="110"></canvas>
       	</div>
	</div>
</div>
<script>
var week_wise_time_off_chart = null;

$(document).ready(function(){
	var week_wise_time_off_count_info =  [];
	<?php if( isset($weekWiseTimeOffCountDetails) && (!empty($weekWiseTimeOffCountDetails)) ) { ?>
	var week_wise_time_off_count = "<?php echo implode("," , array_values($weekWiseTimeOffCountDetails))?>";
	if( week_wise_time_off_count != "" && week_wise_time_off_count != null ){
		week_wise_time_off_count_info = week_wise_time_off_count.split(",");
 	}
	<?php } ?>
	weekWiseTimeOffChart(week_wise_time_off_count_info); 
		
});
function weekWiseTimeOffChart(chartInfo = [] ){

	if( week_wise_time_off_chart != "" && week_wise_time_off_chart != null ){
		week_wise_time_off_chart.destroy();
	}
	
	var ctx = document.getElementById("week-wise-time-off-chart").getContext('2d');
    week_wise_time_off_chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [{
                label: '{{ trans("messages.no-of-time-off-days") }}',
                data: chartInfo ,
                backgroundColor: ["#07a0e3", "#22976d", "#b8c02d", "#fbc62a", "#e75e4e", "#724e8c", "#1c4d9c"],
                hoverBackgroundColor: ["#07a0e3", "#22976d", "#b8c02d", "#fbc62a", "#e75e4e", "#724e8c", "#1c4d9c"],
            }]
        },
        options: {
            layout: {
                padding: {
                    bottom: 10
                }
            },
            legend: {
                display: false,
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    }

                }],
                yAxes: [{
                    ticks: {
                        display: false,
                        max: 10,
                        beginAtZero: true,
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false,
                    }
                }],
            }
        }
    });
	
}
</script>