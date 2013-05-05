<?php
/*
Plugin Name: Seamless Donations
Plugin URI: http://www.allensnook.com/plugins/seamless-donations/
Description: Making online donations easy for your visitors; making donor and donation management easy for you.
Version: 2.3.0
Author: allendav
Author URI: http://www.allensnook.com
License: GPL2
*/

/*  Copyright 2013 Allen Snook (email: allen@allensnook.com)

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
 
include 'dgx-donate-admin.php';
include 'dgx-donate-paypalstd.php';

/******************************************************************************************************/
function dgx_donate_get_giving_levels()
{
	$builtinGivingLevels = array(1000,500,200,100,50,20,10,5);

	$givingLevels = apply_filters('dgx_donate_giving_levels', $builtinGivingLevels);

	// Bad filter?

	if (count($givingLevels) == 0)
	{
		$givingLevels = array(1000); // default = just $1000
	}

	return $givingLevels;
}

/******************************************************************************************************/
function dgx_donate_is_valid_giving_level($amount)
{
	$givingLevels = dgx_donate_get_giving_levels();

	if (in_array($amount, $givingLevels))
	{
		return true;
	}

	return false;
}

/******************************************************************************************************/
function dgx_donate_enable_giving_level($amount)
{
	if (dgx_donate_is_valid_giving_level($amount))
	{
		$key = dgx_donate_get_giving_level_key($amount);
		update_option($key, "yes");
	}
}

/******************************************************************************************************/
function dgx_donate_disable_giving_level($amount)
{
	if (dgx_donate_is_valid_giving_level($amount))
	{
		$key = dgx_donate_get_giving_level_key($amount);
		delete_option($key);
	}
}

/******************************************************************************************************/
function dgx_donate_is_giving_level_enabled($amount)
{
	$levelEnabled = false;

	if (dgx_donate_is_valid_giving_level($amount))
	{
		$key = dgx_donate_get_giving_level_key($amount);
		$value = get_option($key);
		if (!empty($value))
		{
			$levelEnabled = true;
		}
	}

	return $levelEnabled;
}

/******************************************************************************************************/
function dgx_donate_get_giving_level_key($amount)
{
	$key = "dgx_donate_giving_level_" . $amount;

	return $key;
}

/******************************************************************************************************/
function dgx_donate_format_amount($amount)
{
	$formattedAmount = "$" . $amount;

	return $formattedAmount;
}

/******************************************************************************************************/
function dgx_donate_queue_stylesheet() {
	$styleurl = plugins_url('/css/styles.css', __FILE__);
	wp_register_style('dgx_donate_css', $styleurl);
	wp_enqueue_style('dgx_donate_css');
}
add_action('wp_print_styles', 'dgx_donate_queue_stylesheet');

/**********************************************************************************************************/
function dgx_donate_queue_admin_stylesheet() {
        $styleurl = plugins_url('/css/adminstyles.css', __FILE__);

        wp_register_style('dgx_donate_admin_css', $styleurl);
        wp_enqueue_style('dgx_donate_admin_css');
}
add_action('admin_print_styles', 'dgx_donate_queue_admin_stylesheet');

/******************************************************************************************************/
function dgx_donate_queue_scripts() {

	// This queues the scripts used by the core and by paypalstd

	wp_enqueue_script('jquery');
	$scripturl = plugins_url('/js/script.js',__FILE__); 
	wp_enqueue_script('dgx_donate_script', $scripturl, array('jquery'));

	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	wp_localize_script('dgx_donate_script', 'dgxDonateAjax',
		array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('dgx-donate-nonce')
		)
	);
}
add_action('init','dgx_donate_queue_scripts');

/******************************************************************************************************/
function dgx_donate_get_version()
{
	$pluginFolder = get_plugins();
	$pluginBasename = plugin_basename(__FILE__);
	$pluginVersion = $pluginFolder[$pluginBasename]['Version'];

	return $pluginVersion;
}

