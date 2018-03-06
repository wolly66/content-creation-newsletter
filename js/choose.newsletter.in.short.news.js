jQuery(document).ready(function($) {
        $('select[id^=choose-newsletter-shortnews]').change(function(){

        	var id = $(this).val();

        	//alert(id);
            $.ajax({

                type: "POST",
                url: ajaxurl,
                data: "action=extract&id=" + id,
                success: function(results){
	                console.log(results);
                    $('#output_short_news').empty().append(results);

                }

            }); // Ajax Call
        }); //event handler
    }); //document.ready
