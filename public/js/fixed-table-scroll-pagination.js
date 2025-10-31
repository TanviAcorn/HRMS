$(function(){
	$('.fixed-tabel-body').scroll(function() {
		
	    //get last page value
	    let lastPage = $('#last_page').val();
	 
	    //current page value
	    let CurrentPage = $('#current_page').val();
	 
	    //inc current page value
	    CurrentPage = parseInt(CurrentPage) + 1 ;
	 
	    var scrollTop = $(window).scrollTop();
	 
	    scrollTop = parseFloat(scrollTop).toFixed(0);
	    
	    var twt_height = 0;
	    var twt_index = 1;
	   /* $(".fixed-tabel-body tbody tr").each(function(index){
	    	if( $(this).isOnScreen() != false ){
	    		twt_height = parseFloat(twt_height) + parseFloat($(this).height());
	    		twt_index++;
	    	}
	    })*/
	    
	    if(( $('.' + pagination_view_html ).height() - $('.fixed-tabel-body').scrollTop() ) <= ( $('.fixed-tabel-body').height() ) ) {
	    	
	    //if(($('.fixed-tabel-body').scrollTop() + $('.ajax-view').height()) >= ( $('.fixed-tabel-body').height() - $(".navbar").outerHeight() - 100 ) ) { 

		if(lastPage >= CurrentPage ){
		
		searchFields = searchField();
		
		searchFields.page = CurrentPage;
		
		var response = searchAjax( paginationUrl , searchFields  , true , pagination_view_html );
		   
		if( response != false ) { 
			//update current page value
			$('#current_page').val(CurrentPage);
		}
		
		
	      }
	  }

	});
})