/******************************************************************************************************/
function dgx_donate_display_thank_you()
{
	$output .= "<p>";
	$thankYouText = get_option('dgx_donate_thanks_text');
	$thankYouText = nl2br($thankYouText);
	$output .= $thankYouText;
	$output .= "</p>";

	return $output;
}

/******************************************************************************************************/
function dgx_donate_debug_log($message)
{
	$max_log_line_count = 200;

	$debug_log = get_option( 'dgx_donate_log' );

	if ( empty( $debug_log )) {
		$debug_log = array();
	}

	$timestamp = strftime("%m-%d-%G %H:%M:%S");

	$debug_log[] = $timestamp . ' ' . $message;

	if ( count( $debug_log ) > $max_log_line_count ) {
		$debug_log = array_slice( $debug_log, -$max_log_line_count, 0 );
	}

	update_option( 'dgx_donate_log', $debug_log );
}

/******************************************************************************************************/
function dgx_donate_create_post($postData)
{
	// Create a new donation record
	
	// Get all the dates - timezone fix thanks to pkwooster
	$gmtOffset = -get_option('gmt_offset');
	$phpTimezone = date_default_timezone_get();
	if ($gmtOffset > 0)
	{
		$timezone = 'Etc/GMT+' . $gmtOffset;
	}
	else
	{
		$timezone = 'Etc/GMT' . $gmtOffset;
	}
	date_default_timezone_set($timezone);
	
	$year = date('Y');
	$month = date('m');
	$day = date('d');
	$yearMonthDay = date('Y-m-d');
	$time = date('g:i:s A');
	$dateTime = date('Y-m-d H:i:s');
	
	// set the PHP timezone back the way it was
	date_default_timezone_set($phpTimezone);
	
	// the title is Lastname, Firstname (YYYY-MM-dd)
	$postTitle = $lastName . ", " . $firstName . " (" . $yearMonthDay . ")";

	$newDonation = array(
		'post_title' => $postTitle,
		'post_content' => '',
		'post_status' => 'publish',
		'post_date' => $dateTime,
		'post_author' => 1,
		'post_type' => 'dgx-donation'
	);

	$donationID = wp_insert_post($newDonation);
	
	// Save all the meta
	update_post_meta($donationID, '_dgx_donate_year', $year);
	update_post_meta($donationID, '_dgx_donate_month', $month);
	update_post_meta($donationID, '_dgx_donate_day', $day);	
	update_post_meta($donationID, '_dgx_donate_time', $time);	
	
	update_post_meta($donationID, '_dgx_donate_amount', $postData['AMOUNT']);
	update_post_meta($donationID, '_dgx_donate_repeating', $postData['REPEATING']);
	update_post_meta($donationID, '_dgx_donate_designated', $postData['DESIGNATED']);
	update_post_meta($donationID, '_dgx_donate_designated_fund', $postData['DESIGNATEDFUND']);
	
	update_post_meta($donationID, '_dgx_donate_tribute_gift', $postData['TRIBUTEGIFT']);
	update_post_meta($donationID, '_dgx_donate_memorial_gift', $postData['MEMORIALGIFT']);
	update_post_meta($donationID, '_dgx_donate_honoree_name', $postData['HONOREENAME']);
	update_post_meta($donationID, '_dgx_donate_honor_by_email', $postData['HONORBYEMAIL']);
	update_post_meta($donationID, '_dgx_donate_honoree_email_name', $postData['HONOREEEMAILNAME']);
	update_post_meta($donationID, '_dgx_donate_honoree_email', $postData['HONOREEEMAIL']);
	update_post_meta($donationID, '_dgx_donate_honoree_post_name', $postData['HONOREEPOSTNAME']);
	update_post_meta($donationID, '_dgx_donate_honoree_address', $postData['HONOREEADDRESS']);
	update_post_meta($donationID, '_dgx_donate_honoree_city', $postData['HONOREECITY']);
	update_post_meta($donationID, '_dgx_donate_honoree_state', $postData['HONOREESTATE']);	
	update_post_meta($donationID, '_dgx_donate_honoree_zip', $postData['HONOREEZIP']);
	
	update_post_meta($donationID, '_dgx_donate_donor_first_name', $postData['FIRSTNAME']);
	update_post_meta($donationID, '_dgx_donate_donor_last_name', $postData['LASTNAME']);
	update_post_meta($donationID, '_dgx_donate_donor_phone', $postData['PHONE']);
	update_post_meta($donationID, '_dgx_donate_donor_email', $postData['EMAIL']);
	update_post_meta($donationID, '_dgx_donate_add_to_mailing_list', $postData['ADDTOMAILINGLIST']);
	update_post_meta($donationID, '_dgx_donate_donor_address', $postData['ADDRESS']);
	update_post_meta($donationID, '_dgx_donate_donor_address2', $postData['ADDRESS2']);
	update_post_meta($donationID, '_dgx_donate_donor_city', $postData['CITY']);
	update_post_meta($donationID, '_dgx_donate_donor_state', $postData['STATE']);
	update_post_meta($donationID, '_dgx_donate_donor_zip', $postData['ZIP']);
	
	update_post_meta($donationID, '_dgx_donate_increase_to_cover', $postData['INCREASETOCOVER']);
	update_post_meta($donationID, '_dgx_donate_anonymous', $postData['ANONYMOUS']);
	update_post_meta($donationID, '_dgx_donate_payment_method', $postData['PAYMENTMETHOD']);
	
	return $donationID;
}

