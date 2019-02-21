=== WP Freshdesk ===
Contributors: brainstormforce
Tags: freshdesk, support, wordpress freshdesk
Requires at least: 4.2
Tested up to: 5.1
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow users to see their Freshdesk tickets right on your website and provides SSO to have seamless navigation between Freshdesk and WordPress.

== Description ==

With WP Freshdesk you can easily connect your WordPress site with your Freshdesk account. For all the WordPress users, it fetches their tickets from Freshdesk and displays them in a beautiful list with just simple shortcodes.

It also provides Single Sign-On to be configured between WordPress and Freshdesk, so that once the user is logged in to WordPress site, they don't have to re-login on Freshdesk to view their tickets.


We would appreciate all kind of contributions on GitHub with issues / PR here, Visit [Github Repo](https://github.com/brainstormforce/wp-freshdesk/ "Github Repo")

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

1. Check out the current development version from https://github.com/brainstormforce/wp-freshdesk/tree/master

== Screenshots ==

1. Settings screen for general configurations between WordPress and Freshdesk.
2. List of all he available shortcodes.
3. SSO settings page.
4. Display Settings.
5. Frontend display of tickets list.

== Changelog ==

= 1.0.3 =
* Fixed: Ticket updated time showing wrong value.
* Fixed: Redirection issue.
* Improved: Replaced Curl call with WP_REMOTE_GET

= 1.0.2 =
* Fixed: SSO functionality as per the updated API.
* Fixed: Create new ticket shortcode echos on wrong position.
* Improved: Updated Regex for FreshDesk URL.

= 1.0.1 =
* Fixed: Load the plugin’s assets only on the required pages

= 1.0 =
* Initial Release
