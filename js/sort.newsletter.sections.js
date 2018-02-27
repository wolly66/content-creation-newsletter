jQuery(document).ready(function($) {
	
	$( function() {
    $( "#sortable" ).sortable({
               update: function(event, ui) {
                  var productOrder = $(this).sortable('toArray').toString();
                  $("#sortable-order").text (productOrder);
               }
            });
    $("#sortable").sortable({
    placeholder: "ui-state-highlight",
    helper: 'clone',
    sort: function(e, ui) {
        $(ui.placeholder).html(Number($("#sortablev > li:visible").index(ui.placeholder)) + 1);
    },
    update: function(event, ui) {
        var $lis = $(this).children('li');
        $lis.each(function() {
            var $li = $(this);
            var newVal = $(this).index() + 1;
            $(this).children('.sortable-number').html(newVal);
            $(this).children('#item_display_order').val(newVal);
        });
    }
});
    $( "#sortable" ).disableSelection();
  	} );
        
}); //document.ready
    
    
    
    
    
    