/******************************************************************************************************/
function dgx_donate_get_donation_detail_link($donationID)
{
	$detailUrl = get_admin_url();
	$detailUrl .= "admin.php?page=dgx_donate_menu_page&donation=" . $donationID;
	
	return $detailUrl;
}

function dgx_donate_get_donor_detail_link($donorEmail)
{
	$detailUrl = get_admin_url();
	
	// TODO: URLENCODE?
	$detailUrl .= "admin.php?page=dgx_donate_menu_page&donor=" . $donorEmail;
	
	return $detailUrl;
}

/******************************************************************************************************/
function dgx_donate_init () {

	// Start Session
	$sessionID = $_COOKIE['dgxdonate'];

	if (!empty($sessionID))
	{
		session_id($sessionID);
		session_start();
	}
	else
	{
		// Shopping carts last for no more than 7 days
		session_start();
		$sessionID = session_id();
		$domainName = $_SERVER['HTTP_HOST'];
		setcookie("dgxdonate", $sessionID, time()+60*60*24*7, "/", $domainName);
	}

	// Register CPT
	register_post_type('dgx-donation',
		array(
			'labels' => array(
				'name' => __('Donations'),
				'singular_name' => __('Donation'),
				'add_new_item' => __('Add New Donation'),
				'edit_item' => __('Edit Donation'),
				'new_item' => __('New Donation'),
				'view_item' => __('View Donation'),
				'menu_name' => __('Donations Received')
			),
			'supports' => array(
				'title',
				'author'
			),
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_menu' => false,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'has_archive' => false
		)
	);

	// Initialize options to defaults as needed
	dgx_donate_init_defaults();

	// Display an admin notice if we are in sandbox mode

	$payPalServer = get_option('dgx_donate_paypal_server');
	if (strcasecmp($payPalServer, "SANDBOX") == 0)
	{
		add_action('admin_notices', 'dgx_donate_admin_sandbox_msg');
	}
}
add_action('init', 'dgx_donate_init');

/******************************************************************************************************/
function dgx_donate_admin_sandbox_msg()
{
	echo "<div class=\"error\">";
	echo "<p>";
	echo "Warning - Seamless Donations is currently configured to use the Sandbox (Test Server).  ";
	echo "</p>";
    echo "</div>";
}

/******************************************************************************************************/
function dgx_donate_get_state_selector($selectName, $selectInitialValue)
{
	$output = "<select name=\"$selectName\">";
	
	$states = array(
		'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL',
		'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME',
		'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH',
		'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI',
		'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI',
		'WY');

	foreach ($states as $state)
	{
		$selected = "";
		if (strcasecmp($selectInitialValue, $state) == 0)
		{
			$selected = " selected ";
		}
		$output .= "<option value=\"$state\" $selected> $state </option>\n";
	}
	
	$output .= "</select>";
	
	return $output;
}

