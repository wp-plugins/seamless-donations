<?php
/*
Plugin Name: Seamless Donations
Plugin URI: http://zatzlabs.com/seamless-donations/
Description: Making online donations easy for your visitors; making donor and donation management easy for you.  Receive donations (now including repeating donations), track donors and send customized thank you messages with Seamless Donations for WordPress.  Works with PayPal accounts. Adopted from Allen Snook.
Version: 4.0.1
Author: David Gewirtz
Author URI: http://zatzlabs.com/lab-notes/
Text Domain: seamless-donations
Domain Path: /languages
License: GPL2
*/

/*  Copyright 2014 Allen Snook (email: allendav@allendav.com)
	Copyright 2015 David Gewirtz (http://zatzlabs.com/contact-us/)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

require_once 'inc/geography.php';
require_once 'inc/currency.php';
require_once 'inc/utilities.php';
require_once 'inc/legacy.php';

require_once 'legacy/dgx-donate.php';
require_once 'legacy/dgx-donate-admin.php';
require_once 'seamless-donations-admin.php';
require_once 'seamless-donations-form.php';
require_once 'dgx-donate-paypalstd.php';

function seamless_donations_admin_loader () {

	// loads for Seamless Donations 4.0 and above

	// bring in the admin page tabs
	require_once 'admin/main.php';
	require_once 'admin/donations.php';
	require_once 'admin/donors.php';
	require_once 'admin/funds.php';
	require_once 'admin/templates.php';
	require_once 'admin/thanks.php';
	require_once 'admin/forms.php';
	require_once 'admin/settings.php';
	require_once 'admin/logs.php';
	require_once 'admin/help.php';

	// bring in the custom post types
	require_once 'cpt/cpt-donations.php';
	require_once 'cpt/cpt-donors.php';
	require_once 'cpt/cpt-funds.php';

	// bring in other resources
	require_once 'inc/form-engine.php';
	require_once 'inc/donations.php';
}

function seamless_donations_legacy_admin_loader () {

	// load the legacy pre-4.0 admin system
	require_once 'legacy/admin-views/main.php';
	require_once 'legacy/admin-views/donor-detail.php';
	require_once 'legacy/admin-views/donation-detail.php';

	require_once 'legacy/admin-views/donations.php';
	require_once 'legacy/admin-views/donors.php';
	require_once 'legacy/admin-views/funds.php';
	require_once 'legacy/admin-views/templates.php';
	require_once 'legacy/admin-views/completed.php';
	require_once 'legacy/admin-views/form-options.php';
	require_once 'legacy/admin-views/settings.php';
	require_once 'legacy/admin-views/log.php';
	require_once 'legacy/admin-views/help.php';
}

//// plugin loaded

function seamless_donations_plugin_loaded () {

	load_plugin_textdomain ( 'seamless-donations', false, dirname ( plugin_basename ( __FILE__ ) ) . '/languages/' );
}

add_action ( 'plugins_loaded', 'seamless_donations_plugin_loaded' );

//// load and enqueue supporting resources

function seamless_donations_enqueue_scripts () {

	wp_enqueue_script ( 'jquery' );

	$script_url = plugins_url ( '/js/seamless-donations.js', __FILE__ );

	wp_register_script ( 'seamless_javascript_code', $script_url, array( 'jquery' ), false );
	wp_enqueue_script ( 'seamless_javascript_code' );

	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	wp_localize_script (
		'dgx_donate_script', 'dgxDonateAjax',
		array(
			'ajaxurl'            => admin_url ( 'admin-ajax.php' ),
			'nonce'              => wp_create_nonce ( 'dgx-donate-nonce' ),
			'postalCodeRequired' => dgx_donate_get_countries_requiring_postal_code ()
		)
	);
}

add_action ( 'wp_enqueue_scripts', 'seamless_donations_enqueue_scripts' ); // DG version of scripts

function seamless_donations_queue_stylesheet () {

	$styleurl = plugins_url ( '/css/styles.css', __FILE__ );
	wp_register_style ( 'seamless_donations_css', $styleurl );
	wp_enqueue_style ( 'seamless_donations_css' );
}

add_action ( 'wp_enqueue_scripts', 'seamless_donations_queue_stylesheet' );

function seamless_donations_queue_admin_stylesheet () {

	$style_url = plugins_url ( '/css/adminstyles.css', __FILE__ );

	wp_register_style ( 'seamless_donations_admin_css', $style_url );
	wp_enqueue_style ( 'seamless_donations_admin_css' );
}

add_action ( 'admin_enqueue_scripts', 'seamless_donations_queue_admin_stylesheet' );

//// donation-specific code

function seamless_donations_get_escaped_formatted_amount ( $amount, $decimal_places = 2, $currency_code = '' ) {

	// same as dgx_donate_get_escaped_formatted_amount

	if( empty( $currency_code ) ) {
		$currency_code = get_option ( 'dgx_donate_currency' );
	}

	$currencies      = dgx_donate_get_currencies ();
	$currency        = $currencies[ $currency_code ];
	$currency_symbol = $currency['symbol'];

	return $currency_symbol . esc_html ( number_format ( $amount, $decimal_places ) );
}

//// new 4.0+ shortcode for 4.0+ forms and admin environment

add_shortcode ( 'seamless-donations', 'seamless_donations_shortcode' );

function seamless_donations_shortcode ( $atts ) {

	$output      = '';
	$show_thanks = false;
	if( isset( $_GET['thanks'] ) ) {
		$show_thanks = true;
	} else if( isset( $_GET['auth'] ) ) {
		$show_thanks = true;
	}

	// Switch
	if( $show_thanks ) {
		$output = dgx_donate_display_thank_you ();
	} else {
		$sd4_mode = get_option ( 'dgx_donate_start_in_sd4_mode' );
		if( $sd4_mode == false ) {
			$output .= "<div style='background-color:red; color:white'>";
			$output .= "<P style='padding:5px;'>Warning: This form needs to be updated. ";
			$output .= "Please update using the [seamless-donations] shortcode.</P>";
			$output .= "</div>";
		} else {
			$output = "";
			$output = seamless_donations_generate_donation_form ( $output );

			if( empty( $output ) ) {
				$output = "<p>Error: No payment gateway selected. ";
				$output .= "Please choose a payment gateway in Seamless Donations >> Settings.</p>";
			}
		}
	}

	return $output;
}

function seamless_donations_init () {

	// Start a PHP session if none has been started yet
	// The means to test whether a session has been started varies by PHP version

	if( version_compare ( phpversion (), '5.4.0', '>=' ) ) {
		$session_already_started = ( session_status () === PHP_SESSION_ACTIVE );
	} else {
		$session_id              = session_id ();
		$session_already_started = ( ! empty( $session_id ) );
	}

	if( ! $session_already_started ) {
		session_start ();
	}

	// Check to see if we're supposed to run an upgrade
	seamless_donations_sd40_process_upgrade_check ();

	// Check to see if first-time run
	$from_name = get_option ( 'dgx_donate_email_name' );
	if( $from_name == false ) {
		// this is a pure 4.0+ start
		update_option ( 'dgx_donate_start_in_sd4_mode', 'true' );
		$sd4_mode = true;
	} else {
		// now we need to determine if we've already updated to 4.0+ or not
		$sd4_mode = get_option ( 'dgx_donate_start_in_sd4_mode' );
		if( $sd4_mode != false ) {
			$sd4_mode = true;
		}
	}

	// Initialize options to defaults as needed
	if( $sd4_mode ) {
		seamless_donations_init_defaults ();
		seamless_donations_admin_loader ();
	} else {
		dgx_donate_init_defaults ();
		seamless_donations_legacy_admin_loader ();
		add_action ( 'admin_notices', 'seamless_donations_sd40_update_alert_message' );
	}

	// Display an admin notice if we are in sandbox mode

	$payPalServer = get_option ( 'dgx_donate_paypal_server' );
	if( strcasecmp ( $payPalServer, "SANDBOX" ) == 0 ) {
		add_action ( 'admin_notices', 'dgx_donate_admin_sandbox_msg' );
	}
}

add_action ( 'init', 'seamless_donations_init' );

function seamless_donations_init_defaults () {

	// functionally identical to dgx_donate_init_defaults, but likely to change over time

	// Thank you email option defaults

	// validate name - replace with sanitized blog name if needed
	$from_name = get_option ( 'dgx_donate_email_name' );
	if( empty( $from_name ) ) {
		$from_name = get_bloginfo ( 'name' );
		$from_name = preg_replace ( "/[^a-zA-Z ]+/", "", $from_name ); // letters and spaces only please
		update_option ( 'dgx_donate_email_name', $from_name );
	}

	// validate email - replace with admin email if needed
	$from_email = get_option ( 'dgx_donate_email_reply' );
	if( empty( $from_email ) || ! is_email ( $from_email ) ) {
		$from_email = get_option ( 'admin_email' );
		update_option ( 'dgx_donate_email_reply', $from_email );
	}

	$thankSubj = get_option ( 'dgx_donate_email_subj' );
	if( empty( $thankSubj ) ) {
		$thankSubj = "Thank you for your donation";
		update_option ( 'dgx_donate_email_subj', $thankSubj );
	}

	$bodyText = get_option ( 'dgx_donate_email_body' );
	if( empty( $bodyText ) ) {
		$bodyText = "Dear [firstname] [lastname],\n\n";
		$bodyText .= "Thank you for your generous donation of [amount]. Please note that no goods ";
		$bodyText .= "or services were received in exchange for this donation.";
		update_option ( 'dgx_donate_email_body', $bodyText );
	}

	$recurring_text = get_option ( 'dgx_donate_email_recur' );
	if( empty( $recurring_text ) ) {
		$recurring_text = __ (
			"Thank you for electing to have your donation automatically repeated each month.", 'seamless-donations' );
		update_option ( 'dgx_donate_email_recur', $recurring_text );
	}

	$designatedText = get_option ( 'dgx_donate_email_desig' );
	if( empty( $designatedText ) ) {
		$designatedText = "Your donation has been designated to the [fund] fund.";
		update_option ( 'dgx_donate_email_desig', $designatedText );
	}

	$anonymousText = get_option ( 'dgx_donate_email_anon' );
	if( empty( $anonymousText ) ) {
		$anonymousText
			= "You have requested that your donation be kept anonymous.  Your name will not be revealed to the public.";
		update_option ( 'dgx_donate_email_anon', $anonymousText );
	}

	$mailingListJoinText = get_option ( 'dgx_donate_email_list' );
	if( empty( $mailingListJoinText ) ) {
		$mailingListJoinText
			= "Thank you for joining our mailing list.  We will send you updates from time-to-time.  If ";
		$mailingListJoinText .= "at any time you would like to stop receiving emails, please send us an email to be ";
		$mailingListJoinText .= "removed from the mailing list.";
		update_option ( 'dgx_donate_email_list', $mailingListJoinText );
	}

	$tributeText = get_option ( 'dgx_donate_email_trib' );
	if( empty( $tributeText ) ) {
		$tributeText
			= "You have asked to make this donation in honor of or memory of someone else.  Thank you!  We will notify the ";
		$tributeText .= "honoree within the next 5-10 business days.";
		update_option ( 'dgx_donate_email_trib', $tributeText );
	}

	$employer_text = get_option ( 'dgx_donate_email_empl' );
	if( empty( $employer_text ) ) {
		$employer_text = "You have specified that your employer matches some or all of your donation. ";
		update_option ( 'dgx_donate_email_empl', $employer_text );
	}

	$closingText = get_option ( 'dgx_donate_email_close' );
	if( empty( $closingText ) ) {
		$closingText = "Thanks again for your support!";
		update_option ( 'dgx_donate_email_close', $closingText );
	}

	$signature = get_option ( 'dgx_donate_email_sig' );
	if( empty( $signature ) ) {
		$signature = "Director of Donor Relations";
		update_option ( 'dgx_donate_email_sig', $signature );
	}

	//// PayPal defaults
	$notifyEmails = get_option ( 'dgx_donate_notify_emails' );
	if( empty( $notifyEmails ) ) {
		$notifyEmails = get_option ( 'admin_email' );
		update_option ( 'dgx_donate_notify_emails', $notifyEmails );
	}

	$paymentGateway = get_option ( 'dgx_donate_payment_gateway' );
	if( empty( $paymentGateway ) ) {
		update_option ( 'dgx_donate_payment_gateway', DGXDONATEPAYPALSTD );
	}

	$payPalServer = get_option ( 'dgx_donate_paypal_server' );
	if( empty( $payPalServer ) ) {
		update_option ( 'dgx_donate_paypal_server', 'LIVE' );
	}

	$paypal_email = get_option ( 'dgx_donate_paypal_email' );
	if( ! is_email ( $paypal_email ) ) {
		update_option ( 'dgx_donate_paypal_email', '' );
	}

	// Thank you page default
	$thankYouText = get_option ( 'dgx_donate_thanks_text' );
	if( empty( $thankYouText ) ) {
		$message = "Thank you for donating!  A thank you email with the details of your donation ";
		$message .= "will be sent to the email address you provided.";
		update_option ( 'dgx_donate_thanks_text', $message );
	}

	// Giving levels default
	$givingLevels = dgx_donate_get_giving_levels ();
	$noneChecked  = true;
	foreach( $givingLevels as $givingLevel ) {
		$levelEnabled = dgx_donate_is_giving_level_enabled ( $givingLevel );
		if( $levelEnabled ) {
			$noneChecked = false;
		}
	}
	if( $noneChecked ) {
		// Select 1000, 500, 100, 50 by default
		dgx_donate_enable_giving_level ( 1000 );
		dgx_donate_enable_giving_level ( 500 );
		dgx_donate_enable_giving_level ( 100 );
		dgx_donate_enable_giving_level ( 50 );
	}

	// Currency
	$currency = get_option ( 'dgx_donate_currency' );
	if( empty( $currency ) ) {
		update_option ( 'dgx_donate_currency', 'USD' );
	}

	// Country default
	$default_country = get_option ( 'dgx_donate_default_country' );
	if( empty( $default_country ) ) {
		update_option ( 'dgx_donate_default_country', 'US' );
	}

	// State default
	$default_state = get_option ( 'dgx_donate_default_state' );
	if( empty( $default_state ) ) {
		update_option ( 'dgx_donate_default_state', 'WA' );
	}

	// Province default
	$default_province = get_option ( 'dgx_donate_default_province' );
	if( empty( $default_province ) ) {
		update_option ( 'dgx_donate_default_province', 'AB' );
	}

	// Show Employer match section default
	$show_employer_section = get_option ( 'dgx_donate_show_employer_section' );
	if( empty( $show_employer_section ) ) {
		update_option ( 'dgx_donate_show_employer_section', 'false' );
	}

	// Show occupation field default
	$show_occupation_section = get_option ( 'dgx_donate_show_donor_occupation_field' );
	if( empty( $show_occupation_section ) ) {
		update_option ( 'dgx_donate_show_donor_occupation_field', 'false' );
	}

	// Show donor employer default
	$show_occupation_section = get_option ( 'dgx_donate_show_donor_employer_field' );
	if( empty( $show_occupation_section ) ) {
		update_option ( 'dgx_donate_show_donor_employer_field', 'false' );
	}

	// Show Tribute Gift section default
	$show_tribute_section = get_option ( 'dgx_donate_show_tribute_section' );
	if( empty( $show_tribute_section ) ) {
		update_option ( 'dgx_donate_show_tribute_section', 'true' );
	}

	// Scripts location default
	$scripts_in_footer = get_option ( 'dgx_donate_scripts_in_footer' );
	if( empty( $scripts_in_footer ) ) {
		update_option ( 'dgx_donate_scripts_in_footer', 'false' );
	}
}