<div class="card barChart-month card-display border-0 px-0 pt-3 pb-0 h-100">
	<div class="card-body py-0">
    	<h5 class="profile-details-title" id="exampleModalLabel">{{ trans('messages.monthly-stats') }}</h5>
        <div class="pb-4 pt-2">
        	<canvas id="month-wise-time-off-chart" height="50"></canvas>
      	</div>
  	</div>
</div>
<script>
var month_wise_time_off_chart = null;

$(document).ready(function(){
	var month_wise_time_off_count_info =  [];
	<?php if( isset($monthWiseTimeOffCountDetails) && (!empty($monthWiseTimeOffCountDetails)) ) { ?>
	var month_wise_time_off_count = "<?php echo implode("," , array_values($monthWiseTimeOffCountDetails))?>";
	if( month_wise_time_off_count != "" && month_wise_time_off_count != null ){
		month_wise_time_off_count_info = month_wise_time_off_count.split(",");
 	}
	<?php } ?>
	monthWiseTimeOffChart(month_wise_time_off_count_info); 
		
});
function monthWiseTimeOffChart(chartInfo = [] ){

	if( month_wise_time_off_chart != "" && month_wise_time_off_chart != null ){
		month_wise_time_off_chart.destroy();
	}

	var ctx = document.getElementById("month-wise-time-off-chart").getContext('2d');
    month_wise_time_off_chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", ],
            datasets: [{
                label: '{{ trans("messages.no-of-time-off-days") }}',
                data: chartInfo,
                backgroundColor: ["#0a79be", "#07a0e3", "#22976d", "#75b052", "#b8c02d", "#fbc62a", "#f08340", "#e75e4e", "#e4416c", "#b54b8b", "#724e8c", "#1c4d9c"],
                hoverBackgroundColor: ["#0a79be", "#07a0e3", "#22976d", "#75b052", "#b8c02d", "#fbc62a", "#f08340", "#e75e4e", "#e4416c", "#b54b8b", "#724e8c", "#1c4d9c"]
            }, ]
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
                        max: 20,
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