/******************************************************************************************************/
function dgx_donate_get_month_year_selector($monthSelectName, $yearSelectName)
{
	$output = "<select name=\"$monthSelectName\">";

	for ($month = 1; $month <= 12; $month++)
	{
		$formattedMonth = sprintf("%02u", $month);
		$output .= "<option value=\"$formattedMonth\"> $formattedMonth </option>\n";
	}
	
	$output .= "</select>";
	
	$output .= " / ";
	
	$output .= "<select name=\"$yearSelectName\">";
	
	$startYear = date('Y');
	$startYear = intval($startYear);
	$endYear = $startYear + 15;
	
	for ($year = $startYear; $year <= $endYear; $year++)
	{
		$output .= "<option value=\"$year\"> $year </option>\n";
	}
	
	$output .= "</select>";

	return $output;
	
}

/******************************************************************************************************/
function dgx_donate_get_donation_section($formContent)
{
	$repeating = false;
	if ($repeating)
	{
		$checkRepeating = " checked ";
	}
	
	$designatedFund = false;
	if ($designatedFund)
	{
		$checkDesignated = " checked ";
	}
	
	$anonymous = false;
	
	$output .= "<div class=\"dgx-donate-form-section\">\n";
	$output .= "<h2>Donation Information</h2>\n";
	
	$output .= "<p>I would like to make a donation in the amount of:</p>";

	$output .= "<p>";
	$checked = " checked=\"checked\" ";
	$classmod = "";
	$givingLevels = dgx_donate_get_giving_levels();
	foreach ($givingLevels as $givingLevel)
	{
		$key = dgx_donate_get_giving_level_key($givingLevel);

		if (dgx_donate_is_giving_level_enabled($givingLevel))
		{
			$formattedAmount = dgx_donate_format_amount($givingLevel);
			$output .= "<input $classmod type=\"radio\" name=\"_dgx_donate_amount\" value=\"$givingLevel\" $checked /> $formattedAmount ";
			$checked = ""; // only select the first one
			$classmod = " class=\"horiz\" "; // only classmod the second and higher ones
		}
	}
	$output .= "</p>";

	$output .= "<p><input type=\"radio\" name=\"_dgx_donate_amount\" value=\"OTHER\" id=\"dgx-donate-other-radio\" $checkOTHER /> Other: ";
	$output .= "<input type=\"text\" class=\"aftertext\" id=\"dgx-donate-other-input\" name=\"_dgx_donate_user_amount\" />";
	$output .= "</p>\n";
	
	// $output .= "<hr/>";
	
	$output .= "</div>\n"; /* dgx-donate-form-section */

	$formContent .= $output;

	return $formContent;
}

