// JavaScript Document
	function ajaxcall( action, key ) {
		var data = jQuery("#filter_form").serialize();
		jQuery.ajax({
			type : "post",
			dataType : "html",
			url : ajaxurl,
			data : data,
			success: function(response) {
				jQuery("#tickets_html").html( response );
				jQuery("#fd-dark-bg").hide();
			}
		});
		//jQuery("#fd-dark-bg").hide();
	}
	jQuery(document).ready(function(){
		var call_ajax_flag = jQuery('#call_ajax_flag').val();
		jQuery("#filter_dropdown").change(function(){
			//jQuery("#filter_form").submit();
			if( call_ajax_flag == 1 ) {
				jQuery("#fd-dark-bg").show();
				ajaxcall( "filter", this.value );
			}
		});
		jQuery("#search_txt").on( "keyup keypress", function(e) {
			// Enter pressed?
			if( e.keyCode  == 10 || e.keyCode == 13 ) {
				//alert("enter");
				e.preventDefault();
				return false;
			}
			if( e.which != 9 && e.which != 10 && e.which != 13 && e.which != 37 && e.which != 38 && e.which != 39 && e.which != 40 && this.value.length >= 2) {
				if( call_ajax_flag == 1 ) {
					ajaxcall( "search", this.value );
				}
			}
		});
	});