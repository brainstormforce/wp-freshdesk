<?php
/**
* Plugin Name: Freshdesk API
* Plugin URI: 
* Description: Lorem Ipsum.
* Version: 1.0
* Author: Vrunda Kansara
* Author URI: 
* License:
*/

include_once( ABSPATH . 'wp-load.php' );
if(!class_exists("FreshDeskAPI")){
	class FreshDeskAPI{
	
		private $freshdeskUrl;
		private $opt;
		private $options;
	
		/*
		 * Function Name: __construct
		 * Function Description: Constructor
		 */
		
		function __construct(){
			add_action( 'init', array( $this, 'init' ) );
			wp_enqueue_style( 'fd-style', plugins_url( "css/fd-style.css", __FILE__ ) );
			add_shortcode( "fetch_tickets", array($this, "fetch_tickets"));
			include_once( 'admin-settings.php' );
			$this->options = get_option( 'fd_url' );
			$this->opt = get_option( 'fd_apikey' );
			$this->freshdeskUrl = ( isset( $this->opt['freshdesk_url'] ) ) ? rtrim( $this->opt['freshdesk_url'], '/' ) . '/' : '';
		}
		
		
		public function init(){
		
			if ( is_user_logged_in() ) {
			
				
				// This is a login request.
				if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'bsf-freshdesk-remote-login' ) {
					// Don't waste time if remote auth is turned off.
					if ( !isset( $this->options['freshdesk_enable'] ) || $this->options['freshdesk_enable'] != 'on' ) {
						_e( 'Remote authentication is not configured yet.', 'freshdesk' );
						die();
					}
					// Filter freshdesk_return_to
					$return_to = apply_filters( 'freshdesk_return_to', $_REQUEST['host_url'] ) ;
	
					global $current_user;
					wp_get_current_user();
	
					// If the current user is logged in
					if ( 0 != $current_user->ID ) {
	
						// Pick the most appropriate name for the current user.
						if ( $current_user->user_firstname != '' && $current_user->user_lastname != '' )
							$name = $current_user->user_firstname . ' ' . $current_user->user_lastname;
						else
							$name = $current_user->display_name;
	
						// Gather more info from the user, incl. external ID
						$email = $current_user->user_email;
	
						// The token is the remote "Shared Secret" under Admin - Security - Enable Single Sign On
						$token = $this->options['freshdesk_sharedkey'];
	
						// Generate the hash as per http://www.freshdesk.com/api/remote-authentication
						$hash = md5( $name . $email . $token );
	
						// Create the SSO redirect URL and fire the redirect.
						$sso_url = trailingslashit( $this->freshdeskUrl ) . 'login/sso/?action=bsf-freshdesk-remote-login&return_to=' . urlencode( 'https://' . $return_to . '/' ) . '&name=' . urlencode( $name ) . '&email=' . urlencode( $email ) . '&hash=' . urlencode( $hash );
	
						//Hook before redirecting logged in user.
						do_action( 'freshdesk_logged_in_redirect_before' );
	
						wp_redirect( $sso_url );
	
						// No further output.
						die();
					} else {
	
						//Hook before redirecting user to login form
						do_action( 'freshdesk_logged_in_redirect_before' );
	
						// If the current user is not logged in we ask him to visit the login form
						// first, authenticate and specify the current URL again as the return
						// to address. Hopefully WordPress will understand this.
						wp_redirect( wp_login_url( wp_login_url() . '?action=bsf-freshdesk-remote-login&&return_to=' . urlencode( $return_to ) ) );
						die();
					}
				}
	
				// Is this a logout request? Errors from Freshdesk are handled here too.
				if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'bsf-freshdesk-remote-logout' ) {
	
	
					// Error processing and info messages are done here.
					$kind = isset( $_REQUEST['kind'] ) ? $_REQUEST['kind'] : 'info';
					$message = isset( $_REQUEST['message'] ) ? $_REQUEST['message'] : 'nothing';
	
					// Depending on the message kind
					if ( $kind == 'info' ) {
	
						// When the kind is an info, it probably means that the logout
						// was successful, thus, logout of WordPress too.
						wp_redirect( htmlspecialchars_decode( wp_logout_url() ) );
						die();
	
					} elseif ( $kind == 'error' ) {
						// If there was an error...
					?>
						<p><?php _e( 'Remote authentication failed: ', 'freshdesk' ); ?><?php echo $message; ?>.</p>
						<ul>
							<li><a href="<?php echo $this->freshdeskUrl; ?>"><?php _e( 'Try again', 'freshdesk' ); ?></a></li>
							<li><a href="<?php echo wp_logout_url(); ?>"><?php printf( __( 'Log out of %s', 'freshdesk' ), get_bloginfo( 'name' ) ); ?></a></li>
							<li><a href="<?php echo admin_url(); ?>"><?php printf( __( 'Return to %s dashboard', 'freshdesk' ), get_bloginfo( 'name' ) ); ?></a></li>
						</ul>
					<?php
					}
	
					// No further output.
					die();
				}
			}
		}
		
		/*
		 * Function Name: fetch_tickets
		 * Function Description: Fetched all tickets from Freshdesk for current logged in user.
		 */
		
		public function fetch_tickets(){
			$result = '';
			if ( is_user_logged_in() ) {
				global $current_user;
								
				$tickets = $this->get_tickets( $current_user->data->user_email, $current_user->roles, $_POST );
				$result .= '
				<div style="float:left;">
					<form method="post" action="" id="filter_form" name="filter_form">
						<select id="filter_dropdown" name="filter_dropdown">
							<option value="all_tickets" ';
				if( isset( $_POST["filter_dropdown"] ) ) {
					$result .= ( $_POST["filter_dropdown"] == "all_tickets" ) ? 'selected="selected"' : '';
				}
				$result .= '>----All Tickets----</option>
							<option value="Open" ';
				if( isset( $_POST["filter_dropdown"] ) ) {
					$result .= ( $_POST["filter_dropdown"] == "Open" ) ? 'selected="selected"' : '';
				}
				$result .= '>Open</option>
							<option value="Pending" ';
				if( isset( $_POST["filter_dropdown"] ) ) {
					$result .= ( $_POST["filter_dropdown"] == "Pending" ) ? 'selected="selected"' : '';
				}
				$result .= '>Pending</option>
							<option value="Resolved" ';
				if( isset( $_POST["filter_dropdown"] ) ) {
					$result .= ( $_POST["filter_dropdown"] == "Resolved" ) ? 'selected="selected"' : '';
				}
				$result .= '>Resolved</option>
							<option value="Closed" ';
				if( isset( $_POST["filter_dropdown"] ) ) {
					$result .= ( $_POST["filter_dropdown"] == "Closed" ) ? 'selected="selected"' : '';
				}
				$result .= '>Closed</option>
							<option value="Waiting on Customer" ';
				if( isset( $_POST["filter_dropdown"] ) ) {
					$result .= ( $_POST["filter_dropdown"] == "Waiting on Customer" ) ? 'selected="selected"' : '';
				}
				$result .= '>Waiting on Customer</option>
							<option value="Waiting on Third Party" ';
				if( isset( $_POST["filter_dropdown"] ) ) {
					$result .= ( $_POST["filter_dropdown"] == "Waiting on Third Party" ) ? 'selected="selected"' : '';
				}
				$txt = ( isset( $_POST['search_txt'] ) ) ? $_POST['search_txt'] : '';
				$result .= '>Waiting on Third Party</option>
						</select>
					
					</div>
					<div style="float:right;">
						<input type="text" value="' . $txt . '" id="search_txt" name="search_txt" placeholder="Search..."/>
					</div>
					<div style="clear:both;"></div>
				</form>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						tickets = ' . json_encode( $tickets, false ) . ';
						jQuery("#filter_dropdown").change(function(){
							//jQuery("#filter_form").submit();
							ajaxcall( "filter", tickets, this.value );
						});
						jQuery("#search_txt").keyup(function(e) {
							// Enter pressed?
							if( e.which == 10 || e.which == 13 )
								return false;
							if( e.which != 9 && e.which != 10 && e.which != 13 && e.which != 37 && e.which != 38 && e.which != 39 && e.which != 40 && this.value.length >= 2) {
								ajaxcall( "search", tickets, this.value );
							}
						});
					});
					function ajaxcall( action = "", tickets = "", key = "" ) {
						jQuery.ajax({
							type : "post",
							dataType : "json",
							url : "' . plugins_url( "ajax.php", __FILE__ ) . '",
							data : {action: action, tickets : tickets, key : key},
							success: function(response) {
								jQuery("#tickets_html").html( response );
							}
						});
					}
				</script>
				';
				
				if( $tickets ) {
					$result .= $this->get_html( $tickets );
				} else {
					$result .= '<p>No tickets</p>';
				}
			}
			return $result;	
		}
		
		
		/*
		 * Function Name: get_tickets
		 * Function Description: API call to Freshdesk to get all tickets of the user(email)
		 */
		
		public function get_tickets( $uemail = '', $roles = array(), $post_array = array() ){
			if( !empty( $uemail ) ){
				/*if( isset( $post_array['filter_dropdown'] ) ) {
					$filterName = ( $post_array['filter_dropdown'] == 'new_and_my_open' ) ? 'new_and_my_open' : 'all_tickets';
				} else {
					$filterName = 'all_tickets';
				}*/
				$filterName = 'all_tickets';
				if( $this->opt['use_apikey'] == 'on' ){
					$apikey = ( $this->opt['freshdesk_apikey'] != '' ) ? $this->opt['freshdesk_apikey'] : '';
					$password = "";
				} else {
					$apikey = ( $this->opt['api_username'] != '' ) ? $this->opt['api_username'] : '';
					$password = ( $this->opt['api_pwd'] != '' ) ? $this->opt['api_pwd'] : '';
				}
				
				
				
				$filter = ( !in_array( 'administrator', $roles ) ) ? '&email=' . $uemail : '';
				$url = $this->freshdeskUrl . 'helpdesk/tickets.json?filter_name=' . $filterName . $filter;
				$ch = curl_init ($url);
				curl_setopt($ch, CURLOPT_USERPWD, "$apikey:$password");
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				$server_output = curl_exec ($ch);
				curl_close ($ch);
				$tickets = json_decode( $server_output );
				if( isset( $post_array['filter_dropdown'] ) ) {
					$tickets = ( /*$post_array['filter_dropdown'] != 'new_and_my_open' &&*/ $post_array['filter_dropdown'] != 'all_tickets' ) ? $this->filter_tickets( $tickets, $post_array['filter_dropdown'] ) : $tickets ;
				}
				if( isset( $post_array['search_txt'] ) ) {
					$tickets = ( trim( $post_array['search_txt'] ) != '' ) ? $this->search_tickets( $tickets, $post_array['search_txt'] ) : $tickets ;
				}
				return $tickets;
			} else{
				return false;
			}			
		}
		
		
		/*
		 * Function Name: get_html
		 * Function Description: Returns HTML string of the tickets
		 */
		
		public function get_html( $tickets = '' ){
			$html = '';
			$tickets = json_decode( json_encode( $tickets ), FALSE );
			if( !isset( $tickets->require_login ) && $tickets != '' && !isset( $tickets->errors ) ) {
				$html .= '<div id="tickets_html"><p>Total Tickets: ' . count( $tickets ) . '</p>';
				$html .= '<ul>';
				foreach( $tickets as $d ) {
					$html .= '<li>Ticket ID: ' . $d->id . '<br/>
								Requester ID: ' . $d->requester_id . '<br/>
								Responder ID: ' . $d->responder_id . '<br/>
								Status: ' . $d->status_name . '
								<p><strong>SUBJECT: </strong>
									<a href="' . $this->freshdeskUrl . 'helpdesk/tickets/' . $d->display_id . '" target="_blank">' . $d->subject . '</a>
								</p>
								
							</li>';
				}
				$html .= '</ul><div>';
				return $html;
			} else {
				return '<div id="tickets_html"><p>Error!</p></div>';
			}
		}
		
		
		/*
		 * Function Name: filter_tickets
		 * Function Description: Filters the tickets according to ticket_status
		 */
		
		public function filter_tickets( $tickets = '', $status = '' ){
			$filtered_tickets = array();
			if( $status != 'all_tickets' ) {
				foreach( $tickets as $t ){
					if( $t['status_name'] == $status ) {
						$filtered_tickets[] = $t;
					}
				}
				return $filtered_tickets;
			} else {
				return $tickets;
			}
		}
		
		
		/*
		 * Function Name: filter_tickets
		 * Function Description: Filters the tickets according to ticket_status
		 */
		
		public function search_tickets( $tickets, $txt = '' ){
			$filtered_tickets = array();
			foreach( $tickets as $t ){
				if(  stristr( $t['subject'], trim( $txt ) ) || stristr( $t['description'], trim( $txt ) ) || stristr( $t['id'], trim( $txt ) ) ) {
					$filtered_tickets[] = $t;
				}
			}
			return $filtered_tickets;
		}
		
		
	}
} //end of class


/* Register the activation function and redirect to Setting page. */
register_activation_hook(__FILE__, 'fd_plugin_activate');
add_action('admin_init', 'fd_plugin_redirect' );

/*
 * Function Name: fd_plugin_redirect
 * Function Description:
 */
 
function fd_plugin_redirect() {
	if ( get_option( 'fd_do_activation_redirect', false ) ) {
		delete_option( 'fd_do_activation_redirect' );
		if( !isset( $_GET['activate-multi'] ) ) {
			wp_redirect( 'options-general.php?page=fd-setting-admin' );
		}
	}
}

/*
 * Function Name: fd_plugin_activate
 * Function Description:
 */

function fd_plugin_activate() {
	add_option('fd_do_activation_redirect', true);
	if( !isset( $_GET['activate-multi'] ) ) {
			wp_redirect( 'options-general.php?page=fd-setting-admin' );
		}
}

new FreshDeskAPI();
?>