/******************************************************************************************************/
function dgx_donate_get_tribute_section($formContent)
{
	$isTributeGift = false;
	$honorByEmail = true;
	$honoreeName = '';
	$honoreeEmail = '';
	$honoreeAddress = '';
	$honoreeCity = '';
	$honoreeState = 'WA';
	$honoreeZip = '';

	if ($isTributeGift)
	{
		$checkTribute = " checked ";
	}
	if ($honorByEmail)
	{
		$checkHonorEmail = " checked ";
	}
	else
	{
		$checkHonorPostal = " checked ";
	}

	$output .= "<div class=\"dgx-donate-form-section\">\n";
	$output .= "<h2>Tribute Gift</h2>\n";
	$output .= "<div class=\"dgx-donate-form-expander\">\n";	
	$output .= "<p class=\"dgx-donate-form-expander-header\">";
	$output .= "<input type=\"checkbox\" id=\"dgx-donate-tribute\" name=\"_dgx_donate_tribute_gift\" $checkTribute /> Check here to donate in honor or memory of someone </p>\n";
	$output .= "<div class=\"dgx-donate-form-tribute-box\">\n";
	$output .= "<p>";
	$output .= "<input type=\"checkbox\" name=\"_dgx_donate_memorial_gift\" /> Check here if this is a memorial gift ";
	$output .= "</p>\n";
	$output .= "<hr/>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_honoree_name\">Honoree's Name: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_honoree_name\" size=\"30\" value=\"$honoreeName\" />";
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<input type=\"radio\" name=\"_dgx_donate_honor_by_email\" value=\"TRUE\" $checkHonorEmail /> Send acknowledgement via email to ";
	$output .= "</p>";
	$output .= "<div class=\"dgx-donate-form-subsection\">";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_honoree_address\">Name: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_honoree_email_name\" size=\"30\" value=\"$honoreeEmailRecipient\" />";
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_honoree_email\">Email: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_honoree_email\" size=\"30\" value=\"$honoreeEmail\" />";
	$output .= "</p>";
	$output .= "</div>";
	$output .= "<p>";	
	$output .= "<input type=\"radio\" name=\"_dgx_donate_honor_by_email\" value=\"FALSE\" $checkHonorPostal /> Send acknowledgement via postal mail to ";
	$output .= "</p>";
	$output .= "<div class=\"dgx-donate-form-subsection\">";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_honoree_address\">Name: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_honoree_post_name\" size=\"30\" value=\"$honoreePostalRecipient\" />";
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_honoree_address\">Address: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_honoree_address\" size=\"30\" value=\"$honoreeAddress\" />";
	$output .= "</p>";	
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_honoree_city\">City: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_honoree_city\" value=\"$honoreeCity\" />";
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_honoree_state\">State: </label>";
	$output .= dgx_donate_get_state_selector("_dgx_donate_honoree_state", $honoreeState);
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_honoree_zip\">Zip: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_honoree_zip\"  size=\"10\" value=\"$honoreeZip\" />";
	$output .= "</p>";
	$output .= "</div>"; /* dgx-donate-form-subsection */
	$output .= "</div>"; /* dgx-donate-form-tribute-box */
	$output .= "</div>"; /* dgx-donate-form-expander */
	$output .= "</div>\n"; /* dgx-donate-form-section */
	
	$formContent .= $output;

	return $formContent;
}

/******************************************************************************************************/
function dgx_donate_get_donor_section($formContent)
{	
	$addToList = false; /* default is not to add them */
	
	if ($addToList)
	{
		$checkAddMailingList = " checked ";
	}

	$output .= "<div class=\"dgx-donate-form-section\">\n";
	$output .= "<h2>Donor Information</h2>\n";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_donor_first_name\">First Name: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_donor_first_name\" value=\"$donorFirstName\" />";
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_donor_last_name\">Last Name: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_donor_last_name\" value=\"$donorLastName\" />";
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_donor_phone\">Phone:  </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_donor_phone\" value=\"$donorPhone\" />";
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_donor_email\">Email: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_donor_email\"  size=\"40\" value=\"$donorEmail\" />";
	$output .= "</p>";

	$output .= "<p><input type=\"checkbox\" name=\"_dgx_donate_add_to_mailing_list\" $checkAddMailingList /> Add me to your mailing list</p>\n";
	
	$output .= "<p>";
	$output .=    "<input type=\"checkbox\" name=\"_dgx_donate_anonymous\" $anonymous />";
	$output .=    "Please do not publish my name.  I would like to remain anonymous.";
	$output .= "</p>\n";
	
	$output .= "</div>\n";
	
	$formContent .= $output;

	return $formContent;
}

