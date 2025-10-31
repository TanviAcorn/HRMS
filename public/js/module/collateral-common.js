function getSubcategory(thisitem , active_staus = null ){

	var category_id = $.trim($(thisitem).val());;

	if( category_id != "" && category_id != null ){

		$.ajax({
	        type: "POST",
	        dataType: "html",
	        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	        url: collateral_url + "getCollateralSubcategory",
	        data: {
	        	'category_id': category_id,
	        	'active_staus' : active_staus, 
	        },
	        beforeSend: function() {
				//block ui
				showLoader();
			},
	        success: function(response) {
	        	hideLoader();
	        	if( response != "" && response != null  ){
					$('.subcategory-list').html('');
					$('.subcategory-list').html(response);
					$('.collateral-sub-div').html('');
					//filterData();
		        }
	            
	        }
	    });
	}
}