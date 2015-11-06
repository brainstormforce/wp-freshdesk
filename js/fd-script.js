// JavaScript Document

	jQuery(document).ready(function(){
		jQuery('#use_apikey').change(function(){
			if( jQuery("#use_apikey").is(':checked') ) {
				jQuery( "#freshdesk_apikey" ).removeAttr("readonly");
				jQuery( "#api_username" ).attr( "readonly", "readonly" );
				jQuery( "#api_pwd" ).attr( "readonly", "readonly" );
			} else {
				jQuery( "#api_username" ).removeAttr("readonly");
				jQuery( "#api_pwd" ).removeAttr("readonly");
				jQuery( "#freshdesk_apikey" ).attr( "readonly", "readonly" );
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