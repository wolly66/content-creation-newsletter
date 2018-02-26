jQuery(document).ready(function($) {
        $('select[id^=choose_newsletter_sections]').change(function(){

        	var id = $(this).val();

        	//alert(id);
            $.ajax({

                type: "POST",
                url: ajaxurl,
                data: 'action=ajax_extract_short_news&id=' + id,
                success: function(results){
                    $('#output_short_news').empty().append(results);

                }

            }); // Ajax Call
        }); //event handler
    }); //document.ready