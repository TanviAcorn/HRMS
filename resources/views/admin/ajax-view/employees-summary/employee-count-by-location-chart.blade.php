<div class="card card-display border-0 px-2 py-3 h-100">
   <div class="card-body px-2 py-0">
    <h5 class="profile-details-title">{{ trans("messages.employee-count-by-city") }}</h5>
    <div class="pb-3 pt-3 employee-chart-no-record">
        <canvas id="location-wise-employee-chart" height="180" style="display: none"></canvas>
        <p class="total-location-count mb-0 text-center" style="display: none">{{ trans("messages.no-record-found") }}</p>
     </div>
   </div>
</div>
<script>
 var location_wise_chart = null;
 var location_name_record_info = [];
 var location_record_count_info = [];
 var location_color_code_info = [];
 var number_of_record_status = true;
 var location_stage_data = <?php echo (!empty($cityWiseCountDetails) ? preg_replace('/"([^"]+)"\s*:\s*/', '$1:', json_encode($cityWiseCountDetails))  : '[]') ?>;
 
 $(document).ready(function() {
	 var location_details = location_stage_data;
	  $(location_details).each(function(index, value) {
		location_record_count_info.push(value.count_info);
	    location_name_record_info.push(value.city_name);
	    location_color_code_info.push('#' + value.color_code);
	    if(value.count_info > 0 ){
	    	number_of_record_status = false;
     	}
	 });
	  
	 if(number_of_record_status != true ){
	  	$('#location-wise-employee-chart').show();
	  	$('.total-location-count').hide();
  	}else {
      	$('.total-location-count').show();
      	$('#location-wise-employee-chart').hide();
      }	
	 locationWiseChart(location_name_record_info , location_record_count_info , location_color_code_info ); 
 });
 
 var back_ground_color = [];
  function locationWiseChart(location_name_record_info,location_record_count_info , location_color_code_info ){
	if( location_wise_chart != "" && location_wise_chart != null ){
		location_wise_chart.destroy();
 	}
	var ctx = document.getElementById("location-wise-employee-chart").getContext('2d');
    location_wise_chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels:location_name_record_info,
            datasets: [{
                label:'{{ trans("messages.numbers-of-employees") }}',
                data:location_record_count_info,
                backgroundColor: location_color_code_info ,
                hoverBackgroundColor: location_color_code_info,
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
      		
