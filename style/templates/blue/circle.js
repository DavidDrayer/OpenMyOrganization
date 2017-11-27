    function resizeBeamer() {

        $('head').append( $('<link title="beamer" rel="stylesheet" type="text/css" />').attr('href', '/style/beamer.css') );
	}
	
	$(document).ready(function(){
 		var oldsize=$( window ).width();
 		if ($( window ).width()<1200) resizeBeamer();
	 	$( window ).resize(function() {	 		
	 		if ($( window ).width()<1280 && oldsize>=1280) {
		    	resizeBeamer();

		    }
	 		if ($( window ).width()>=1280 && oldsize<1280) {
		    	$('link[title=beamer]')[0].remove();
				$("#tabs-1").html($("#tabs-1").html());
		    }
		    oldsize=$( window ).width();
	    });
    });