/******************************************************************************************************/
function dgx_donate_get_billing_section($formContent)
{
	$donorState = get_option('dgx_donate_default_state');

	$output .= "<div class=\"dgx-donate-form-section\">\n";
	$output .= "<h2>Billing Information</h2>\n";
	
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_donor_address\">Address: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_donor_address\"  size=\"40\" value=\"$donorAddress\" />";
	$output .= "</p>";	
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_donor_address2\">Address 2: <span class=\"dgx-donate-comment\">(optional)</span> </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_donor_address2\"  size=\"40\" value=\"$donorAddress2\" />";
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_donor_city\">City: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_donor_city\" value=\"$donorCity\" /> ";
	$output .= "</p>";
	$output .= "<p>";	
	$output .= "<label for=\"_dgx_donate_donor_state\">State: </label>";
	$output .= dgx_donate_get_state_selector("_dgx_donate_donor_state", $donorState);
	$output .= "</p>";
	$output .= "<p>";
	$output .= "<label for=\"_dgx_donate_donor_zip\">Zip: </label>";
	$output .= "<input type=\"text\" name=\"_dgx_donate_donor_zip\"  size=\"10\" value=\"$donorZip\" />";
	$output .= "</p>";	
	
	$output .= "</div>\n";
	
	$formContent .= $output;

	return $formContent;
}

/******************************************************************************************************/
add_shortcode('dgx-donate', 'dgx_donate_shortcode');

function dgx_donate_shortcode($atts)
{
	$thanks = $_GET['thanks'];
	
	// Sanitize
	$thanks = trim($thanks);
	$thanks = strip_tags($thanks);
	$thanks = htmlspecialchars($thanks);
	
	// Switch
	if (!empty($thanks))
	{
		$output = dgx_donate_display_thank_you();
	}
	else
	{
		$output = "";
		$output = apply_filters('dgx_donate_donation_form', $output);

		if (empty($output))
		{
			$output = "<p>Error: No payment gateway selected.  Please choose a payment gateway in Seamless Donations >> Settings.</p>";
		}
	}
	
	return $output;
}

/******************************************************************************************************/
function dgx_donate_send_thank_you_email($donationID, $testAddress="")
{
	if (!empty($testAddress))
	{
		// Fill in dummy data
		$toEmail = $testAddress;
		// firstname
		$firstName = "Jane";
		// lastname
		$lastName = "Doe";
		// amount
		$amount = "$100.00";
		// fundname
		$fund = "Tesla Scholarship";
		// recurring y/n
		$recurring = "TRUE";
		// designated y/n
		$designated = "TRUE";
		// anonymous y/n
		$anonymous = "TRUE";
		// mailinglistjoin y/n
		$mailingListJoin = "TRUE";
		// tribute y/n
		$tribute = "TRUE";
	}
	else
	{
		// Get data from donationID
		$toEmail = get_post_meta($donationID, '_dgx_donate_donor_email', true);
		// firstname
		$firstName = get_post_meta($donationID, '_dgx_donate_donor_first_name', true);
		// lastname
		$lastName = get_post_meta($donationID, '_dgx_donate_donor_last_name', true);
		// amount
		$amount = get_post_meta($donationID, '_dgx_donate_amount', true);
		$amount = "$" . number_format($amount, 2);
		// fundname
		$fund = get_post_meta($donationID, '_dgx_donate_designated_fund', true);
		// recurring y/n
		$recurring = get_post_meta($donationID, '_dgx_donate_repeating', true);
		// designated y/n
		$designated = get_post_meta($donationID, '_dgx_donate_designated', true);
		// anonymous y/n
		$anonymous = get_post_meta($donationID, '_dgx_donate_anonymous', true);
		// mailinglistjoin y/n
		$mailingListJoin = get_post_meta($donationID, '_dgx_donate_add_to_mailing_list', true);
		// tribute y/n
		$tribute = get_post_meta($donationID, '_dgx_donate_tribute_gift', true);
	}
	
    $replyEmail = get_option('dgx_donate_email_reply');
    $subject = get_option('dgx_donate_email_subj');
    $subject = stripslashes($subject);
    
    $body = get_option('dgx_donate_email_body');
    $body = str_replace("[firstname]", $firstName, $body);
    $body = str_replace("[lastname]", $lastName, $body);
    $body = str_replace("[amount]", $amount, $body);
    $body = str_replace("[fund]", $fund, $body);
    $body = stripslashes($body);
    $emailBody = $body;
    $emailBody .= "\n\n";
    
    // if (!empty($recurring))
    // {
    //	$text = get_option('dgx_donate_email_recur');
    //	$text = str_replace("[amount]", $amount, $text);
    //	$text = stripslashes($text);
    //	$emailBody .= $text;
    //	$emailBody .= "\n\n";
	// }

    // if (!empty($designated))
    // {
    //	$text = get_option('dgx_donate_email_desig');
    //	$text = str_replace("[fund]", $fund, $text);
    //	$text = stripslashes($text);
    //	$emailBody .= $text;
    //	$emailBody .= "\n\n";
	// }	
	
    if (!empty($anonymous))
    {
    	$text = get_option('dgx_donate_email_anon');
     	$text = stripslashes($text);
    	$emailBody .= $text;
    	$emailBody .= "\n\n";
	}	

    if (!empty($mailingListJoin))
    {
    	$text = get_option('dgx_donate_email_list');
    	$text = stripslashes($text);
    	$emailBody .= $text;
    	$emailBody .= "\n\n";
	}	

    if (!empty($tribute))
    {
    	$text = get_option('dgx_donate_email_trib');
    	$text = stripslashes($text);
    	$emailBody .= $text;
    	$emailBody .= "\n\n";
	}
	
	$text = get_option('dgx_donate_email_close');
    $text = stripslashes($text);
    $emailBody .= $text;
    $emailBody .= "\n\n";
    
	$text = get_option('dgx_donate_email_sig');
    $text = stripslashes($text);
    $emailBody .= $text;
    $emailBody .= "\n";    
	
    $headers = "From: $replyEmail\r\n";

    mail($toEmail, $subject, $emailBody, $headers); 
}

