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
		jQuery('#tab-api').click(function(){
			jQuery( '.nav-tab' ).removeClass( "nav-tab-active" );
			jQuery( this ).addClass( "nav-tab-active" );
			jQuery( '.tabs' ).hide();
			jQuery( '#api-tab' ).show();
		});
		jQuery('#tab-shortcode').click(function(){
			jQuery( '.nav-tab' ).removeClass( "nav-tab-active" );
			jQuery( this ).addClass( "nav-tab-active" );
			jQuery( '.tabs' ).hide();
			jQuery( '#shortcode-tab' ).show();
		});
		jQuery('#tab-url').click(function(){
			jQuery( '.nav-tab' ).removeClass( "nav-tab-active" );
			jQuery( this ).addClass( "nav-tab-active" );
			jQuery( '.tabs' ).hide();
			jQuery( '#url-tab' ).show();
		});
		jQuery('#tab-display').click(function(){
			jQuery( '.nav-tab' ).removeClass( "nav-tab-active" );
			jQuery( this ).addClass( "nav-tab-active" );
			jQuery( '.tabs' ).hide();
			jQuery( '#display-tab' ).show();
		});
		
	});