=== WP Freshdesk ===
Contributors: brainstormforce, vrundakansara, nikschavan, pratikc
Tags: freshdesk, support, wordpress freshdesk
Requires at least: 4.2
Tested up to: 4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With this plugin, your users will be able to see their tickets on your Freshdesk support portal. Other features include - SSO, ticket filtering, sorting & search options. Admins have an options to display only certain status tickets with shortcodes.

== Description ==

With WP Freshdesk you can easily connect your WordPress site with your Freshdesk account, For all the WordPress users, it fetches their tickets from Freshdesk and displays them in a beautiful list with just simple shortcodes.

IT also provides Single sign-on to be configured between WordPress and freshdesk, so that once the user is logged in to WordPress site, they don't have to re-login on freshdesk to view their tickets.

Available Shortcodes - 

All Tickets 			-	`[fd_fetch_tickets]`
Open Tickets 			-	`[fd_fetch_tickets filter="Open"]`
Resolved tickets 		-	`[fd_fetch_tickets filter="Resolved"]`
Closed Tickets 			-	`[fd_fetch_tickets filter="Closed"]`
Pending Tickets 		-	`[fd_fetch_tickets filter="Pending"]`
Waiting on Customer		-	`[fd_fetch_tickets filter="Waiting on Customer"]`
Waiting on Third Party 	- 	`[fd_fetch_tickets filter="Waiting on Third Party"]`
Create New Ticket 		-	`[fd_new_ticket]`

Get involved in the development of the plugin, Visit [Github Repo](https://github.com/brainstormforce/wp-freshdesk/ "Github Repo")

== Installation ==

= The Good Way =

1. In your WordPress Admin, go to the Add New Plugins page
2. Search for: WP Freshdesk
3. WP Freshdesk should be the first result. Click the Install link.

= The Old Way =

1. Upload the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

= The Living-On-The-Edge Way =

(Please don't do this in production, you will almost certainly break something!)

1. Checkout the current development version from https://github.com/brainstormforce/wp-freshdesk/tree/devp

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. Settings screen for general configurationsbetween WordPress and Freshdesk.
2. List of all he available shortcodes.
3. SSO settings page.
4. Display Settings.
5. Frontend display of tickets list.

== Changelog ==

= 1.0 =
* Initial Release
