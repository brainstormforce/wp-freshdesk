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



    /*
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



    /*
     * Options page callback
     */
    public function create_admin_page(){
	
        // Set class property
        $this->options = get_option( 'fd_apikey' );
		if( $this->options ){
			$this->options['freshdesk_url'] = ( isset( $this->options['freshdesk_url'] ) ) ? rtrim( $this->options['freshdesk_url'], '/' ) . '/' : '';
		}
		$this->url_options = get_option( 'fd_url' );
		$this->display_option = get_option( 'fd_display' );
        ?>
        <div class="wrap">
            <div class="bend-heading-section">
				<h1>FreshDesk Settings</h1>
				<h3>Now your users won't have to remember one more username and password! Configure your Wordpress website and Freshdesk to work together to give your users Freshdesk Remote Authentication!</h3>
			</div>
			
			<h2 class="nav-tab-wrapper">
				<a href="javascript:void(0);" id="tab-api" class="nav-tab nav-tab-active">General Configuration</a>
				<a href="javascript:void(0);" id="tab-shortcode" class="nav-tab">Shortcode</a>
				<a href="javascript:void(0);" id="tab-url" class="nav-tab">Freshdesk SSO</a>
				<a href="javascript:void(0);" id="tab-display" class="nav-tab">Display Settings</a>
			</h2>
			<div id="api-tab" class="tabs">
				<form method="post" action="options.php" autocomplete="off">
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
				<p>This shortcode will display all the tickets on your page. It also provides filter options and search options. You can filter tickets with respect to:</p>
				<table>
					<tr>
						<td>All tickets</td>
						<td><code>[fetch_tickets]</code></td>
					</tr>
					<tr>
						<td>Open</td>
						<td><code>[fetch_tickets filter="Open"]</code></td>
					</tr>
					<tr>
						<td>Resolved</td>
						<td><code>[fetch_tickets filter="Resolved"]</code></td>
					</tr>
					<tr>
						<td>Closed</td>
						<td><code>[fetch_tickets filter="Closed"]</code></td>
					</tr>
					<tr>
						<td>Pending</td>
						<td><code>[fetch_tickets filter="Pending"]</code></td>
					</tr>
					<tr>
						<td>Waiting on Customer</td>
						<td><code>[fetch_tickets filter="Waiting on Customer"]</code></td>
					</tr>
					<tr>
						<td>Waiting on Third Party</td>
						<td><code>[fetch_tickets filter="Waiting on Third Party"]</code></td>
					</tr>
				</table>
			</div>
			<div id="url-tab" style="display:none;" class="tabs">
				<form method="post" action="options.php" id="url_form" autocomplete="off">
					<?php
						// This prints out all hidden setting fields
						settings_fields( 'url_option' );   
						do_settings_sections( 'url-admin-setting' );
						submit_button();?>
				</form>
			</div>
			<div id="display-tab" style="display:none;" class="tabs">
				<form method="post" action="options.php" id="display_form" autocomplete="off">
					<?php
						// This prints out all hidden setting fields
						settings_fields( 'display_option' );   
						do_settings_sections( 'display-admin-setting' );
						submit_button();?>
				</form>
			</div>
        </div>
        <?php
    }


    /*
     * Register and add settings
     */
    public function page_init(){
	
		//Enqueue all styles and scripts.
		//wp_enqueue_style( 'fd-style', plugins_url( "css/fd-style.css", __FILE__ ) );
		//wp_enqueue_script( 'fd-script', plugins_url( "js/fd-script.js", __FILE__ ) );
		
		wp_register_script( 'fd-script', plugins_url('js/fd-script.js', __FILE__), array('jquery'), '1.1', true );
		wp_enqueue_script( 'fd-script' );
		
		wp_register_style( 'fd-style', plugins_url('css/fd-style.css', __FILE__) );
		wp_enqueue_style( 'fd-style' );
		
		// Register the setting tab
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
            'Base freshdesk URL', // Title 
            array( $this, 'freshdesk_url_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );

        add_settings_field(
            'freshdesk_apikey', // ID
            'API Key', // Title 
            array( $this, 'freshdesk_apikey_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		
		add_settings_field(
            'use_apikey', // ID
            'Use only API key?', // Title 
            array( $this, 'use_apikey_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		
		add_settings_field(
            'api_username', // ID
            'Username', // Title 
            array( $this, 'api_username_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		
		add_settings_field(
            'api_pwd', // ID
            'Password', // Title 
            array( $this, 'api_pwd_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		
		add_settings_field(
            'no_tickets_msg', // ID
            'No Tickets Error Message', // Title 
            array( $this, 'no_tickets_msg_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		
		// Register the setting tab		
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
            'Enable SSO', // Title 
            array( $this, 'freshdesk_enable_callback' ), // Callback
            'url-admin-setting', // Page
            'freshdesk_url_section' // Section           
        );

		
		add_settings_field(
            'freshdesk_sharedkey', // ID
            'Secret Shared Key', // Title 
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
		
		// Register the display setting tab		
		register_setting(
            'display_option', // Option group
            'fd_display' // Option name
        );
		
		add_settings_section(
            'freshdesk_display_section', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'display-admin-setting' // Page
        );
		/*
		add_settings_field(
            'fd_display_display_id', // ID
            'Ticket ID', // Title 
            array( $this, 'fd_display_display_id_callback' ), // Callback
            'display-admin-setting', // Page
            'freshdesk_display_section' // Section           
        );*/
		
		add_settings_field(
            'fd_display_description', // ID
            'Description', // Title 
            array( $this, 'fd_display_description_callback' ), // Callback
            'display-admin-setting', // Page
            'freshdesk_display_section' // Section           
        );
		
		add_settings_field(
            'fd_display_priority_name', // ID
            'Priority', // Title 
            array( $this, 'fd_display_priority_name_callback' ), // Callback
            'display-admin-setting', // Page
            'freshdesk_display_section' // Section           
        );
		
		add_settings_field(
            'fd_display_updated_at', // ID
            'Updated Date', // Title 
            array( $this, 'fd_display_updated_at_callback' ), // Callback
            'display-admin-setting', // Page
            'freshdesk_display_section' // Section           
        );
		
		/*add_settings_field(
            'fd_display_subject', // ID
            'Subject', // Title 
            array( $this, 'fd_display_subject_callback' ), // Callback
            'display-admin-setting', // Page
            'freshdesk_display_section' // Section           
        );
		
		add_settings_field(
            'fd_display_status_name', // ID
            'Status', // Title 
            array( $this, 'fd_display_status_name_callback' ), // Callback
            'display-admin-setting', // Page
            'freshdesk_display_section' // Section           
        );*/
    }
	

    /*
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ){
	
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
			
		if( isset( $input['no_tickets_msg'] ) )
            $new_input['no_tickets_msg'] = sanitize_text_field( $input['no_tickets_msg'] );
			
		if( isset( $input['fd_display_description'] ) )
            $new_input['fd_display_description'] = sanitize_text_field( $input['fd_display_description'] );
			
		if( isset( $input['fd_display_priority_name'] ) )
            $new_input['fd_display_priority_name'] = sanitize_text_field( $input['fd_display_priority_name'] );
			
		if( isset( $input['fd_display_updated_at'] ) )
            $new_input['fd_display_updated_at'] = sanitize_text_field( $input['fd_display_updated_at'] );

        return $new_input;
    }


    /* 
     * Print the Section text
     */
    public function print_section_info(){
        //Nothing to do here
    }
	
	

    /*
     * Callback function for "FreshDesk API Key"
     */
    public function freshdesk_apikey_callback(){
		$val1 = $val2 = '';
		if( isset( $this->options['freshdesk_apikey'] ) ) {
			$val1 = esc_attr( $this->options['freshdesk_apikey']);
		} else {
			$val1 = '';
		}
		if( isset( $this->options['use_apikey'] ) ) {
			$val2 = ( $this->options['use_apikey'] != 'on' ) ? 'readonly="readonly"' : '';
		} else {
			$val2 = 'readonly="readonly"';
		}
        printf(
            '<input autocomplete="off" type="text" id="freshdesk_apikey" name="fd_apikey[freshdesk_apikey]" value="%s" class="regular-text" %s />', $val1, $val2
        );
		printf( '<p id="timezone-description" class="description"><strong>Where can I find my API Key?</strong><br/>You can find the API key under,<br/>"User Profile" (top right options of your helpdesk) >> "Profile Settings" >> Your API Key</p>' );
    }
	
	
	
	/*
     * Callback function for "FreshDesk Shared Secret Key"
     */
    public function freshdesk_sharedkey_callback(){
		$val1 = $val2 = '';
		if( isset( $this->url_options['freshdesk_sharedkey'] ) ) {
			$val1 = esc_attr( $this->url_options['freshdesk_sharedkey']);
		} else {
			$val1 = '';
		}
		if( isset( $this->url_options['freshdesk_enable'] ) ) {
			$val2 = '';
		} else {
			$val2 = 'readonly="readonly"';
		}
        printf(
            '<input autocomplete="off" type="text" id="freshdesk_sharedkey" name="fd_url[freshdesk_sharedkey]" value="%s" class="regular-text" %s />', $val1, $val2
        );
		printf( '<p id="timezone-description" class="description">Your shared token could be obtained on the <a target="_blank" href="%sadmin/security">Account Security page</a> in the <br> Single Sign-On >> "Simple SSO" section.</p>', ( isset( $this->options['freshdesk_url'] ) ) ? $this->options['freshdesk_url'] : '' );
    }
	
	
	
	 /*
     * Callback function for "FreshDesk Admin Username"
     */
    public function use_apikey_callback(){
		$val = '';
		if( isset( $this->options['use_apikey'] ) ) {
			$val = ( $this->options['use_apikey'] == 'on' ) ? 'checked="checked"' : '';
		} else {
			$val = '';
		}
		if( !$this->options ){
			$val = 'checked="checked"';
		}
        printf(
				'<div class="onoffswitch">
					<input type="checkbox" name="fd_apikey[use_apikey]" class="onoffswitch-checkbox" id="use_apikey" style="display:none;" %s>
					<label class="onoffswitch-label" for="use_apikey">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>', $val
        );
		printf( '<p><strong>OR</strong></p>' );
    }
	
	
	/*
     * Callback function for "FreshDesk Admin Username"
     */
    public function api_username_callback(){
		$val1 = $val2 = '';
		if( !isset( $this->options['use_apikey'] ) ) {
			if( isset( $this->options['api_username'] ) ) {
				$val1 = esc_attr( $this->options['api_username'] );
				$val2 = '';
			}
		} else {
			$val1 = '';
			$val2 = 'readonly="readonly"';
		}
        printf(
            '<input type="text" autocomplete="off" placeholder="Username" id="api_username" name="fd_apikey[api_username]" value="%s" class="regular-text" %s>', $val1, $val2
        );
    }
	
	
	
	/*
     * Callback function for "FreshDesk Admin Password"
     */
    public function api_pwd_callback(){
		$val1 = $val2 = '';
		if( !isset( $this->options['use_apikey'] ) ) {
			if( isset( $this->options['api_pwd'] ) ) {
				$val1 = esc_attr( $this->options['api_pwd'] );
				$val2 = '';
			}
			
		} else {
			$val1 = '';
			$val2 = 'readonly="readonly"';
		}
        printf(
            '<input type="password" autocomplete="off" placeholder="Password" id="api_pwd" name="fd_apikey[api_pwd]" class="regular-text" value="%s" %s>', $val1, $val2
        );
    }
	
	
	
	/* 
     * Callback function for "FreshDesk URL"
     */
    public function freshdesk_url_callback(){
		$val = '';
		if( isset( $this->options['freshdesk_url'] ) && strlen( $this->options['freshdesk_url'] ) > 5 ) {
			$val = esc_attr( $this->options['freshdesk_url']);
		} else {
			$val = '';
		}
        printf(
            '<input type="text" autocomplete="off" id="freshdesk_url" name="fd_apikey[freshdesk_url]" value="%s" class="regular-text" placeholder="Ex: https://your_domain_name.freshdesk.com/" />', $val
        );
		printf( '<p id="timezone-description" class="description">This is the base FreshDesk support URL.</p>' );
    }
	
	
	
	/* 
     * Callback function for "Login URL" for SSO
     */
    public function freshdesk_loginurl_callback(){
        printf(
            '<code>' . site_url() . '/wp-login.php?action=bsf-freshdesk-remote-login' . '</code>'
        );
		printf(
			'<p class="description">The settings that need to be configured in your Freshdesk account.</p>'
		);
    }
	
	
	/*
     * Callback function for "Logout URL" for SSO
     */
    public function freshdesk_logouturl_callback(){
		$val = '';
		if(  isset( $this->options['freshdesk_url'] ) && strlen( $this->options['freshdesk_url'] ) > 5 ) {
			$val = $this->options['freshdesk_url'];
		} else {
			$val = 'https://your_domain.freshdesk.com/';
		}
        printf(
            '<code>' . site_url() . '/wp-login.php?action=bsf-freshdesk-remote-logout' . '</code>'
        );
		printf(
			'<p class="description">The settings that need to be configured in your Freshdesk account.</p><br/>
			<p class="description">Remember that you can always go to:
<a href="%slogin/normal" target="_blank">%saccess/normal</a><br/>
			to use the regular login in case you get unlucky and somehow lock yourself out of Freshdesk. </p>', $val, $val
		);
    }
	
	
	/*
     * Callback function for "Enable SSO" checkbox
     */
    public function freshdesk_enable_callback(){
		$val = '';
		if( isset( $this->url_options['freshdesk_enable'] ) ){
			$val = ( $this->url_options['freshdesk_enable'] == 'on' ) ? 'checked="checked"' : '';
		} else {
			$val = '';
		}
        printf(
            	'<div class="onoffswitch">
					<input type="checkbox" name="fd_url[freshdesk_enable]" class="onoffswitch-checkbox" id="freshdesk_enable" style="display:none;" %s>
					<label class="onoffswitch-label" for="freshdesk_enable">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>',$val 
        );
    }
	
	
	public function no_tickets_msg_callback(){
		$val = '';
		if( isset( $this->options['no_tickets_msg'] ) ){
			$val = ( $this->options['no_tickets_msg'] != '' ) ? $this->options['no_tickets_msg'] : '';
		}
        printf(
            '<input type="text" autocomplete="off" placeholder="Eg: Sorry! No Tickets!" id="no_tickets_msg" name="fd_apikey[no_tickets_msg]" value="%s" class="regular-text">', $val
        );
	}
	
	/*public function fd_display_display_id_callback(){
		$val = ( isset( $this->display_option['fd_display_display_id'] ) ) ? 'checked="checked"' : '';
		printf(
            	'<div class="onoffswitch">
					<input type="checkbox" name="fd_display[fd_display_display_id]" class="onoffswitch-checkbox" id="fd_display_display_id" style="display:none;" %s>
					<label class="onoffswitch-label" for="fd_display_display_id">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>',$val 
        );
	}*/
	
	public function fd_display_description_callback(){
		$val = ( isset( $this->display_option['fd_display_description'] ) ) ? 'checked="checked"' : '';
		printf(
            	'<div class="onoffswitch">
					<input type="checkbox" name="fd_display[fd_display_description]" class="onoffswitch-checkbox" id="fd_display_description" style="display:none;" %s>
					<label class="onoffswitch-label" for="fd_display_description">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>',$val 
        );
	}
	
	/*public function fd_display_subject_callback(){
		$val = ( isset( $this->display_option['fd_display_subject'] ) ) ? 'checked="checked"' : '';
		printf(
            	'<div class="onoffswitch">
					<input type="checkbox" name="fd_display[fd_display_subject]" class="onoffswitch-checkbox" id="fd_display_subject" style="display:none;" %s>
					<label class="onoffswitch-label" for="fd_display_subject">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>',$val 
        );
	}*/
	
	/*public function fd_display_status_name_callback(){
		$val = ( isset( $this->display_option['fd_display_status_name'] ) ) ? 'checked="checked"' : '';
		printf(
            	'<div class="onoffswitch">
					<input type="checkbox" name="fd_display[fd_display_status_name]" class="onoffswitch-checkbox" id="fd_display_status_name" style="display:none;" %s>
					<label class="onoffswitch-label" for="fd_display_status_name">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>',$val 
        );
	}*/
	
	public function fd_display_priority_name_callback(){
		$val = ( isset( $this->display_option['fd_display_priority_name'] ) ) ? 'checked="checked"' : '';
		printf(
            	'<div class="onoffswitch">
					<input type="checkbox" name="fd_display[fd_display_priority_name]" class="onoffswitch-checkbox" id="fd_display_priority_name" style="display:none;" %s>
					<label class="onoffswitch-label" for="fd_display_priority_name">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>',$val 
        );
	}
	
	
	public function fd_display_updated_at_callback(){
		$val = ( isset( $this->display_option['fd_display_updated_at'] ) ) ? 'checked="checked"' : '';
		printf(
            	'<div class="onoffswitch">
					<input type="checkbox" name="fd_display[fd_display_updated_at]" class="onoffswitch-checkbox" id="fd_display_updated_at" style="display:none;" %s>
					<label class="onoffswitch-label" for="fd_display_updated_at">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>',$val 
        );
	}
	
}

if( is_admin() )
    new FreshDeskSettingsPage();



?>