/******************************************************************************************************/
function dgx_donate_send_donation_notification($donationID)
{
    $fromEmail = get_option('dgx_donate_reply_email');
	$subject = "[Donate] A donation has been received";
	$body = "A donation has been received.  Here are some details about the donation.\n";
	$body .= "\n";
	
	$body .= "Donor:\n";
	$firstName = get_post_meta($donationID, '_dgx_donate_donor_first_name', true);
	$lastName = get_post_meta($donationID, '_dgx_donate_donor_last_name', true);
	$city = get_post_meta($donationID, '_dgx_donate_donor_city', true);
	$state = get_post_meta($donationID, '_dgx_donate_donor_state', true);
	$zip = get_post_meta($donationID, '_dgx_donate_donor_zip', true);
	$donorEmail = get_post_meta($donationID, '_dgx_donate_donor_email', true);	
	$body .= "$firstName $lastName\n";
	$body .= "$city $state $zip\n";
	$body .= "$donorEmail\n";
	$body .= "\n";

	$tributeGift = get_post_meta($donationID, '_dgx_donate_tribute_gift', true);
	if (!empty($tributeGift))
	{
		$body .= "NOTE:  The donor is making this donation in honor of / in memory of someone.  Please see ";
		$body .= "the donation details (using the link below) for more information.\n";
		$body .= "\n";
	}
	
	$amount = get_post_meta($donationID, '_dgx_donate_amount', true);
	$formattedDonationAmount = "$" . number_format($amount, 2);
	$body .= "Donation:\n";
	$body .= "Amount: $formattedDonationAmount\n";
	
	$body .= "\n";
	$body .= "Click on the following link to view all details for this donation:\n";
	$secureDonateLink = dgx_donate_get_donation_detail_link($donationID);
	$donateLink = str_replace("https:", "http:", $secureDonateLink);
	$body .= $donateLink;
	$body .= "\n";
    
    // Loop on addresses
    $notifyEmails = get_option('dgx_donate_notify_emails');
    $notifyEmailAr = explode(',', $notifyEmails);

	foreach ($notifyEmailAr as $notifyEmail)
	{
		$notifyEmail = trim($notifyEmail);
		if (!empty($notifyEmail))
		{
    		$headers = "From: $fromEmail\r\n";

    		mail($notifyEmail, $subject, $body, $headers);
    	}
    }
}

