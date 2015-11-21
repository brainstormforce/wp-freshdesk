// JavaScript Document

	jQuery(document).ready(function(){
		var hashTxt = window.location.hash.substr(1);
		if( hashTxt == '' ) {
			hashTxt = 'api-tab';
			window.location.hash = hashTxt;
		}
		//alert( hashTxt );
		var arr = hashTxt.split('-');
		//alert('#' + arr[1] + '-' + arr[0]);
		jQuery( '.nav-tab' ).removeClass( "nav-tab-active" );
		jQuery( '#' + arr[1] + '-' + arr[0] ).addClass( "nav-tab-active" );
		jQuery( '.fd-tabs' ).hide();
		jQuery( '#' + hashTxt ).show();
		jQuery('#' + hashTxt + ' form').attr('action', 'options.php#' + hashTxt);
		
		if( jQuery("#use_apikey").val() == 'on' ) {
			jQuery( "#api_username" ).parent().parent().hide();
			jQuery( "#api_pwd" ).parent().parent().hide();
			jQuery( "#freshdesk_apikey" ).parent().parent().show();
		} else {
			jQuery( "#freshdesk_apikey" ).parent().parent().hide();
			jQuery( "#api_username" ).parent().parent().show();
			jQuery( "#api_pwd" ).parent().parent().show();
		}
		jQuery('#api-tab').submit(function(){
			if(/^[A-Za-z\d\s]+$/.test(jQuery("#freshdesk_url").val())){
				return true;
			} else {
				alert("Invalid URL");
				return false;
			}
		});
		jQuery('#use_apikey').change(function(){
			if( jQuery("#use_apikey").val() == 'on' ) {
				jQuery( "#freshdesk_apikey" ).removeAttr("readonly");
				jQuery( "#api_username" ).attr( "readonly", "readonly" );
				jQuery( "#api_pwd" ).attr( "readonly", "readonly" );
				jQuery( "#api_username" ).parent().parent().hide();
				jQuery( "#api_pwd" ).parent().parent().hide();
				jQuery( "#freshdesk_apikey" ).parent().parent().show();
			} else {
				jQuery( "#api_username" ).removeAttr("readonly");
				jQuery( "#api_pwd" ).removeAttr("readonly");
				jQuery( "#freshdesk_apikey" ).attr( "readonly", "readonly" );
				jQuery( "#freshdesk_apikey" ).parent().parent().hide();
				jQuery( "#api_username" ).parent().parent().show();
				jQuery( "#api_pwd" ).parent().parent().show();
			}
		});
		jQuery('#freshdesk_enable').change(function(){
			if( jQuery("#freshdesk_enable").is(':checked') ) {
				jQuery( "#freshdesk_sharedkey" ).removeAttr("readonly");
			} else {
				jQuery( "#freshdesk_sharedkey" ).attr( "readonly", "readonly" );
			}
		});
		jQuery('.nav-tab').click(function(){
			var id = jQuery(this).attr('id');
			var arr = id.split('-');
			jQuery( '.nav-tab' ).removeClass( "nav-tab-active" );
			jQuery( this ).addClass( "nav-tab-active" );
			jQuery( '.fd-tabs' ).hide();
			jQuery( '#' + arr[1] + '-' + arr[0] ).show();
			window.location.hash = arr[1] + '-' + arr[0];
			jQuery('#' + arr[1] + '-' + arr[0] + ' form').attr('action', 'options.php#' + arr[1] + '-' + arr[0]);
		});
		
		jQuery('.fd-toggle').click(function(){
			var id = jQuery(this).attr('id');
			if( jQuery("#"+id).is(':checked') ) {
				jQuery( "#"+id+'-p' ).removeClass( "fd-use-apikey-no" );
				jQuery( "#"+id+'-p' ).addClass( "fd-use-apikey-yes" );
				jQuery( "#"+id+'-p' ).html( "Yes" );
			} else {
				jQuery( "#"+id+'-p' ).removeClass( "fd-use-apikey-yes" );
				jQuery( "#"+id+'-p' ).addClass( "fd-use-apikey-no" );
				jQuery( "#"+id+'-p' ).html( "No" );
			}
		});
		
	});