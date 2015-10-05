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
	
		/*
		 * Function Name: __construct
		 * Function Description: Constructor
		 */
		
		function __construct(){
			add_shortcode( "fetch_tickets", array($this, "fetch_tickets"));
			include_once( 'admin-settings.php' );
			$options = get_option( 'fd_url' );
			$this->freshdeskUrl = rtrim( $options['freshdesk_url'], '/' ) . '/';
		}
		
		
		/*
		 * Function Name: fetch_tickets
		 * Function Description: Fetched all tickets from Freshdesk for current logged in user.
		 */
		
		function fetch_tickets(){
			$result = '';
			if ( is_user_logged_in() ) {
				global $current_user;
				$tickets = $this->get_tickets( $current_user->data->user_email, $current_user->roles, $_POST );
				//echo '<xmp>'; print_r($tickets); echo '</xmp>';
				$result .= '
				<div style="float:left;">
					<form method="post" action="" id="filter_form" name="filter_form">
						<select id="filter_dropdown" name="filter_dropdown">
							<option value="all_tickets" ';
				$result .= ( $_POST["filter_dropdown"] == "all_tickets" ) ? 'selected="selected"' : '';
				$result .= '>----All Tickets----</option>
							<option value="new_and_my_open" ';
				$result .= ( $_POST["filter_dropdown"] == "new_and_my_open" ) ? 'selected="selected"' : '';
				$result .= '>Open</option>
							<option value="Pending" ';
				$result .= ( $_POST["filter_dropdown"] == "Pending" ) ? 'selected="selected"' : '';
				$result .= '>Pending</option>
							<option value="Resolved" ';
				$result .= ( $_POST["filter_dropdown"] == "Resolved" ) ? 'selected="selected"' : '';
				$result .= '>Resolved</option>
							<option value="Closed" ';
				$result .= ( $_POST["filter_dropdown"] == "Closed" ) ? 'selected="selected"' : '';
				$result .= '>Closed</option>
							<option value="Waiting on Customer" ';
				$result .= ( $_POST["filter_dropdown"] == "Waiting on Customer" ) ? 'selected="selected"' : '';
				$result .= '>Waiting on Customer</option>
							<option value="Waiting on Third Party" ';
				$result .= ( $_POST["filter_dropdown"] == "Waiting on Third Party" ) ? 'selected="selected"' : '';
				$result .= '>Waiting on Third Party</option>
						</select>
					
					</div>
					<div style="float:right;">
						<input type="text" value="' . $_POST['search_txt'] . '" id="search_txt" name="search_txt" placeholder="Search..."/>
					</div>
					<div style="clear:both;"></div>
				</form>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery("#filter_dropdown").change(function(){
							jQuery("#filter_form").submit();
						});
						jQuery("#search_txt").keypress(function(e) {
							// Enter pressed?
							if(e.which == 10 || e.which == 13) {
								jQuery("#filter_form").submit();
							}
						});
					});
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
		
		function get_tickets( $uemail = '', $roles = array(), $post_array = array() ){
			if( !empty( $uemail ) ){
				if( isset( $post_array['filter_dropdown'] ) ) {
					$filterName = ( $post_array['filter_dropdown'] == 'new_and_my_open' ) ? 'new_and_my_open' : 'all_tickets';
				} else {
					$filterName = 'all_tickets';
				}
				$opt = get_option( 'fd_apikey' );
				$apikey = ( $opt['freshdesk_apikey'] != '' ) ? $opt['freshdesk_apikey'] : '';
				$password = "";
				$filter = ( !in_array( 'administrator', $roles ) ) ? '&email=' . $uemail : '';
				$url = $this->freshdeskUrl . 'helpdesk/tickets.json?page=1&filter_name=' . $filterName . $filter;
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
					$tickets = ( $post_array['filter_dropdown'] != 'new_and_my_open' && $post_array['filter_dropdown'] != 'all_tickets' ) ? $this->filter_tickets( $tickets, $post_array['filter_dropdown'] ) : $tickets ;
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
		
		function get_html( $tickets = '' ){
			$html = '';
			if( !isset( $tickets->require_login ) && $tickets != '' && !isset( $tickets->errors ) ) {
				$html .= '<p>Total Tickets: ' . count( $tickets ) . '</p>';
				$html .= '<ul>';
				foreach( $tickets as $d ) {
					$html .= '<li>Ticket ID: ' . $d->id . '<br/>
								Requester ID: ' . $d->requester_id . '<br/>
								Status: ' . $d->status_name . '
								<p><strong>SUBJECT: </strong>
									<a href="' . $this->freshdeskUrl . 'helpdesk/tickets/' . $d->display_id . '" target="_blank">' . $d->subject . '</a>
								</p>
								
							</li>';
				}
				$html .= '</ul>';
				return $html;
			} else {
				return '<p>Error!</p>';
			}
		}
		
		
		/*
		 * Function Name: filter_tickets
		 * Function Description: Filters the tickets according to ticket_status
		 */
		
		function filter_tickets( $tickets = '', $status = '' ){
			$filtered_tickets = array();
			foreach( $tickets as $t ){
				if( $t->status_name == $status ) {
					$filtered_tickets[] = $t;
				}
			}
			return $filtered_tickets;
		}
		
		
		/*
		 * Function Name: filter_tickets
		 * Function Description: Filters the tickets according to ticket_status
		 */
		
		function search_tickets( $tickets, $txt = '' ){
			$filtered_tickets = array();
			foreach( $tickets as $t ){
				if( strpos( $t->subject, trim( $txt ) ) ) {
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