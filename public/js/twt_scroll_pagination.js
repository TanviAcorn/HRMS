$(window).scroll(function() {
   
    //get last page value
    let lastPage = $('#last_page').val();
 
    //current page value
    let CurrentPage = $('#current_page').val();
 
    //inc current page value
    CurrentPage = parseInt(CurrentPage) + 1 ;
 
    var scrollTop = $(window).scrollTop();
 
    scrollTop = parseFloat(scrollTop).toFixed(0);
 
    if(($(window).scrollTop() + $(window).height()) >= ( $(document).height() - $(".navbar").outerHeight() - 100 ) ) { 

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

