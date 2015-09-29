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
	
		/*
		 * Function Name: __construct
		 * Function Description: Constructor
		 */
		
		function __construct(){
			add_shortcode( "fetch_tickets", array($this, "fetch_tickets"));
			include_once( 'admin-settings.php' );
		}
		
		
		/*
		 * Function Name: fetch_tickets
		 * Function Description: Fetched all tickets from Freshdesk for current logged in user.
		 */
		
		function fetch_tickets(){
			$result = '';
			if ( is_user_logged_in() ) {
				global $current_user;
				$tickets = $this->get_tickets( $current_user->data->user_email, $current_user->roles );
				if( $tickets ) {
					$result = $this->get_html( $tickets );
				} else {
					$result = '';
				}
			}
			return $result;	
		}
		
		
		/*
		 * Function Name: get_tickets
		 * Function Description: API call to Freshdesk to get all tickets of the user(email)
		 */
		
		function get_tickets( $uemail = '', $roles = array() ){
			if( !empty( $uemail ) ){
				$opt = get_option( 'fd_apikey' );
				$apikey = ( $opt['freshdesk_apikey'] != '' ) ? $opt['freshdesk_apikey'] : '';
				$password = "";
				$filter = ( !in_array( 'administrator', $roles ) ) ? '&email=' . $uemail : '';
				$url = 'https://bsfv.freshdesk.com/helpdesk/tickets.json?filter_name=all_tickets' . $filter;
				$ch = curl_init ($url);
				curl_setopt($ch, CURLOPT_USERPWD, "$apikey:$password");
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				$server_output = curl_exec ($ch);
				curl_close ($ch);
				return json_decode( $server_output );
			} else{
				return false;
			}			
		}
		
		
		/*
		 * Function Name: get_html
		 * Function Description: Returns HTML string of the tickets
		 */
		
		function get_html( $tickets = '' ){
			if( !isset( $tickets->require_login ) ) {
				$html = '<ul>';
				foreach( $tickets as $d ) {
					$html .= '<li>Ticket ID: ' . $d->id . '<br/>
								Requester ID: ' . $d->requester_id . '
								<p><strong>SUBJECT: </strong>
									<a href="https://bsfv.freshdesk.com/helpdesk/tickets/' . $d->display_id . '" target="_blank">' . $d->subject . '</a>
								</p>
								<p><strong>DESCRIPTION: </strong>' . $d->description . '</p>
							</li>';
				}
				$html .= '</ul>';
				return $html;
			} else {
				return '<p>Error!</p>';
			}
		}
	}
}

new FreshDeskAPI();
?>