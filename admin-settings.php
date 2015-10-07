<?php
class FreshDeskSettingsPage{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
	private $url_options;

    /**
     * Start up
     */
    public function __construct(){
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page(){
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'FreshDesk Settings', 
            'manage_options', 
            'fd-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page(){
        // Set class property
        $this->options = get_option( 'fd_apikey' );
		$this->options['freshdesk_url'] = rtrim( $this->options['freshdesk_url'], '/' ) . '/';
		//echo '<xmp>'; print_r($this->options); echo '</xmp>';
		$this->url_options = get_option( 'fd_url' );
		
        ?>
        <div class="wrap">
            <h2>FreshDesk Settings</h2>
			<h2 class="nav-tab-wrapper">
				<a href="javascript:void(0);" id="tab-api" class="nav-tab nav-tab-active">General Configuration</a>
				<a href="javascript:void(0);" id="tab-shortcode" class="nav-tab">Shortcode</a>
				<a href="javascript:void(0);" id="tab-url" class="nav-tab">Freshdesk SSO</a>
			</h2>
			<div id="api-tab" class="tabs">
				<form method="post" action="options.php">
					<?php
						// This prints out all hidden setting fields
						settings_fields( 'my_option_group' );   
						do_settings_sections( 'my-setting-admin' );
						submit_button();?>
				</form>
			</div>
			<div id="shortcode-tab" style="display:none;" class="tabs">
				<p class="description1">Paste the below shortcode on your page.</p>
				<code>[fetch_tickets]</code>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
				<code>[fetch_tickets atts="some_atts"]</code>
				<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>
			</div>
			<div id="url-tab" style="display:none;" class="tabs">
				<form method="post" action="options.php" id="url_form">
					<?php
						// This prints out all hidden setting fields
						settings_fields( 'url_option' );   
						do_settings_sections( 'url-admin-setting' );
						submit_button();?>
				</form>
			</div>
            
			<script type="text/javascript">
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
					//alert( jQuery('#use_apikey').val() );
				});
			</script>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init(){        
        register_setting(
            'my_option_group', // Option group
            'fd_apikey', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );
		
		add_settings_field(
            'freshdesk_url', // ID
            'FreshDesk URL', // Title 
            array( $this, 'freshdesk_url_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );

        add_settings_field(
            'freshdesk_apikey', // ID
            'FreshDesk API Key', // Title 
            array( $this, 'freshdesk_apikey_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		
		add_settings_field(
            'use_apikey', // ID
            '', // Title 
            array( $this, 'use_apikey_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		
		add_settings_field(
            'api_username', // ID
            'Username / Password', // Title 
            array( $this, 'api_username_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		
		add_settings_field(
            'api_pwd', // ID
            '', // Title 
            array( $this, 'api_pwd_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		
		register_setting(
            'url_option', // Option group
            'fd_url' // Option name
        );
		
		add_settings_section(
            'freshdesk_url_section', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'url-admin-setting' // Page
        );
		
		add_settings_field(
            'freshdesk_enable', // ID
            '', // Title 
            array( $this, 'freshdesk_enable_callback' ), // Callback
            'url-admin-setting', // Page
            'freshdesk_url_section' // Section           
        );

		
		add_settings_field(
            'freshdesk_sharedkey', // ID
            'FreshDesk Secret Shared Key', // Title 
            array( $this, 'freshdesk_sharedkey_callback' ), // Callback
            'url-admin-setting', // Page
            'freshdesk_url_section' // Section           
        );
		
		add_settings_field(
            'freshdesk_login_url', // ID
            'Remote Login URL', // Title 
            array( $this, 'freshdesk_loginurl_callback' ), // Callback
            'url-admin-setting', // Page
            'freshdesk_url_section' // Section           
        );
		
		add_settings_field(
            'freshdesk_logout_url', // ID
            'Remote Logout URL', // Title 
            array( $this, 'freshdesk_logouturl_callback' ), // Callback
            'url-admin-setting', // Page
            'freshdesk_url_section' // Section           
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ){
		//echo '<xmp>'; print_r($input); echo '</xmp>'; die;
        $new_input = array();
        if( isset( $input['freshdesk_apikey'] ) )
            $new_input['freshdesk_apikey'] = sanitize_text_field( $input['freshdesk_apikey'] );
			
		if( isset( $input['freshdesk_url'] ) )
            $new_input['freshdesk_url'] = sanitize_text_field( $input['freshdesk_url'] );
			
		if( isset( $input['freshdesk_sharedkey'] ) )
            $new_input['freshdesk_sharedkey'] = sanitize_text_field( $input['freshdesk_sharedkey'] );
			
		if( isset( $input['api_username'] ) )
            $new_input['api_username'] = sanitize_text_field( $input['api_username'] );
			
		if( isset( $input['api_pwd'] ) )
            $new_input['api_pwd'] = sanitize_text_field( $input['api_pwd'] );
			
		if( isset( $input['use_apikey'] ) )
            $new_input['use_apikey'] = sanitize_text_field( $input['use_apikey'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info(){
        //print '<p class="description">Paste the below shortcode on your page.</p><code>[fetch_tickets]</code>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function freshdesk_apikey_callback(){
        printf(
            '<input type="text" id="freshdesk_apikey" name="fd_apikey[freshdesk_apikey]" value="%s" class="regular-text" %s />',
            isset( $this->options['freshdesk_apikey'] ) ? esc_attr( $this->options['freshdesk_apikey']) : '', ( $this->options['use_apikey'] != 'on' ) ? 'readonly="readonly"' : ''
        );
		printf( '<p id="timezone-description" class="description"><strong>Where can I find my API Key?</strong><br/>You can find the API key under,<br/>"User Profile" (top right options of your helpdesk) >> "Profile Settings" >> Your API Key</p>' );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function freshdesk_sharedkey_callback(){
        printf(
            '<input type="text" id="freshdesk_sharedkey" name="fd_url[freshdesk_sharedkey]" value="%s" class="regular-text" %s />',
            isset( $this->url_options['freshdesk_sharedkey'] ) ? esc_attr( $this->url_options['freshdesk_sharedkey']) : '', ( $this->url_options['freshdesk_enable'] != 'on' ) ? 'readonly="readonly"' : ''
        );
		printf( '<p id="timezone-description" class="description">Your shared token could be obtained on the <a target="_blank" href="' . $this->options['freshdesk_url'] . 'admin/security">Account Security page</a> in the <br> Single Sign-On section.</p>' );
    }
	
	
	 /** 
     * Get the settings option array and print one of its values
     */
    public function use_apikey_callback(){
        printf(
            '<input type="checkbox" name="fd_apikey[use_apikey]" id="use_apikey" %s >Use my API key.',
            ( $this->options['use_apikey'] == 'on' ) ? 'checked="checked"' : ''
        );
		printf( '<p><strong>OR</strong></p>' );
    }
	
	
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function api_username_callback(){
        printf(
            '<input type="text" placeholder="Username" id="api_username" name="fd_apikey[api_username]" value="%s" class="regular-text" %s>',
            ( isset( $this->options['api_username'] ) && ( $this->options['use_apikey'] != 'on' ) ) ? esc_attr( $this->options['api_username']) : '', ( $this->options['use_apikey'] == 'on' ) ? 'readonly="readonly"' : ''
        );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function api_pwd_callback(){
        printf(
            '<input type="password" placeholder="Password" id="api_pwd" name="fd_apikey[api_pwd]" class="regular-text" value="%s" %s>',
            ( isset( $this->options['api_pwd'] ) && ( $this->options['use_apikey'] != 'on' ) ) ? esc_attr( $this->options['api_pwd']) : '', ( $this->options['use_apikey'] == 'on' ) ? 'readonly="readonly"' : ''
        );
    }
	
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function freshdesk_url_callback(){
        printf(
            '<input type="text" id="freshdesk_url" name="fd_apikey[freshdesk_url]" value="%s" class="regular-text" placeholder="Ex: https://your_domain_name.freshdesk.com/" />',
            isset( $this->options['freshdesk_url'] ) ? esc_attr( $this->options['freshdesk_url']) : ''
        );
		printf( '<p id="timezone-description" class="description">This is the base FreshDesk support URL.</p>' );
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function freshdesk_loginurl_callback(){
        printf(
            '<code>' . site_url() . '/wp-login.php?action=bsf-freshdesk-remote-login' . '</code>'
        );
		printf(
			'<p class="description">The settings that need to be configured in your Freshdesk account.</p>'
		);
    }
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function freshdesk_logouturl_callback(){
        printf(
            '<code>' . site_url() . '/wp-login.php?action=bsf-freshdesk-remote-logout' . '</code>'
        );
		printf(
			'<p class="description">The settings that need to be configured in your Freshdesk account.</p>'
		);
    }
	
	
	/** 
     * Get the settings option array and print one of its values
     */
    public function freshdesk_enable_callback(){
        printf(
            '<input type="checkbox" name="fd_url[freshdesk_enable]" id="freshdesk_enable" %s >Enable FreshDesk SSO',
            ( $this->url_options['freshdesk_enable'] == 'on' ) ? 'checked="checked"' : ''
        );
    }
	
}

if( is_admin() )
    $my_settings_page = new FreshDeskSettingsPage();



?>