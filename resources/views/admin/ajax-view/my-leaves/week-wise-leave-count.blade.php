<div class="card card-display border-0 px-0 pt-3 pb-0 h-100">
	<div class="card-body py-0">
    	<h5 class="profile-details-title">{{ trans('messages.weekly-pattern') }}</h5>
        <div class="pb-4 pt-2">
        	<canvas id="week-wise-leave-chart" height="110"></canvas>
       	</div>
    </div>
</div>
<script>
var week_wise_chart = null;

$(document).ready(function(){
	var week_wise_count_info =  [];
	<?php if( isset($weekDayWiseCount) && (!empty($weekDayWiseCount)) ) { ?>
	var week_wise_count = "<?php echo implode("," , array_values($weekDayWiseCount))?>";
	if( week_wise_count != "" && week_wise_count != null ){
		week_wise_count_info = week_wise_count.split(",");
 	}
	<?php } ?>
	createWeekWiseLeaveChart(week_wise_count_info); 
		
});

function createWeekWiseLeaveChart(chartInfo = [] ){
	if( week_wise_chart != "" && week_wise_chart != null ){
		week_wise_chart.destroy();
	}
	var ctx = document.getElementById("week-wise-leave-chart").getContext('2d');
    week_wise_chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [{
                label: '{{ trans("messages.no-of-leave-days") }}',
                data: chartInfo,
                backgroundColor: ["#07a0e3", "#22976d", "#b8c02d", "#fbc62a", "#e75e4e", "#724e8c", "#1c4d9c"],
                hoverBackgroundColor: ["#07a0e3", "#22976d", "#b8c02d", "#fbc62a", "#e75e4e", "#724e8c", "#1c4d9c"],
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