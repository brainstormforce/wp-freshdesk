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
	});