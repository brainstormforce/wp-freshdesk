// JavaScript Document

	jQuery(document).ready(function(){						
		jQuery("#fd-filter_dropdown").change(function(){
			jQuery("#fd-filter_form").submit();
		});
		jQuery('#reset_filter').click(function(){
			jQuery('#search_txt').val('');
			jQuery("#fd-filter_form").submit();
		});
		jQuery('#button-holder').click(function(){
			jQuery("#fd-filter_form").submit();
		});
		jQuery('#new_ticket').click(function(){
			var url = jQuery('#hidden_new_ticket_url').val();
			alert(url);
			window.open( url, '_blank' ); 
		});
	});