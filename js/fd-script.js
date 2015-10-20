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
		jQuery('.onoffswitch').on('onUltimateSwitchClick',function(){
			//alert('on');
			setTimeout(function(){
				if(jQuery('#use_apikey').is(':checked')) {
					//jQuery('.ult-theme-support-row-dependant').fadeOut(200);
					jQuery( "#freshdesk_apikey" ).removeAttr("readonly");
					jQuery( "#api_username" ).attr( "readonly", "readonly" );
					jQuery( "#api_pwd" ).attr( "readonly", "readonly" );
				} else {
					//jQuery('.ult-theme-support-row-dependant').fadeIn(200);
					jQuery( "#api_username" ).removeAttr("readonly");
					jQuery( "#api_pwd" ).removeAttr("readonly");
					jQuery( "#freshdesk_apikey" ).attr( "readonly", "readonly" );
				}
			},300);
		});
		
		jQuery('.onoffswitch').click(function(){
			$switch = jQuery(this);
			setTimeout(function(){
				if($switch.find('.onoffswitch-checkbox').is(':checked'))
					$switch.find('.onoffswitch-checkbox').attr('checked',false);
				else
					$switch.find('.onoffswitch-checkbox').attr('checked',true);
				$switch.trigger('onUltimateSwitchClick');
			},300);
			
		});
	
		/*var checked_items = 0;
		var all_modules = parseInt(jQuery('#ult-all-modules-toggle').data('all'));
		if(checked_items === all_modules) {
			jQuery('#ult-all-modules-toggle').attr('checked',true);
		}
	
		jQuery('#ult-all-modules-toggle').click(function(){
			var is_check = (jQuery(this).is(':checked')) ? true : false;
			jQuery('.onoffswitch').trigger('click').find('.onoffswitch-checkbox').attr('checked',is_check);
		});*/
		
	});