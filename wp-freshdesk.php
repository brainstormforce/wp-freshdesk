<?php
/**
* Plugin Name: WP Freshdesk
* Plugin URI:
* Description: With this plugin, your users will be able to see their tickets on your Freshdesk support portal. Other features include - SSO, ticket filtering, sorting & search options. Admins have an options to display only certain status tickets with shortcodes.
* Version: 1.0.3
* Author: Brainstorm Force
* Author URI: https://www.brainstormforce.com/
* License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

//Block direct access to plugin files.
defined('ABSPATH') or die();

if (!class_exists("FreshDeskAPI")) {
    class FreshDeskAPI
    {
    
        //Class Variables
        private $freshdeskUrl;
        private $opt;
        private $options;
        private $display_option;
    
        /*
         * Function Name: __construct
         * Function Description: Constructor
         */
        
        function __construct()
        {
        
            add_action('init', array( $this, 'init' ));
            add_action('plugins_loaded', array( $this, 'fd_load_textdomain' ));
            add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ));
            add_shortcode("fd_fetch_tickets", array($this, "fetch_tickets"));
            add_shortcode("fd_new_ticket", array($this, "new_ticket"));
            
            include_once('admin-settings.php');
            
            $this->options = get_option('fd_url');
            $this->opt = get_option('fd_apikey');
            $this->display_option = get_option('fd_display');
            
            if (isset($this->opt['freshdesk_url'])) {
                if (preg_match("/^[-A-Za-z\d\s]+$/", $this->opt['freshdesk_url'])) {
                    $this->freshdeskUrl = 'https://' . $this->opt['freshdesk_url'] . '.freshdesk.com/';
                } else {
                    $this->freshdeskUrl = '';
                }
            } else {
                $this->freshdeskUrl = '';
            }
        }
        
        
        /**
         * Load plugin textdomain.
         *
         * @since 1.0.0
         */
        function fd_load_textdomain()
        {
            load_plugin_textdomain('wp-freshdesk', false, plugin_basename(dirname(__FILE__)) . '/languages');
        }
        
        
        /*
         * Function Name: enqueue_scripts
         * Function Description: Adds scripts to wp pages
         */
        
        function enqueue_scripts()
        {
            wp_register_style('fd-style', plugins_url('css/fd-style.css', __FILE__));
            wp_enqueue_style('fd-style');
            wp_register_script('fd-script-frontend', plugins_url('js/fd-script-frontend.js', __FILE__), array('jquery'), '1.1', true);
            wp_enqueue_script('fd-script-frontend');
        }
        
        
        
        /*
         * Function Name: init
         * Function Description: Initialization
         */
        public function init()
        {
            
            if (is_user_logged_in()) {
                // This is a login request.
                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'fd-remote-login') {
                    // Don't waste time if remote auth is turned off.
                    if (!isset($this->options['freshdesk_enable']) && $this->options['freshdesk_enable'] != 'on' && !isset($this->options['freshdesk_sharedkey']) && $this->options['freshdesk_sharedkey'] != '') {
                        __('Remote authentication is not configured yet.', 'wp-freshdesk');
                        die();
                    }
                    // Filter freshdesk_return_to
                    $return_to = apply_filters('freshdesk_return_to', $_REQUEST['host_url']) ;
    
                    // If the current user is logged in
                    if (is_user_logged_in()) {
                        global $current_user;
                        wp_get_current_user();
    
                        // Pick the most appropriate name for the current user.
                        if ($current_user->user_firstname != '' && $current_user->user_lastname != '') {
                            $name = $current_user->user_firstname . ' ' . $current_user->user_lastname;
                        } else {
                            $name = $current_user->display_name;
                        }
    
                        // Gather more info from the user, incl. external ID
                        $email = $current_user->user_email;
    
                        // The token is the remote "Shared Secret" under Admin - Security - Enable Single Sign On
                        $token = $this->options['freshdesk_sharedkey'];
    
                        // Current timestamp.
                        $timestamp = time();

                        // Generate the hash as per http://www.freshdesk.com/api/remote-authentication

                        $to_be_hashed = $name . $token . $email . $timestamp;
                        $hash = hash_hmac('md5', $to_be_hashed, $token);

    
                        // Create the SSO redirect URL and fire the redirect.
                        $sso_url = trailingslashit($this->freshdeskUrl) . 'login/sso/?action=fd-remote-login&return_to=' . urlencode('https://' . $return_to . '/') . '&name=' . urlencode($name) . '&email=' . urlencode($email) . '&hash=' . urlencode($hash) . '&timestamp=' . $timestamp;
    
                        //Hook before redirecting logged in user.
                        do_action('freshdesk_logged_in_redirect_before');
    
                        wp_redirect($sso_url);
    
                        // No further output.
                        die();
                    } else {
                        //Hook before redirecting user to login form
                        do_action('freshdesk_logged_in_redirect_before');
    
                        // If the current user is not logged in we ask him to visit the login form
                        // first, authenticate and specify the current URL again as the return
                        // to address. Hopefully WordPress will understand this.
                        wp_redirect(wp_login_url(wp_login_url() . '?action=fd-remote-login&&return_to=' . urlencode($return_to)));
                        die();
                    }
                }
    
                // Is this a logout request? Errors from Freshdesk are handled here too.
                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'fd-remote-logout') {
                    // Error processing and info messages are done here.
                    $kind = isset($_REQUEST['kind']) ? $_REQUEST['kind'] : 'info';
                    $message = isset($_REQUEST['message']) ? $_REQUEST['message'] : 'nothing';
    
                    // Depending on the message kind
                    if ($kind == 'info') {
                        // When the kind is an info, it probably means that the logout
                        // was successful, thus, logout of WordPress too.
                        wp_redirect(htmlspecialchars_decode(wp_logout_url()));
                        die();
                    } elseif ($kind == 'error') {
                        // If there was an error...
                        ?>
                        <p><?php __('Remote authentication failed: ', 'wp-freshdesk'); ?><?php echo $message; ?>.</p>
                        <ul>
                            <li><a href="<?php echo $this->freshdeskUrl; ?>"><?php __('Try again', 'wp-freshdesk'); ?></a></li>
                            <li><a href="<?php echo wp_logout_url(); ?>"><?php printf(__('Log out of %s', 'wp-freshdesk'), get_bloginfo('name')); ?></a></li>
                            <li><a href="<?php echo admin_url(); ?>"><?php printf(__('Return to %s dashboard', 'wp-freshdesk'), get_bloginfo('name')); ?></a></li>
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
        
        public function fetch_tickets($atts)
        {
            $result = '
			<div class="fd-tickets-outter">
				<ul>';
            
            if (is_user_logged_in()) {
                global $current_user;
                $fd_filter_dropdown = ( isset($_GET["fd-filter_dropdown"]) ) ? esc_attr($_GET["fd-filter_dropdown"]) : '';
                if (( isset($this->opt['freshdesk_apikey']) && $this->opt['freshdesk_apikey'] != '' ) || !isset($this->opt['use_apikey'])) {
                    if (isset($atts['filter']) && trim($atts['filter']) != '') {
                        switch (trim(ucwords(strtolower($atts['filter'])))) {
                            case 'Open':
                                $fd_filter_dropdown = 'Open';
                                break;
                            case 'Closed':
                                $fd_filter_dropdown = 'Closed';
                                break;
                            case 'Resolved':
                                $fd_filter_dropdown = 'Resolved';
                                break;
                            case 'Waiting On Third Party':
                                $fd_filter_dropdown = 'Waiting on Third Party';
                                break;
                            case 'Waiting On Customer':
                                $fd_filter_dropdown = 'Waiting on Customer';
                                break;
                            case 'Pending':
                                $fd_filter_dropdown = 'Pending';
                                break;
                            default:
                                break;
                        }
                    }
                    
                    if (!isset($fd_filter_dropdown) || $fd_filter_dropdown == '') {
                        $fd_filter_dropdown = 'Open';
                    }
                    
                    $tickets = $this->get_tickets($current_user->data->user_email, $current_user->roles);
                    $filteredTickets = false;
                    $search_txt = ( isset($_GET['search_txt']) ) ? esc_attr($_GET['search_txt']) : '';
                    if (isset($tickets)) {
                        $tickets = json_decode(json_encode($tickets), true);
                        if (isset($fd_filter_dropdown) && $fd_filter_dropdown != '') {
                            $filteredTickets = ( $fd_filter_dropdown != 'all_tickets' ) ? $this->filter_tickets($tickets, $fd_filter_dropdown) : $tickets ;
                        }
                        if (isset($search_txt) && $search_txt != '') {
                            $filteredTickets = ( trim($search_txt) != '' ) ? $this->search_tickets($filteredTickets, $search_txt) : $tickets ;
                        }
                    } else {
                        $filteredTickets = false;
                    }
                    
                    
                    
                    $result .= '
								<li class="fd-filter-tickets">
									<form method="get" action="" id="fd-filter_form" name="fd-filter_form">
										<div class="fd-filter-dropdown fd-filter">
											<select id="fd-filter_dropdown" name="fd-filter_dropdown">
												<option value="all_tickets" ';
                    if (isset($fd_filter_dropdown)) {
                        $result .= ( $fd_filter_dropdown == "all_tickets" ) ? 'selected="selected"' : '';
                    }
                                    $result .= '>' . __('All Tickets', 'wp-freshdesk') . '</option>
												<option value="Open" ';
                    if (isset($fd_filter_dropdown)) {
                        $result .= ( $fd_filter_dropdown == "Open" ) ? 'selected="selected"' : '';
                    }
                                    $result .= '>' . __('Open', 'wp-freshdesk') . '</option>
												<option value="Pending" ';
                    if (isset($fd_filter_dropdown)) {
                        $result .= ( $fd_filter_dropdown == "Pending" ) ? 'selected="selected"' : '';
                    }
                                    $result .= '>' . __('Pending', 'wp-freshdesk') . '</option>
												<option value="Resolved" ';
                    if (isset($fd_filter_dropdown)) {
                        $result .= ( $fd_filter_dropdown == "Resolved" ) ? 'selected="selected"' : '';
                    }
                                    $result .= '>' . __('Resolved', 'wp-freshdesk') . '</option>
												<option value="Closed" ';
                    if (isset($fd_filter_dropdown)) {
                        $result .= ( $fd_filter_dropdown == "Closed" ) ? 'selected="selected"' : '';
                    }
                                    $result .= '>' . __('Closed', 'wp-freshdesk') . '</option>
												<option value="Waiting on Customer" ';
                    if (isset($fd_filter_dropdown)) {
                        $result .= ( $fd_filter_dropdown == "Waiting on Customer" ) ? 'selected="selected"' : '';
                    }
                                    $result .= '>' . __('Waiting on Customer', 'wp-freshdesk') . '</option>
												<option value="Waiting on Third Party" ';
                    if (isset($fd_filter_dropdown)) {
                        $result .= ( $fd_filter_dropdown == "Waiting on Third Party" ) ? 'selected="selected"' : '';
                    }
                                    $txt = ( isset($search_txt) ) ? $search_txt : '';
                                    $result .= '>' . __('Waiting on Third Party', 'wp-freshdesk') . '</option>
											</select>
										</div>
										<div class="fd-search-box fd-filter">
											<input type="text" value="' . $txt . '" id="search_txt" name="search_txt" placeholder="' . __('Search...', 'wp-freshdesk') . '"/>
										</div>
										<div class="fd-filter">
											<input type="submit" value="Search" id="filter_tickets"/>
										</div>
										<div class="fd-filter">
											<input type="button" value="Reset" id="reset_filter">
										</div>
										<div class="clear"></div>
									</form>
								</li>';
                    
                    if (!isset($tickets->require_login) && $tickets != '' && !isset($tickets->errors) && !empty($tickets)) {
                        if (isset($search_txt) || isset($fd_filter_dropdown)) {
                            if (!isset($filteredTickets->require_login) && $filteredTickets != '' && !isset($filteredTickets->errors) && !empty($filteredTickets)) {
                                $result .= $this->get_html($filteredTickets);
                            } else {
                                if (isset($filteredTickets->require_login)) {
                                    $msg = __('Invalid Credentials', 'wp-freshdesk');
                                } elseif (isset($filteredTickets->errors)) {
                                    if (isset($filteredTickets->errors->no_email)) {
                                        $msg = ( isset($this->display_option['invalid_user_msg']) && $this->display_option['invalid_user_msg'] != '' ) ? $this->display_option['invalid_user_msg'] : __('Invalid User', 'wp-freshdesk');
                                    } else {
                                        $msg = __('Invalid Freshdesk URL', 'wp-freshdesk');
                                    }
                                } elseif (empty($filteredTickets)) {
                                    $keyword = ( isset($search_txt) && $search_txt != '' ) ? 'keyword <strong>"' . $search_txt . '"</strong>.' : '';
                                    $dropdown = ( isset($fd_filter_dropdown) && $fd_filter_dropdown != '' ) ? 'No tickets for <strong>"' . strtoupper(str_replace('_', ' ', $fd_filter_dropdown)) . '"</strong> category' : '';
                                    $str = $dropdown;
                                    $str .= ( $keyword != '' ) ? ' & ' . $keyword : '';
                                    $msg = '<p> ' . $str . '</p><div class="fd-more-ticket">Could not find what you are searching for? Click <a href="' . $this->freshdeskUrl . 'support/tickets" target="_blank">here</a> to check all your old tickets.</div>';
                                } else {
                                    $msg = __('Error!', 'wp-freshdesk');
                                }
                                $result .= '<li>
												<div class="fd-message">' . $msg . '</div>
											</li>';
                            }
                        } else {
                            $result .= $this->get_html($tickets);
                        }
                    } else {
                        if (isset($tickets->require_login)) {
                            $msg = __('Invalid Credentials', 'wp-freshdesk');
                        } elseif (isset($tickets->errors)) {
                            if (isset($tickets->errors->no_email)) {
                                $msg = ( isset($this->display_option['invalid_user_msg']) && $this->display_option['invalid_user_msg'] != '' ) ? $this->display_option['invalid_user_msg'] : __('Invalid User', 'wp-freshdesk');
                            } else {
                                $msg = __('Invalid Freshdesk URL', 'wp-freshdesk');
                            }
                        } elseif (empty($tickets)) {
                            $msg = ( isset($this->display_option['no_tickets_msg']) && $this->display_option['no_tickets_msg'] != '' ) ? $this->display_option['no_tickets_msg'] : __('No tickets', 'wp-freshdesk');
                        } else {
                            $msg = __('Error!', 'wp-freshdesk');
                        }
                        $result .= '<li>
										<div class="fd-message">' . $msg . '</div>
									</li>';
                    }
                } else {
                    $result .= '
						<li>
							<div class="fd-message">Please configure settings for <strong>Freshdesk API</strong> from <a href="' . admin_url('/options-general.php?page=wp-freshdesk') . '" target="_blank">admin panel</a></div>
						</li>
					';
                }
            } else {
                $result .= '
					<li>
						<div class="fd-message"><a href="' . wp_login_url() . '" title="Login">Login</a> to view your tickets!</div>
					</li>
				';
            }
            
            $result .=
                '</ul>
			</div>';
            return $result;
        }
        
        
        /*
         * Function Name: new_ticket
         * Function Description: Create a new ticket button html
         */
        
        function new_ticket()
        {
            return '<form action="' . $this->freshdeskUrl . 'support/tickets/new/" target="_blank"><input type="submit" value="New Ticket" id="new_ticket"></form>';
        }
        
        
        /*
         * Function Name: get_tickets
         * Function Description: API call to Freshdesk to get all tickets of the user(email)
         */
        
        public function get_tickets($uemail = '', $roles = array())
        {
            if (!empty($uemail)) {
                $filterName = 'all_tickets';
                if (isset($this->opt['use_apikey'])) {
                    $apikey = ( $this->opt['freshdesk_apikey'] != '' ) ? $this->opt['freshdesk_apikey'] : '';
                    $password = "";
                } else {
                    $apikey = ( $this->opt['api_username'] != '' ) ? $this->opt['api_username'] : '';
                    $password = ( $this->opt['api_pwd'] != '' ) ? $this->opt['api_pwd'] : '';
                }
                
                $filter = ( !in_array('administrator', $roles) ) ? '&email=' . $uemail : '';
                $url = $this->freshdeskUrl . 'helpdesk/tickets.json?filter_name=' . $filterName . $filter;
                
                $auth = base64_encode($apikey . ':' . $password);
                
                $args = array(
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Authorization' => "Basic $auth"
                    ),
                    'body' => array()
                );

                $response = wp_remote_get($url, $args);

                // test for wp errors.
                if (is_wp_error($response)) {
                    return array();
                    exit;
                }

                $body = wp_remote_retrieve_body($response);
                $tickets = json_decode($body);
                return $tickets;
            } else {
                return false;
            }
        }
        
        
        /*
         * Function Name: get_html
         * Function Description: Returns HTML string of the tickets
         */
        
        public function get_html($tickets = '')
        {
            global $current_user;
            $html = '';
            $tickets = json_decode(json_encode($tickets), false);
            $append = ( count($tickets) > 1 ) ? 's' : '';
            $html .=
            '<li>
				<div class="fd-message">' . count($tickets) . ' ticket' . $append . ' found (Opened with: ' . $current_user->data->user_email . ')</div>
			</li>';
            
            foreach ($tickets as $d) {
                $class = ( $d->status_name == "Closed" ) ? 'status-closed' : '';
                $diff = ( strtotime(date_i18n('Y-m-d H:i:s')) - strtotime(date_i18n('Y-m-d H:i:s', false, 'gmt')) );
                $date = date_i18n('j M\' Y, g:i A', strtotime($d->updated_at) + $diff);
                $description = ( strlen($d->description) > 125 ) ? strip_tags(substr($d->description, 0, 125)) . '...' : strip_tags($d->description);
                $time_elapsed = $this->timeAgo(date_i18n('Y-m-d H:i:s', strtotime($d->updated_at) + $diff));
                $html .= '
				<li class="group ' . $class . '">
					<a href="' . $this->freshdeskUrl . 'helpdesk/tickets/' . $d->display_id . '" target="_blank">
						<span class="ticket-data">
							<span class="ticket-title">' . strip_tags($d->subject) . ' <span class="ticket-id">#' . $d->display_id . '</span></span>
							<span class="ticket-excerpt">' . $description . '</span>
						</span>
						<span class="ticket-meta">
							<span class="ticket-status ' . $class . '">' . strip_tags($d->status_name) . '</span>
							<span class="ticket-time"><abbr title="Last Updated on - ' . $date . '" class="timeago comment-time ticket-updated-at">' . $time_elapsed . '</abbr></span>
						</span>
					</a>
				</li>';
            }
            return $html;
        }
        
        
        /*
         * Function Name: filter_tickets
         * Function Description: Filters the tickets according to ticket_status
         */
        
        public function filter_tickets($tickets = '', $status = '')
        {
            $filtered_tickets = array();
            if ($status != 'all_tickets') {
                foreach ($tickets as $t) {
                    if ($t['status_name'] == $status) {
                        $filtered_tickets[] = $t;
                    }
                }
                return $filtered_tickets;
            } else {
                return $tickets;
            }
        }
        
        
        /*
         * Function Name: search_tickets
         * Function Description: Searches the tickets according to input text
         */
        
        public function search_tickets($tickets, $txt = '')
        {
            $filtered_tickets = array();
            foreach ($tickets as $t) {
                if (stristr($t['subject'], trim($txt)) || stristr($t['description'], trim($txt)) || stristr($t['id'], trim($txt))) {
                    $filtered_tickets[] = $t;
                }
            }
            return $filtered_tickets;
        }
        
        
        /*
         * Function Name: timeAgo
         * Function Description: returns input php time to "mins/hours/months/weeks/years ago" format.
         */

        function timeAgo($time_ago)
        {
            $time_ago = strtotime($time_ago);
            $cur_time = strtotime(date_i18n('Y-m-d H:i:s'));
            $time_elapsed = $cur_time - $time_ago;
            $seconds = $time_elapsed ;
            $minutes = round($time_elapsed / 60);
            $hours = round($time_elapsed / 3600);
            $days = round($time_elapsed / 86400);
            $weeks = round($time_elapsed / 604800);
            $months = round($time_elapsed / 2600640);
            $years = round($time_elapsed / 31207680);
            // Seconds
            if ($seconds <= 60) {
                return "just now";
            }
            //Minutes
            elseif ($minutes <= 60) {
                if ($minutes == 1) {
                    return "one minute ago";
                } else {
                    return "$minutes minutes ago";
                }
            }
            //Hours
            elseif ($hours <= 24) {
                if ($hours == 1) {
                    return "an hour ago";
                } else {
                    return "$hours hrs ago";
                }
            }
            //Days
            elseif ($days <= 7) {
                if ($days == 1) {
                    return "yesterday";
                } else {
                    return "$days days ago";
                }
            }
            //Weeks
            elseif ($weeks <= 4.3) {
                if ($weeks == 1) {
                    return "a week ago";
                } else {
                    return "$weeks weeks ago";
                }
            }
            //Months
            elseif ($months <= 12) {
                if ($months == 1) {
                    return "a month ago";
                } else {
                    return "$months months ago";
                }
            }
            //Years
            else {
                if ($years == 1) {
                    return "one year ago";
                } else {
                    return "$years years ago";
                }
            }
        }
    }
} //end of class


/* Register the activation function and redirect to Setting page. */
register_activation_hook(__FILE__, 'fd_plugin_activate');
add_action('admin_init' , 'fd_plugin_activate_redirect');

function fd_plugin_activate_redirect() {

    $fd_plugin_activate_redirect = get_option('fd_plugin_activate_redirect');
    if($fd_plugin_activate_redirect) {
        delete_option('fd_plugin_activate_redirect');
        exit(wp_redirect(admin_url('options-general.php?page=wp-freshdesk')));
    }
}
/*
 * Function Name: fd_plugin_activate
 * Function Description:
 */

function fd_plugin_activate()
{
    $activate_multi = isset($_GET['networkwide']) ? esc_attr($_GET['networkwide']) : '';
    if ('1' !== $activate_multi) {
        add_option('fd_plugin_activate_redirect' , true);
        //wp_redirect(admin_url('options-general.php?page=wp-freshdesk'));
    }
}

new FreshDeskAPI();
?>
