<div class="card barChart-month card-display border-0 px-0 pt-3 pb-0 h-100">
  	<div class="card-body  py-0">
    	<h5 class="profile-details-title" >{{ trans('messages.monthly-stats') }}</h5>
        <div class="pb-4 pt-2">
        	<canvas id="month-wise-leave-chart" height="50"></canvas>
    	</div>
	</div>
</div>
<script>
var month_wise_chart = null;

$(document).ready(function(){
	var month_wise_count_info =  [];
	<?php if( isset($monthWiseCount) && (!empty($monthWiseCount)) ) { ?>
	var month_wise_count = "<?php echo implode("," , array_values($monthWiseCount))?>";
	if( month_wise_count != "" && month_wise_count != null ){
		month_wise_count_info = month_wise_count.split(",");
 	}
	<?php } ?>
	monthWiseChart(month_wise_count_info); 
});

function monthWiseChart(chartInfo = [] ){
	if( month_wise_chart != "" && month_wise_chart != null ){
		month_wise_chart.destroy();
	}
	//console.log(chartInfo);
	 var ctx = document.getElementById("month-wise-leave-chart").getContext('2d');
     month_wise_chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", ],
            datasets: [{
                label: '{{ trans("messages.no-of-leave-days") }}',
                data: chartInfo,
                backgroundColor: month_wise_chart_colors,
                hoverBackgroundColor: month_wise_chart_colors
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