jQuery(document).ready(function($) {
	
	$( function() {
    $( "#sortable" ).sortable({
               update: function(event, ui) {
                  var productOrder = $(this).sortable('toArray').toString();
                  $("#sortable-order").text (productOrder);
               }
            });
    $( "#sortable" ).disableSelection();
  	} );
        
}); //document.ready
    
    
    
    
    