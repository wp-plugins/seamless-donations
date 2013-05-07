<?php

/* Copyright 2013 Allen Snook (email: allen@allensnook.com) */

/******************************************************************************************************/
function dgx_donate_add_menus()
{
	add_action( 'dgx_donate_menu', 'dgx_donate_donation_report_menu', 1 );
	add_action( 'dgx_donate_menu', 'dgx_donate_donor_report_menu', 3 );
	add_action( 'dgx_donate_menu', 'dgx_donate_email_template_menu', 9 );
	add_action( 'dgx_donate_menu', 'dgx_donate_thank_you_menu', 11 );
	add_action( 'dgx_donate_menu', 'dgx_donate_settings_menu', 13 );
	add_action( 'dgx_donate_menu', 'dgx_donate_debug_log_menu', 15 );
}

add_action( 'init', 'dgx_donate_add_menus' );

/******************************************************************************************************/
function dgx_donate_echo_admin_footer()
{
	$pluginVersion = dgx_donate_get_version();

	echo "<p class=\"dgxdonateadminfooter\">Seamless Donations $pluginVersion</p>";
}

add_action('dgx_donate_admin_footer', 'dgx_donate_echo_admin_footer');

/******************************************************************************************************/
function dgx_donate_donation_report_menu()
{
	add_submenu_page("dgx_donate_menu_page", __('Donations'), __('Donations'), 'manage_options', 'dgx_donate_donation_report_page', dgx_donate_donation_report_page);	
}

function dgx_donate_donor_report_menu()
{
	add_submenu_page("dgx_donate_menu_page", __('Donors'), __('Donors'), 'manage_options', 'dgx_donate_donor_report_page', dgx_donate_donor_report_page);
}

function dgx_donate_email_template_menu()
{
	add_submenu_page("dgx_donate_menu_page", __('Thank You Emails'), __('Thank You Emails'), 'manage_options', 'dgx_donate_template_page', dgx_donate_template_page);
}

function dgx_donate_thank_you_menu()
{
	add_submenu_page("dgx_donate_menu_page", __('Thank You Page'), __('Thank You Page'), 'manage_options', 'dgx_donate_thank_you_page', dgx_donate_thank_you_page);
}

function dgx_donate_settings_menu()
{
	add_submenu_page("dgx_donate_menu_page", __('Settings'), __('Settings'), 'manage_options', 'dgx_donate_settings_page', dgx_donate_settings_page);
}

function dgx_donate_debug_log_menu() {
	add_submenu_page( "dgx_donate_menu_page", __('Log'), __('Log'), 'manage_options', 'dgx_donate_debug_log_page', dgx_donate_debug_log_page);
}

/******************************************************************************************************/
function dgx_donate_admin_menu()
{
	add_menu_page("Seamless Donations", "Seamless Donations", "manage_options", "dgx_donate_menu_page", "dgx_donate_menu_page");

	do_action('dgx_donate_menu');
}
add_action('admin_menu', 'dgx_donate_admin_menu', 9);

/******************************************************************************************************/
function dgx_donate_init_defaults()
{
	// Thank you email option defaults
	$fromEmail = get_option('dgx_donate_email_reply');
	if (empty($fromEmail))
	{
		$fromEmail = get_option('admin_email');
		update_option('dgx_donate_email_reply', $fromEmail);
	}
	
	$thankSubj = get_option('dgx_donate_email_subj');
	if (empty($thankSubj))
	{
		$thankSubj = "Thank you for your donation";
		update_option('dgx_donate_email_subj', $thankSubj);
	}

	$bodyText = get_option('dgx_donate_email_body');
	if (empty($bodyText))
	{
		$bodyText = "Dear [firstname] [lastname],\n\n";
		$bodyText .= "Thank you for your generous donation of [amount]. Please note that no goods ";
		$bodyText .= "or services were received in exchange for this donation.";
		update_option('dgx_donate_email_body', $bodyText);
	}
	
	$recurringText = get_option('dgx_donate_email_recur');
	if (empty($recurringText))
	{
		$recurringText = "Thank you for electing to have your donation automatically repeated each month.  The next automatic ";
		$recurringText .= "donation will occur in one month and continue until cancelled.  If you would like to cancel your ";
		$recurringText .= "recurring donation at any time, please send us an email.";
		update_option('dgx_donate_email_recur', $recurringText);
	}
	
	$designatedText = get_option('dgx_donate_email_desig');
	if (empty($designatedText))
	{
		$designatedText = "Your donation has been designated to the [fund] fund.";
		update_option('dgx_donate_email_desig', $designatedText);
	}

	$anonymousText = get_option('dgx_donate_email_anon');
	if (empty($anonymousText))
	{
		$anonymousText = "You have requested that your donation be kept anonymous.  Your name will not be revealed to the public.";
		update_option('dgx_donate_email_anon', $anonymousText);
	}

	$mailingListJoinText = get_option('dgx_donate_email_list');
	if (empty($mailingListJoinText))
	{
		$mailingListJoinText = "Thank you for joining our mailing list.  We will send you updates from time-to-time.  If ";
		$mailingListJoinText .= "at any time you would like to stop receiving emails, please send us an email to be ";
		$mailingListJoinText .= "removed from the mailing list.";
		update_option('dgx_donate_email_list', $mailingListJoinText);
	}

	$tributeText = get_option('dgx_donate_email_trib');
	if (empty($tributeText))
	{
		$tributeText = "You have asked to make this donation in honor of or memory of someone else.  Thank you!  We will notify the ";
		$tributeText .= "honoree within the next 5-10 business days.";
		update_option('dgx_donate_email_trib', $tributeText);
	}

	$closingText = get_option('dgx_donate_email_close');
	if (empty($closingText))
	{
		$closingText = "Thanks again for your support!";
		update_option('dgx_donate_email_close', $closingText);
	}

	$signature = get_option('dgx_donate_email_sig');
	if (empty($signature))
	{
		$signature = "Director of Donor Relations";
		update_option('dgx_donate_email_sig', $signature);
	}
	
	//// PayPal defaults
	$notifyEmails = get_option('dgx_donate_notify_emails');
	if (empty($notifyEmails))
	{
		$notifyEmails = get_option('admin_email');
		update_option('dgx_donate_notify_emails', $notifyEmails);
	}
	
	$paymentGateway = get_option('dgx_donate_payment_gateway');
	if (empty($paymentGateway))
	{
		update_option('dgx_donate_payment_gateway', DGXDONATEPAYPALSTD);
	}
	
	$payPalServer = get_option('dgx_donate_paypal_server');
	if (empty($payPalServer))
	{
		update_option('dgx_donate_paypal_server', 'LIVE');
	}

	$paypal_email = get_option( 'dgx_donate_paypal_email' );
	if ( ! is_email( $paypal_email ) ) {
		update_option( 'dgx_donate_paypal_email', '' );
	}
	
	// Thank you page default
	$thankYouText = get_option('dgx_donate_thanks_text');
	if (empty($thankYouText))
	{
		$message = "Thank you for donating!  A thank you email with the details of your donation ";
		$message .= "will be sent to the email address you provided.";
		update_option('dgx_donate_thanks_text', $message);
	}

	// Giving levels default
	$givingLevels = dgx_donate_get_giving_levels();
	$noneChecked = true;
	foreach ($givingLevels as $givingLevel)
	{
		$levelEnabled = dgx_donate_is_giving_level_enabled($givingLevel);
		if ($levelEnabled)
		{
			$noneChecked = false;
		}
	}
	if ($noneChecked)
	{
		// Select 1000, 500, 100, 50 by default
		dgx_donate_enable_giving_level(1000);
		dgx_donate_enable_giving_level(500);
		dgx_donate_enable_giving_level(100);
		dgx_donate_enable_giving_level(50);
	}

	// State default
	$defaultState = get_option('dgx_donate_default_state');
	if (empty($defaultState))
	{
		update_option('dgx_donate_default_state', 'WA');
	}

	// Show Tribute Gift section default
	$show_tribute_section = get_option( 'dgx_donate_show_tribute_section' );
	if ( empty( $show_tribute_section ) ) {
		update_option( 'dgx_donate_show_tribute_section', 'true' );
	}
}

/******************************************************************************************************/
function dgx_donate_menu_page()
{
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    
	$donorID = $_GET['donor'];
	$donationID = $_GET['donation'];
	
	if (!empty($donorID))
	{
		dgx_donate_donor_detail_page($donorID);	
	}
	else if (!empty($donationID))
	{
		dgx_donate_donation_detail_page($donationID);	
	}
	else
	{
		dgx_donate_main_page();
	}
}

/******************************************************************************************************/
function dgx_donate_donor_detail_page($donorID)
{
	echo "<div class=\"wrap\">\n";
	echo "<div id=\"icon-edit-pages\" class=\"icon32\"></div>\n";
	echo "<h2>Donor Detail</h2>\n";
	
	$donorEmail = strtolower($donorID);
	
	$args = array(
		'numberposts'     => '-1',
		'post_type'       => 'dgx-donation',
		'meta_key'		  => '_dgx_donate_donor_email',
		'meta_value'	  => $donorEmail,
		'order'           => 'ASC'
	); 

	$myDonations = get_posts($args);
	
	$args = array(
		'numberposts'     => '1',
		'post_type'       => 'dgx-donation',
		'meta_key'		  => '_dgx_donate_donor_email',
		'meta_value'	  => $donorEmail,
		'order'           => 'DESC'
	); 	
	
	$lastDonation = get_posts($args);
	
	if (count($myDonations) < 1)
	{
		echo "<p>No donations found.</p>";
	}
	else
	{
		echo "<div id=\"col-container\">\n";
		echo "<div id=\"col-right\">\n";
		echo "<div class=\"col-wrap\">\n";
	
		echo "<h3>Donations by This Donor</h3>\n";
		echo "<table class=\"widefat\"><tbody>\n";
		echo "<tr><th>Date</th><th>Fund</th><th>Amount</th></tr>\n";
		$donorTotal = 0;
			
		foreach ($myDonations as $myDonation)
		{
			$donationID = $myDonation->ID;
			
			$year = get_post_meta($donationID, '_dgx_donate_year', true);
			$month = get_post_meta($donationID, '_dgx_donate_month', true);
			$day = get_post_meta($donationID, '_dgx_donate_day', true);
			$time = get_post_meta($donationID, '_dgx_donate_time', true);
			$fundName = "Undesignated";
			$designated = get_post_meta($donationID, '_dgx_donate_designated', true);
			if (!empty($designated))
			{
				$fundName = get_post_meta($donationID, '_dgx_donate_designated_fund', true);
			}
			$amount = get_post_meta($donationID, '_dgx_donate_amount', true);
			$donorTotal = $donorTotal + floatval($amount);
			$formattedAmount = "$" . number_format($amount, 2);				

			$donationDetail = dgx_donate_get_donation_detail_link($donationID);
			echo "<tr><td><a href=\"$donationDetail\">$year-$month-$day $time</a></td>";
			echo "<td>$fundName</td>";
			echo "<td>$formattedAmount</td>";
			echo "</tr>\n";
		}
		$formattedDonorTotal = "$" . number_format($donorTotal, 2);
		echo "<tr><th>&nbsp</th><th>Donor Total</th><td>$formattedDonorTotal</td></tr>\n";
		
		echo "</tbody></table>\n";

		do_action('dgx_donate_donor_detail_right', $donorID);

		do_action('dgx_donate_admin_footer');
	
		echo "</div> <!-- col-wrap -->\n";
		echo "</div> <!-- col-right -->\n";
	
		echo "<div id=\"col-left\">\n";
		echo "<div class=\"col-wrap\">\n";
	
		$donationID = $lastDonation[0]->ID;
	
		$firstName = get_post_meta($donationID, '_dgx_donate_donor_first_name', true);
		$lastName = get_post_meta($donationID, '_dgx_donate_donor_last_name', true);
		$company = get_post_meta($donationID, '_dgx_donate_donor_company_name', true);
		$address1 = get_post_meta($donationID, '_dgx_donate_donor_address', true);
		$address2 = get_post_meta($donationID, '_dgx_donate_donor_address2', true);
		$city = get_post_meta($donationID, '_dgx_donate_donor_city', true);
		$state =  get_post_meta($donationID, '_dgx_donate_donor_state', true);
		$zip = get_post_meta($donationID, '_dgx_donate_donor_zip', true);
		$phone =  get_post_meta($donationID, '_dgx_donate_donor_phone', true);
		$email = get_post_meta($donationID, '_dgx_donate_donor_email', true);
		
		echo "<h3>Donor Information</h3>\n";
		echo "<table class=\"widefat\"><tbody>\n";
		echo "<tr><td>";
		echo "$firstName $lastName<br/>";
		if (!empty($company))
		{
			echo "$company<br/>";
		}
		echo "$address1<br/>";
		if (!empty($address2))
		{
			echo "$address2<br/>";
		}
		echo "$city $state $zip<br/>";
		echo "$phone<br/>";
		echo "$email";
		echo "</td></tr>";
		echo "</tbody></table>\n";

		do_action('dgx_donate_donor_detail_left', $donorID);
	
		echo "</div> <!-- col-wrap -->\n";
		echo "</div> <!-- col-left -->\n";
		echo "</div> <!-- col-container -->\n";
	}
	
	echo "</div> <!-- wrap -->\n"; 
}

/******************************************************************************************************/
function dgx_donate_donation_detail_page($donationID)
{
	// Validate User
    if ( ! current_user_can( 'manage_options' ) )
    {
    	wp_die( __('You do not have sufficient permissions to access this page.') );
    }
	
	// Get form arguments
	$delete_donation = $_POST['delete_donation'];

	// If we have form arguments, we must validate the nonce
	if ( count( $_POST ) > 0 )
	{
		$nonce = $_POST['dgx_donate_donation_detail_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'dgx_donate_donation_detail_nonce' ) )
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}

	echo "<div class=\"wrap\">\n";
	echo "<div id=\"icon-edit-pages\" class=\"icon32\"></div>\n";
	echo "<h2>Donation Detail</h2>\n";

	$donation_deleted = false;
	if ( "true" == $delete_donation ) {
		dgx_donate_debug_log( "Donation (ID: $donationID) deleted" );
		wp_delete_post( $donationID, true ); /* true = force delete / bypass trash */
		$donation_deleted = true;
		$message = __( 'Donation deleted', 'dgx-donate' );
	}

	// Display any message
	if ( ! empty( $message ) )
	{
		echo "<div id=\"message\" class=\"updated below-h2\">\n";
		echo "<p>$message</p>\n";
		echo "</div>\n";
	}

	if ( ! $donation_deleted ) {
		$amount = get_post_meta($donationID, '_dgx_donate_amount', true);
		
		if (empty($amount))
		{
			echo "<p>No such donation found ($donationID).</p>";
		}
		else
		{
			echo "<div id=\"col-container\">\n";
			echo "<div id=\"col-right\">\n";
			echo "<div class=\"col-wrap\">\n";
		
			echo "<h3>Donation Details</h3>\n";
			echo "<table class=\"widefat\"><tbody>\n";

			$year = get_post_meta($donationID, '_dgx_donate_year', true);
			$month = get_post_meta($donationID, '_dgx_donate_month', true);
			$day = get_post_meta($donationID, '_dgx_donate_day', true);
			$time = get_post_meta($donationID, '_dgx_donate_time', true);
			
			echo "<tr><th>Date</th><td>$month/$day/$year $time</td></tr>\n";
			
			$amount = get_post_meta($donationID, '_dgx_donate_amount', true);
			$formattedAmount = "$" . number_format($amount, 2);	
			echo "<tr><th>Amount</th><td>$formattedAmount</td></tr>\n";

			$addToMailingList = get_post_meta($donationID, '_dgx_donate_add_to_mailing_list', true);
			if (!empty($addToMailingList))
			{
				$addToMailingList = "Yes";
			}
			else
			{
				$addToMailingList = "No";
			}
			echo "<tr><th>Add to Mailing List?</th><td>$addToMailingList</td></tr>\n";

			$anonymous = get_post_meta($donationID, '_dgx_donate_anonymous', true);
			if (empty($anonymous))
			{
				$anonymous = "No";
			}
			else
			{
				$anonymous = "Yes";
			}
			echo "<tr><th>Would like to remain anonymous?</th><td>$anonymous</td></tr>\n";

			$fundName = "Undesignated";
			$designated = get_post_meta($donationID, '_dgx_donate_designated', true);
			if (!empty($designated))
			{
				$fundName = get_post_meta($donationID, '_dgx_donate_designated_fund', true);
			}
			echo "<tr><th>Designated Fund</th><td>$fundName</td></tr>\n";

			$tributeGift = get_post_meta($donationID, '_dgx_donate_tribute_gift', true);
			if (empty($tributeGift))
			{
				$tributeGiftMessage = "No";
			}
			else
			{
				$tributeGiftMessage = "Yes - ";

				$honoreeName = get_post_meta($donationID, '_dgx_donate_honoree_name', true);
				$honoreeEmailName = get_post_meta($donationID, '_dgx_donate_honoree_email_name', true);
				$honoreePostName = get_post_meta($donationID, '_dgx_donate_honoree_post_name', true);			
				$honoreeEmail = get_post_meta($donationID, '_dgx_donate_honoree_email', true);
				$honoreeAddress = get_post_meta($donationID, '_dgx_donate_honoree_address', true);
				$honoreeCity = get_post_meta($donationID, '_dgx_donate_honoree_city', true);
				$honoreeState = get_post_meta($donationID, '_dgx_donate_honoree_state', true);
				$honoreeZip = get_post_meta($donationID, '_dgx_donate_honoree_zip', true);
				$memorialGift = get_post_meta($donationID, '_dgx_donate_memorial_gift', true);
				if (empty($memorialGift))
				{
					$tributeGiftMessage .= "in honor of ";
				}
				else
				{
					$tributeGiftMessage .= "in memory of ";
				}
				
				$tributeGiftMessage .= "$honoreeName<br/><br/>";
				if (!empty($honoreeEmail))
				{
					$tributeGiftMessage .= "Send acknowledgement via email to<br/>";
					$tributeGiftMessage .= "$honoreeEmailName<br/>";
					$tributeGiftMessage .= "$honoreeEmail<br/>";
				}
				if (!empty($honoreeAddress))
				{
					$tributeGiftMessage .= "Send acknowledgement via postal mail to<br/>";
					$tributeGiftMessage .= "$honoreePostName<br/>";
					$tributeGiftMessage .= "$honoreeAddress<br/>";
					$tributeGiftMessage .= "$honoreeCity $honoreeState $honoreeZip";				
				}			
			}
			echo "<tr><th>Tribute Gift</th><td>$tributeGiftMessage</td></tr>\n";

			$paymentMethod = get_post_meta($donationID, '_dgx_donate_payment_method', true);
			echo "<tr><th>Payment Method</th><td>$paymentMethod</td></tr>\n";
			
			echo "</tbody></table>\n";

			do_action('dgx_donate_donation_detail_right', $donationID);

			do_action('dgx_donate_admin_footer');
		
			echo "</div> <!-- col-wrap -->\n";
			echo "</div> <!-- col-right -->\n";
		
			echo "<div id=\"col-left\">\n";
			echo "<div class=\"col-wrap\">\n";
		
			$firstName = get_post_meta($donationID, '_dgx_donate_donor_first_name', true);
			$lastName = get_post_meta($donationID, '_dgx_donate_donor_last_name', true);
			$company = get_post_meta($donationID, '_dgx_donate_donor_company_name', true);
			$address1 = get_post_meta($donationID, '_dgx_donate_donor_address', true);
			$address2 = get_post_meta($donationID, '_dgx_donate_donor_address2', true);
			$city = get_post_meta($donationID, '_dgx_donate_donor_city', true);
			$state =  get_post_meta($donationID, '_dgx_donate_donor_state', true);
			$zip = get_post_meta($donationID, '_dgx_donate_donor_zip', true);
			$phone =  get_post_meta($donationID, '_dgx_donate_donor_phone', true);
			$email = get_post_meta($donationID, '_dgx_donate_donor_email', true);
			
			echo "<h3>Donor Information</h3>\n";
			echo "<table class=\"widefat\"><tbody>\n";
			echo "<tr><td>";
			echo "$firstName $lastName<br/>";
			if (!empty($company))
			{
				echo "$company<br/>";
			}
			echo "$address1<br/>";
			if (!empty($address2))
			{
				echo "$address2<br/>";
			}
			echo "$city $state $zip<br/>";
			echo "$phone<br/>";
			echo "$email";
			echo "</td></tr>";
			echo "</tbody></table>\n";

			echo "<h3>Delete this Donation</h3>";
			echo "<p>Click the following button to delete this donation.  This will also remove this ";
			echo "donation from all reports.  This operation cannot be undone.</p>";

			echo "<form method=\"POST\" action=\"\">\n";
			$nonce = wp_create_nonce( 'dgx_donate_donation_detail_nonce' );
			echo "<input type=\"hidden\" name=\"dgx_donate_donation_detail_nonce\" id=\"dgx_donate_donation_report_nonce\" value=\"$nonce\" />\n";	
			echo "<input type=\"hidden\" name=\"delete_donation\" value=\"true\" />";
			echo "<p><input class=\"button\" type=\"submit\" value=\"Delete Donation\" ></p>\n";
			echo "</form>";

			do_action('dgx_donate_donation_detail_left', $donationID);
		
			echo "</div> <!-- col-wrap -->\n";
			echo "</div> <!-- col-left -->\n";
			echo "</div> <!-- col-container -->\n";
		}
	}
	
	echo "</div> <!-- wrap -->\n"; 
}

/******************************************************************************************************/
function dgx_donate_main_page()
{
	echo "<div class=\"wrap\">\n";
	echo "<div id=\"icon-edit-pages\" class=\"icon32\"></div>\n";
	echo "<h2>Seamless Donations for WordPress</h2>\n";
	
	echo "<div id=\"col-container\">\n";
	echo "<div id=\"col-right\">\n";
	echo "<div class=\"col-wrap\">\n";
	
	echo "<h3>Recent Donations</h3>\n";
	
	$args = array(
		'numberposts'     => '5',
		'post_type'       => 'dgx-donation'
	); 

	$myDonations = get_posts($args);
	
	if (count($myDonations) > 0)
	{
		echo "<table class=\"widefat\"><tbody>\n";
		echo "<tr><th>Date</th><th>Donor</th><th>Amount</th></tr>\n";
		
		foreach ($myDonations as $myDonation)
		{
			$donationID = $myDonation->ID;
			
			$year = get_post_meta($donationID, '_dgx_donate_year', true);
			$month = get_post_meta($donationID, '_dgx_donate_month', true);
			$day = get_post_meta($donationID, '_dgx_donate_day', true);
			$time = get_post_meta($donationID, '_dgx_donate_time', true);
			$donationDate = $month . "/" . $day . "/" . $year;
			
			$firstName = get_post_meta($donationID, '_dgx_donate_donor_first_name', true);
			$lastName = get_post_meta($donationID, '_dgx_donate_donor_last_name', true);
			$donorEmail = get_post_meta($donationID, '_dgx_donate_donor_email', true);
			$donorDetail = dgx_donate_get_donor_detail_link($donorEmail);
			
			$amount = get_post_meta($donationID, '_dgx_donate_amount', true);
			$formattedAmount = "$" . number_format($amount, 2);
			
			$donationDetail = dgx_donate_get_donation_detail_link($donationID);
			echo "<tr><td><a href=\"$donationDetail\">$donationDate $time</a></td>";
			echo "<td><a href=\"$donorDetail\">$firstName $lastName</a></td>";
			echo "<td>$formattedAmount</td></tr>\n";
		}
		
		echo "</tbody></table>\n";
	}
	else
	{
		echo "<p>No donations found.</p>\n";
	}

	do_action('dgx_donate_main_page_right');

	do_action('dgx_donate_admin_footer');
	
	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-right -->\n";
	
	echo "<div id=\"col-left\">\n";
	echo "<div class=\"col-wrap\">\n";
	
	echo "<h3>View Donations</h3>\n";
	echo "<p>View all the donations you've received.</p>\n";	
	$actionUrl = "'" . get_admin_url() . "admin.php?page=dgx_donate_donation_report_page" . "'";
	echo "<form>\n";
	echo "<p><input class=\"button\" type=\"button\" value=\"View Donations\" onClick=\"parent.location=$actionUrl\"></p>\n";
	echo "</form>";

	echo "<h3>View Donors</h3>\n";
	echo "<p>View donations by date and donor.</p>\n";	
	$actionUrl = "'" . get_admin_url() . "admin.php?page=dgx_donate_donor_report_page" . "'";
	echo "<form>\n";
	echo "<p><input class=\"button\" type=\"button\" value=\"View Donors\" onClick=\"parent.location=$actionUrl\"></p>\n";
	echo "</form>";

	echo "<h3>Settings</h3>\n";
	echo "<p>Update giving levels, payment gateway and email settings</p>\n";	
	$actionUrl = "'" . get_admin_url() . "admin.php?page=dgx_donate_settings_page" . "'";
	echo "<form>\n";
	echo "<p><input class=\"button\" type=\"button\" value=\"Manage Settings\" onClick=\"parent.location=$actionUrl\"></p>\n";
	echo "</form>";

	do_action('dgx_donate_main_page_left');

	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-left -->\n";
	echo "</div> <!-- col-container -->\n";

	echo "</div> <!-- wrap -->\n";
}

/******************************************************************************************************/
function dgx_donate_donation_report_page()
{
	echo "<div class=\"wrap\">\n";
	echo "<div id=\"icon-edit-pages\" class=\"icon32\"></div>\n";
	
	echo "<h2>Donations</h2>\n";
	
	// Validate User
    if (!current_user_can('manage_options'))
    {
    	wp_die( __('You do not have sufficient permissions to access this page.') );
    }
	
	// Get form arguments
	$startDate = $_POST['startdate'];
	$endDate = $_POST['enddate'];

	// If we have form arguments, we must validate the nonce
	if (count($_POST) > 0)
	{
		$nonce = $_POST['dgx_donate_donation_report_nonce'];
		if (!wp_verify_nonce($nonce, 'dgx_donate_donation_report_nonce'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}

	if (empty($startDate))
	{
		// Set the first day of the current year as the start date
		$startYear = date('Y'); // ok not to worry about timezone here
		$startMonth = 1;
		$startDay = 1;
	}
	else
	{
		// Split on m/d/y
		$dateArray = explode("/", $startDate);
		$startMonth = $dateArray[0];
		$startDay = $dateArray[1];
		$startYear = $dateArray[2];
		
		if ($startMonth < 1)
		{
			$startMonth = 1;
		}
		
		if ($startMonth > 12)
		{
			$startMonth = 12;
		}
		
		if ($startDay < 1)
		{
			$startDay = 1;
		}
		
		if ($startDay > 31)
		{
			$startDay = 31;
		}
		
		if ($startYear < 100)
		{
			$startYear = 2000 + $startYear;
		}
	}
	
	if (empty($endDate))
	{
		// Set the last day of the current year as the end date
		$endYear = date('Y'); // ok not to worry about timezone here
		$endMonth = 12;
		$endDay = 31;
	}
	else
	{
		// Split on m/d/y
		$dateArray = explode("/", $endDate);
		$endMonth = $dateArray[0];
		$endDay = $dateArray[1];
		$endYear = $dateArray[2];
		
		if ($endMonth < 1)
		{
			$endMonth = 1;
		}
		
		if ($endMonth > 12)
		{
			$endMonth = 12;
		}
		
		if ($endDay < 1)
		{
			$endDay = 1;
		}
		
		if ($endDay > 31)
		{
			$endDay = 31;
		}
		
		if ($endYear < 100)
		{
			$endYear = 2000 + $endYear;
		}
	}
	
	// Create the date strings for editing next time
	$startDate = $startMonth . "/" . $startDay . "/" . $startYear;
	$endDate = $endMonth . "/" . $endDay . "/" . $endYear;

	do_action('dgx_donate_donations_page_load');
	
	echo "<div id=\"col-container\">\n";
	echo "<div id=\"col-right\">\n";
	echo "<div class=\"col-wrap\">\n";

	echo "<h3>Donation Report for $startDate to $endDate</h3>\n";

	$args = array(
		'numberposts'     => '-1',
		'post_type'       => 'dgx-donation',
		'order'			  => 'ASC'
	); 

	$myDonations = get_posts($args);
	
	// Scan all the donations for that date range
	
	// Build a hashmap of funds (don't forget to handle undesignated) to an array of donationIDs
	// Sort by fund name, put undesignated last?
	// Loop through the hashmap, printing the fund, its donations, total for that fund
	// Finally, print a total for the entire timeperiod
	
	if (count($myDonations) > 0)
	{
		$myFunds = array();
		
		foreach ($myDonations as $myDonation)
       	{ 
			$donationID = $myDonation->ID;
				
			$okToAdd = true;

			$startTimeStamp = strtotime($startDate);
			$endTimeStamp = strtotime($endDate);

			$year = get_post_meta($donationID, '_dgx_donate_year', true);
			$month = get_post_meta($donationID, '_dgx_donate_month', true);
			$day = get_post_meta($donationID, '_dgx_donate_day', true);
			$donationDate = $month . "/" . $day . "/" . $year;
			$donationTimeStamp = strtotime($donationDate);
			
			if ($donationTimeStamp < $startTimeStamp)
			{
				$okToAdd = false;
			}
			
			if ($donationTimeStamp > $endTimeStamp)
			{
				$okToAdd = false;
			}
				
       		if ($okToAdd)
       		{

				$designatedFund = "Undesignated";
				$designated = get_post_meta($donationID, '_dgx_donate_designated', true);
				if (!empty($designated))
				{
					$designatedFund = get_post_meta($donationID, '_dgx_donate_designated_fund', true);
				}
				
				if (array_key_exists($designatedFund, $myFunds))
				{
					$tempArray = $myFunds[$designatedFund];
					$tempArray[] = $donationID;
					$myFunds[$designatedFund] = $tempArray;
				}
				else
				{
					$myFunds[$designatedFund] = array($donationID);
				}
			}
		}
		
		ksort($myFunds);
		
		// Start the table
		echo "<table class=\"widefat\"><tbody>\n";
		echo "<tr><th>Fund/Date</th><th>Donor</th><th>Amount</th></tr>\n";
		
		// Now, loop on the funds and then the donation IDs inside them
		
		$grandTotal = 0;
		
		foreach ($myFunds as $myFund => $fundDonationIDs)
		{
			$fundTotal = 0;
			
			$fundCount = count($fundDonationIDs);
			echo "<tr><th colspan=\"3\">$myFund ($fundCount)</th></tr>\n";
			foreach ($fundDonationIDs as $donationID)
			{
				$year = get_post_meta($donationID, '_dgx_donate_year', true);
				$month = get_post_meta($donationID, '_dgx_donate_month', true);
				$day = get_post_meta($donationID, '_dgx_donate_day', true);
				$time = get_post_meta($donationID, '_dgx_donate_time', true);
				$firstName = get_post_meta($donationID, '_dgx_donate_donor_first_name', true);
				$lastName = get_post_meta($donationID, '_dgx_donate_donor_last_name', true);
				$amount = get_post_meta($donationID, '_dgx_donate_amount', true);
				$fundTotal = $fundTotal + floatval($amount);
				$formattedAmount = "$" . number_format($amount, 2);				
				
				$donationDetail = dgx_donate_get_donation_detail_link($donationID);
				$donorEmail = get_post_meta($donationID, '_dgx_donate_donor_email', true);
				$donorDetail = dgx_donate_get_donor_detail_link($donorEmail);
				
				echo "<tr><td><a href=\"$donationDetail\">$year-$month-$day $time</a></td>";
				echo "<td><a href=\"$donorDetail\">$firstName $lastName</a></td>";
				echo "<td>$formattedAmount</td>";
				echo "</tr>\n";
			}
			$formattedFundTotal = "$" . number_format($fundTotal, 2);
			echo "<tr><th>&nbsp</th><th>Fund Subtotal</th><td>$formattedFundTotal</td></tr>\n";
			$grandTotal = $grandTotal + $fundTotal;
		}
		
		$formattedGrandTotal = "$" . number_format($grandTotal, 2);
		echo "<tr><th>&nbsp</th><th>Grand Total</th><td>$formattedGrandTotal</td></tr>\n";
		
		echo "</tbody></table>\n";
	}
	else
	{
		echo "<p>No donations found.</p>\n";
	}

	do_action('dgx_donate_donations_page_right');
	
	do_action('dgx_donate_admin_footer');

	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-right -->\n";
	
	echo "<div id=\"col-left\">\n";
	echo "<div class=\"col-wrap\">\n";

	if ( count( $myDonations ) > 0 )
	{
		$exportUrl = plugins_url( '/dgx-donate-export.php', __FILE__ );
		echo "<h3>Export Report as Spreadsheet (CSV)</h3>\n";
		echo "<p>Click the following button to export detailed information for each donation in this report to a ";
		echo "comma-separated-value (CSV) file compatible with most spreadsheet software.</p>";
		echo "<form method=\"POST\" action=\"$exportUrl\">\n";
		echo "<input type=\"hidden\" name=\"startdate\" value=\"$startDate\" size=\"12\"/>";
		echo "<input type=\"hidden\" name=\"enddate\" value=\"$endDate\" size=\"12\"/>";
		echo "</p><p>";
		echo "<input id=\"submit\" class=\"button\" type=\"submit\" value=\"Export Report\" name=\"submit\"></p>\n";
		echo "</form>";	
		echo "<hr/>";
	}

	echo "<h3>Date Range</h3>\n";
	echo "<form method=\"POST\" action=\"\">\n";
	
	$nonce = wp_create_nonce('dgx_donate_donation_report_nonce');
	echo "<input type=\"hidden\" name=\"dgx_donate_donation_report_nonce\" id=\"dgx_donate_donation_report_nonce\" value=\"$nonce\" />\n";	

	echo "<p>Start Date: ";
	echo "<input type=\"text\" name=\"startdate\" value=\"$startDate\" size=\"12\"/>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo " End Date: ";
	echo "<input type=\"text\" name=\"enddate\" value=\"$endDate\" size=\"12\"/>";
	echo "</p><p>";
	echo "<input id=\"submit\" class=\"button\" type=\"submit\" value=\"Update\" name=\"submit\"></p>\n";
	echo "</form>";

	do_action('dgx_donate_donations_page_left');
	
	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-left -->\n";
	echo "</div> <!-- col-container -->\n";

	echo "</div> <!-- wrap -->\n"; 
}

/******************************************************************************************************/
function dgx_donate_donor_report_page()
{
	echo "<div class=\"wrap\">\n";
	echo "<div id=\"icon-edit-pages\" class=\"icon32\"></div>\n";
	
	echo "<h2>Donors</h2>\n";

	// Validate user
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
	
	// Get form arguments
	$startDate = $_POST['startdate'];
	$endDate = $_POST['enddate'];

	// If we have form arguments, we must validate the nonce
	if (count($_POST) > 0)
	{
		$nonce = $_POST['dgx_donate_donor_report_nonce'];
		if (!wp_verify_nonce($nonce, 'dgx_donate_donor_report_nonce'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}

	if (empty($startDate))
	{
		// Set the first day of the current year as the start date
		$startYear = date('Y'); // ok not to worry about timezone here
		$startMonth = 1;
		$startDay = 1;
	}
	else
	{
		// Split on m/d/y
		$dateArray = explode("/", $startDate);
		$startMonth = $dateArray[0];
		$startDay = $dateArray[1];
		$startYear = $dateArray[2];
		
		if ($startMonth < 1)
		{
			$startMonth = 1;
		}
		
		if ($startMonth > 12)
		{
			$startMonth = 12;
		}
		
		if ($startDay < 1)
		{
			$startDay = 1;
		}
		
		if ($startDay > 31)
		{
			$startDay = 31;
		}
		
		if ($startYear < 100)
		{
			$startYear = 2000 + $startYear;
		}
	}
	
	if (empty($endDate))
	{
		// Set the last day of the current year as the end date
		$endYear = date('Y'); // ok not to worry about timezone here
		$endMonth = 12;
		$endDay = 31;
	}
	else
	{
		// Split on m/d/y
		$dateArray = explode("/", $endDate);
		$endMonth = $dateArray[0];
		$endDay = $dateArray[1];
		$endYear = $dateArray[2];
		
		if ($endMonth < 1)
		{
			$endMonth = 1;
		}
		
		if ($endMonth > 12)
		{
			$endMonth = 12;
		}
		
		if ($endDay < 1)
		{
			$endDay = 1;
		}
		
		if ($endDay > 31)
		{
			$endDay = 31;
		}
		
		if ($endYear < 100)
		{
			$endYear = 2000 + $endYear;
		}
	}
	
	// Create the date strings for editing next time
	$startDate = $startMonth . "/" . $startDay . "/" . $startYear;
	$endDate = $endMonth . "/" . $endDay . "/" . $endYear;

	do_action('dgx_donate_donors_page_load');
	
	echo "<div id=\"col-container\">\n";
	echo "<div id=\"col-right\">\n";
	echo "<div class=\"col-wrap\">\n";

	echo "<h3>Donor Report for $startDate to $endDate</h3>\n";

	$args = array(
		'numberposts'     => '-1',
		'post_type'       => 'dgx-donation',
		'order'			  => 'ASC'
	); 

	$myDonations = get_posts($args);
	
	// Scan all the donations for that date range
	
	// Build a hashmap of donor email addresses to an array of donor names
	// Build a hashmap of donor email addresses to an array of donationIDs
	// Sort by donor email address
	// Loop through the hashmap, printing the donor, their donations, total for that fund
	// Finally, print a total for the entire timeperiod
	
	if (count($myDonations) > 0)
	{
		$myDonorEmails = array();
		$myDonorNames = array();
		
		foreach ($myDonations as $myDonation)
       	{ 
			$donationID = $myDonation->ID;
				
			$okToAdd = true;

			$startTimeStamp = strtotime($startDate);
			$endTimeStamp = strtotime($endDate);

			$year = get_post_meta($donationID, '_dgx_donate_year', true);
			$month = get_post_meta($donationID, '_dgx_donate_month', true);
			$day = get_post_meta($donationID, '_dgx_donate_day', true);
			$donationDate = $month . "/" . $day . "/" . $year;
			$donationTimeStamp = strtotime($donationDate);
			
			if ($donationTimeStamp < $startTimeStamp)
			{
				$okToAdd = false;
			}
			
			if ($donationTimeStamp > $endTimeStamp)
			{
				$okToAdd = false;
			}
				
       		if ($okToAdd)
       		{
				$donorEmail = get_post_meta($donationID, '_dgx_donate_donor_email', true);
				
				if (array_key_exists($donorEmail, $myDonorEmails))
				{
					$tempArray = $myDonorEmails[$donorEmail];
					$tempArray[] = $donationID;
					$myDonorEmails[$donorEmail] = $tempArray;
				}
				else
				{
					$firstName = get_post_meta($donationID, '_dgx_donate_donor_first_name', true);
					$lastName = get_post_meta($donationID, '_dgx_donate_donor_last_name', true);
					$myDonorEmails[$donorEmail] = array($donationID);
					$myDonorNames[$donorEmail] = $firstName . " " . $lastName;
				}
			}
		}
		
		ksort($myDonorEmails);
		
		// Start the table
		echo "<table class=\"widefat\"><tbody>\n";
		echo "<tr><th>Donor/Date</th><th>Fund</th><th>Amount</th></tr>\n";
		
		// Now, loop on the funds and then the donation IDs inside them
		
		$grandTotal = 0;
		
		foreach ($myDonorEmails as $myDonorEmail => $donorDonationIDs)
		{
			$donorTotal = 0;
			
			$donorName = $myDonorNames[$myDonorEmail];
			$donorCount = count($donorDonationIDs);
			$donorDetail = dgx_donate_get_donor_detail_link($donorEmail);
			echo "<tr><th colspan=\"3\"><a href=\"$donorDetail\">$donorName ($donorCount)</a></th></tr>\n";
			foreach ($donorDonationIDs as $donationID)
			{
				$year = get_post_meta($donationID, '_dgx_donate_year', true);
				$month = get_post_meta($donationID, '_dgx_donate_month', true);
				$day = get_post_meta($donationID, '_dgx_donate_day', true);
				$time = get_post_meta($donationID, '_dgx_donate_time', true);
				$fundName = "Undesignated";
				$designated = get_post_meta($donationID, '_dgx_donate_designated', true);
				if (!empty($designated))
				{
					$fundName = get_post_meta($donationID, '_dgx_donate_designated_fund', true);
				}
				$amount = get_post_meta($donationID, '_dgx_donate_amount', true);
				$donorTotal = $donorTotal + floatval($amount);
				$formattedAmount = "$" . number_format($amount, 2);				

				$donationDetail = dgx_donate_get_donation_detail_link($donationID);
				echo "<tr><td><a href=\"$donationDetail\">$year-$month-$day $time</a></td>";
				echo "<td>$fundName</td>";
				echo "<td>$formattedAmount</td>";
				echo "</tr>\n";
			}
			$formattedDonorTotal = "$" . number_format($donorTotal, 2);
			echo "<tr><th>&nbsp</th><th>Donor Subtotal</th><td>$formattedDonorTotal</td></tr>\n";
			$grandTotal = $grandTotal + $donorTotal;
		}
		
		$formattedGrandTotal = "$" . number_format($grandTotal, 2);
		echo "<tr><th>&nbsp</th><th>Grand Total</th><td>$formattedGrandTotal</td></tr>\n";
		
		echo "</tbody></table>\n";
	}
	else
	{
		echo "<p>No donors found.</p>\n";
	}

	do_action('dgx_donate_donors_page_right');

	do_action('dgx_donate_admin_footer');
	
	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-right -->\n";
	
	echo "<div id=\"col-left\">\n";
	echo "<div class=\"col-wrap\">\n";	
	
	echo "<h3>Date Range</h3>\n";
	echo "<form method=\"POST\" action=\"\">\n";

	$nonce = wp_create_nonce('dgx_donate_donor_report_nonce');
	echo "<input type=\"hidden\" name=\"dgx_donate_donor_report_nonce\" id=\"dgx_donate_donor_report_nonce\" value=\"$nonce\" />\n";	

	echo "<p>Start Date: ";
	echo "<input type=\"text\" name=\"startdate\" value=\"$startDate\" size=\"12\"/>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo " End Date: ";
	echo "<input type=\"text\" name=\"enddate\" value=\"$endDate\" size=\"12\"/>";
	echo "</p><p>";
	echo "<input id=\"submit\" class=\"button\" type=\"submit\" value=\"Update\" name=\"submit\"></p>\n";
	echo "</form>";

	do_action('dgx_donate_donor_page_left');
	
	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-left -->\n";
	echo "</div> <!-- col-container -->\n";

	echo "</div> <!-- wrap -->\n"; 
}

/******************************************************************************************************/
function dgx_donate_template_page()
{
	if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	// If we have form arguments, we must validate the nonce
	if (count($_POST) > 0)
	{
		$nonce = $_POST['dgx_donate_template_nonce'];
		if (!wp_verify_nonce($nonce, 'dgx_donate_template_nonce'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
		// Otherwise, proceed - get form arguments
		$fromMail = $_POST['frommail'];
		$subject = $_POST['subject'];
		$bodyText = $_POST['bodytext'];
		$recurringText = $_POST['recurringtext'];
		$designatedText = $_POST['designatedtext'];
		$anonymousText = $_POST['anonymoustext'];
		$mailingListJoinText = $_POST['mailinglistjointext'];
		$tributeText = $_POST['tributetext'];
		$closingText = $_POST['closingtext'];
		$signature = $_POST['signature'];

    	// If they made changes, save them
		if (!empty($fromMail))
		{
			update_option('dgx_donate_email_reply', $fromMail);
    		update_option('dgx_donate_email_subj', $subject);
    		update_option('dgx_donate_email_body', $bodyText);
    		update_option('dgx_donate_email_recur', $recurringText);
    		update_option('dgx_donate_email_desig', $designatedText);
    		update_option('dgx_donate_email_anon', $anonymousText);
    		update_option('dgx_donate_email_list', $mailingListJoinText);
    		update_option('dgx_donate_email_trib', $tributeText);
    		update_option('dgx_donate_email_close', $closingText);
    		update_option('dgx_donate_email_sig', $signature);
    		$message = "Templates updated.";
		}
	
    	// If they asked for a test email, send it
    	$testMail = $_POST['testmail'];
    	if (!empty($testMail))
    	{
    		dgx_donate_send_thank_you_email(0, $testMail);
     		$message = "Test email sent.";
    	}		
	}

	do_action('dgx_donate_email_template_page_load');
    
    // Otherwise, proceed

	echo "<div class=\"wrap\">\n";
	echo "<div id=\"icon-edit-pages\" class=\"icon32\"></div>\n";
	echo "<h2>Thank You Emails</h2>\n";    
    
	// Display any message
	if (!empty($message))
	{
		echo "<div id=\"message\" class=\"updated below-h2\">\n";
		echo "<p>$message</p>\n";
		echo "</div>\n";
	}
    
    $fromEmail = get_option('dgx_donate_email_reply');
    $subject = get_option('dgx_donate_email_subj');
    $subject = stripslashes($subject);
    $bodyText = get_option('dgx_donate_email_body');
    $bodyText = stripslashes($bodyText);
    $recurringText = get_option('dgx_donate_email_recur');
    $recurringText = stripslashes($recurringText);
    $designatedText = get_option('dgx_donate_email_desig');
    $designatedText = stripslashes($designatedText);
    $anonymousText = get_option('dgx_donate_email_anon');
    $anonymousText = stripslashes($anonymousText);
    $mailingListJoinText = get_option('dgx_donate_email_list');
    $mailingListJoinText = stripslashes($mailingListJoinText);
    $tributeText = get_option('dgx_donate_email_trib');
    $tributeText = stripslashes($tributeText);
    $closingText = get_option('dgx_donate_email_close');
    $closingText = stripslashes($closingText);
    $signature = get_option('dgx_donate_email_sig');
    $signature = stripslashes($signature);
    
	$nonce = wp_create_nonce('dgx_donate_template_nonce');

	echo "<div id=\"col-container\">\n";
	echo "<div id=\"col-right\">\n";
	echo "<div class=\"col-wrap\">\n";
	
	echo "<h3>Email Template</h3>\n";
	echo "<p>The template on this page is used to generate thank you emails for each donation.  You ";
	echo "can include placeholders such as [firstname] [lastname] [fund] and/or [amount] which will automatically ";
	echo "be filled in with the donor and donation details.";
	echo "</p>\n";
	
	echo "<form method=\"POST\" action=\"\">\n";	
	echo "<input type=\"hidden\" name=\"dgx_donate_template_nonce\" id=\"dgx_donate_template_nonce\" value=\"$nonce\" />\n";	

	echo "<div class=\"form-field\">\n";
	echo "<label for=\"frommail\">From / Reply-To Email Address</label>\n";
	echo "<input type=\"text\" name=\"frommail\" size=\"40\" value=\"$fromEmail\"/>\n";
	echo "<p class=\"description\">The email address the thank you email should appear to come from.</p>\n";
	echo "</div> <!-- form-field --> \n";	
	
	echo "<div class=\"form-field\">\n";
	echo "<label for=\"subject\">Subject</label>\n";
	echo "<input type=\"text\" name=\"subject\" size=\"40\" value=\"$subject\"/>\n";
	echo "<p class=\"description\">The subject of the email (e.g. Thank You for Your Donation).</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	echo "<div class=\"form-field\">\n";
	echo "<label for=\"bodytext\">Body</label><br/>\n";
	echo "<textarea name=\"bodytext\" rows=\"6\" cols=\"40\">$bodyText</textarea>\n";
	echo "<p class=\"description\">The body of the email message to all donors.</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	// echo "<div class=\"form-field\">\n";
	// echo "<label for=\"recurringtext\">Recurring Donations</label><br/>\n";
	// echo "<textarea name=\"recurringtext\" rows=\"3\" cols=\"40\">$recurringText</textarea>\n";
	// echo "<p class=\"description\">This message will be included when the donor elects to make their donation recurring.</p>\n";
	// echo "</div> <!-- form-field --> \n";

	// echo "<div class=\"form-field\">\n";
	// echo "<label for=\"designatedtext\">Designated Fund</label><br/>\n";
	// echo "<textarea name=\"designatedtext\" rows=\"3\" cols=\"40\">$designatedText</textarea>\n";
	// echo "<p class=\"description\">This message will be included when the donor designates their donation to a specific fund.</p>\n";
	// echo "</div> <!-- form-field --> \n";
	
	echo "<div class=\"form-field\">\n";
	echo "<label for=\"anonymoustext\">Anonymous Donations</label><br/>\n";
	echo "<textarea name=\"anonymoustext\" rows=\"3\" cols=\"40\">$anonymousText</textarea>\n";
	echo "<p class=\"description\">This message will be included when the donor requests their donation get kept anonymous.</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	echo "<div class=\"form-field\">\n";
	echo "<label for=\"mailinglistjointext\">Mailing List Join</label><br/>\n";
	echo "<textarea name=\"mailinglistjointext\" rows=\"3\" cols=\"40\">$mailingListJoinText</textarea>\n";
	echo "<p class=\"description\">This message will be included when the donor elects to join the mailing list.</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	echo "<div class=\"form-field\">\n";
	echo "<label for=\"tributetext\">Tribute Gift</label><br/>\n";
	echo "<textarea name=\"tributetext\" rows=\"3\" cols=\"40\">$tributeText</textarea>\n";
	echo "<p class=\"description\">This message will be included when the donor elects to make their donation a tribute gift.</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	echo "<div class=\"form-field\">\n";
	echo "<label for=\"closingtext\">Closing</label><br/>\n";
	echo "<textarea name=\"closingtext\" rows=\"6\" cols=\"40\">$closingText</textarea>\n";
	echo "<p class=\"description\">The closing text of the email message to all donors.</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	echo "<div class=\"form-field\">\n";
	echo "<label for=\"signature\">Signature</label><br/>\n";
	echo "<textarea name=\"signature\" rows=\"6\" cols=\"40\">$signature</textarea>\n";
	echo "<p class=\"description\">The signature at the end of the email message to all donors.</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	echo "<p><input id=\"submit\" class=\"button\" type=\"submit\" value=\"Save Changes\" name=\"submit\"></p>\n";	
	
	echo "</form>";

	do_action('dgx_donate_email_template_page_right');
	
	do_action('dgx_donate_admin_footer');

	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-right -->\n";
	
	echo "<div id=\"col-left\">\n";
	echo "<div class=\"col-wrap\">\n";

	echo "<h3>Send a Test Email</h3>\n";
	echo "<p>Enter an email address (e.g. your own) to have a test email sent using the template.</p>\n";
	
	echo "<form method=\"POST\" action=\"\">\n";
	echo "<input type=\"hidden\" name=\"dgx_donate_template_nonce\" id=\"dgx_donate_template_nonce\" value=\"$nonce\" />\n";	
	
	echo "<div class=\"form-field\">\n";
	echo "<label for=\"testmail\">Email Address</label>\n";
	echo "<input type=\"text\" name=\"testmail\" size=\"40\" />\n";
	echo "<p class=\"description\">The email address to receive the test message.</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	echo "<p><input id=\"submit\" class=\"button\" type=\"submit\" value=\"Send Test Email\" name=\"submit\"></p>\n";
	echo "</form>";

	do_action('dgx_donate_email_template_page_left');
	
	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-left -->\n";
	echo "</div> <!-- col-container -->\n";
	
	echo "</div> <!-- wrap -->\n"; 
}

/******************************************************************************************************/
function dgx_donate_thank_you_page()
{
	if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	// Get form arguments
	$thankYouText = $_POST['thankstext'];

	// If we have form arguments, we must validate the nonce
	if (count($_POST) > 0)
	{
		$nonce = $_POST['dgx_donate_thanks_nonce'];
		if (!wp_verify_nonce($nonce, 'dgx_donate_thanks_nonce'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}

    // If they made changes, save them
	if (!empty($thankYouText))
	{
		update_option('dgx_donate_thanks_text', $thankYouText);
    	$message = "Thank you page content updated.";
	}

	do_action('dgx_donate_thanks_page_load');
    
    // Otherwise, proceed

	echo "<div class=\"wrap\">\n";
	echo "<div id=\"icon-edit-pages\" class=\"icon32\"></div>\n";
	echo "<h2>Email Templates</h2>\n";    
    
	// Display any message
	if (!empty($message))
	{
		echo "<div id=\"message\" class=\"updated below-h2\">\n";
		echo "<p>$message</p>\n";
		echo "</div>\n";
	}
    
    $thankYouText = get_option('dgx_donate_thanks_text');
    $thankYouText = stripslashes($thankYouText);

	$nonce = wp_create_nonce('dgx_donate_thanks_nonce');

	echo "<div id=\"col-container\">\n";
	echo "<div id=\"col-right\">\n";
	echo "<div class=\"col-wrap\">\n";
	
	echo "<h3>Thank You Page</h3>\n";
	
	echo "<form method=\"POST\" action=\"\">\n";	
	echo "<input type=\"hidden\" name=\"dgx_donate_thanks_nonce\" id=\"dgx_donate_thanks_nonce\" value=\"$nonce\" />\n";	
	
	echo "<div class=\"form-field\">\n";
	echo "<label for=\"thankstext\">Thank You Page Text</label><br/>\n";
	echo "<textarea name=\"thankstext\" rows=\"6\" cols=\"40\">$thankYouText</textarea>\n";
	echo "<p class=\"description\">The text to display to the donor after they complete their donation.</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	echo "<p><input id=\"submit\" class=\"button\" type=\"submit\" value=\"Save Changes\" name=\"submit\"></p>\n";	
	
	echo "</form>";

	do_action('dgx_donate_thanks_page_right');

	do_action('dgx_donate_admin_footer');
	
	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-right -->\n";
	
	echo "<div id=\"col-left\">\n";
	echo "<div class=\"col-wrap\">\n";

	echo "<h3>Thank You Page</h3>\n";
	echo "<p>On this page you can configure a special thank you message which will appear to your donors after they ";
	echo "complete their donation.  This is separate from the thank you email that gets emailed to your donor.";
	echo "</p>\n";

	echo "</form>";

	do_action('dgx_donate_thanks_page_left');
	
	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-left -->\n";
	echo "</div> <!-- col-container -->\n";
	
	echo "</div> <!-- wrap -->\n"; 
}

/******************************************************************************************************/
function dgx_donate_save_giving_levels_settings()
{
	$noneEnabled = true;

	$givingLevels = dgx_donate_get_giving_levels();
	foreach ($givingLevels as $givingLevel)
	{
		$key = dgx_donate_get_giving_level_key($givingLevel);
		if (isset($_POST[$key]))
		{
			dgx_donate_enable_giving_level($givingLevel);
			$noneEnabled = false;
		}
		else
		{
			dgx_donate_disable_giving_level($givingLevel);
		}
	}

	// If they are all disabled, at least enable the first one
	if ($noneEnabled)
	{
		dgx_donate_enable_giving_level($givingLevels[0]);
	}
}

/******************************************************************************************************/
function dgx_donate_settings_page()
{
	global $gatewayArray;

    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	// If we have form arguments, we must validate the nonce
	if (count($_POST) > 0)
	{
		$nonce = $_POST['dgx_donate_settings_nonce'];
		if (!wp_verify_nonce($nonce, 'dgx_donate_settings_nonce'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}

	// Save our global settings
    $notificationEmails = $_POST['notifyemails'];
    if (!empty($notificationEmails))
    {
    	update_option('dgx_donate_notify_emails', $notificationEmails);
    	$message = "Settings updated.";
    }

    // Save giving level selections
    $givingLevels = $_POST['dgx_donate_giving_levels'];
    if (!empty($givingLevels))
    {
    	dgx_donate_save_giving_levels_settings();
    	$message = "Settings updated.";
    }

    // Whether to show the tribute section or not
    $show_tribute_section = $_POST['show_tribute_section'];
    if ( ! empty( $show_tribute_section ) ) {
    	if ( "true" == $show_tribute_section ) {
			update_option( 'dgx_donate_show_tribute_section', 'true' );
    	} else {
			update_option( 'dgx_donate_show_tribute_section', 'false' );
    	}
    	$message = "Settings updated.";
    }

    // Save default state
    $defaultState = $_POST['dgx_donate_default_state'];
    if (!empty($defaultState))
    {
    	update_option('dgx_donate_default_state', $defaultState);
    	$message = "Settings updated.";
    }

    // Save each payment gateway's settings
    do_action('dgx_donate_save_settings_forms');
    
    // Set up a nonce
   	$nonce = wp_create_nonce('dgx_donate_settings_nonce');

	echo "<div class=\"wrap\">\n";
	echo "<div id=\"icon-edit-pages\" class=\"icon32\"></div>\n";
	echo "<h2>Settings</h2>\n";    
    
	// Display any message
	if (!empty($message))
	{
		echo "<div id=\"message\" class=\"updated below-h2\">\n";
		echo "<p>$message</p>\n";
		echo "</div>\n";
	}
	
	echo "<div id=\"col-container\">\n";
	echo "<div id=\"col-right\">\n";
	echo "<div class=\"col-wrap\">\n";
	
	// Notification Emails
	echo "<h3>Notification Emails</h3>\n";
	echo "<p>Enter one or more emails that should be notified when a new donation arrives.  You can separate multiple email addresses with commas.</p>";

	$notifyMails = get_option('dgx_donate_notify_emails');
	echo "<form method=\"POST\" action=\"\">\n";
	echo "<input type=\"hidden\" name=\"dgx_donate_settings_nonce\" id=\"dgx_donate_settings_nonce\" value=\"$nonce\" />\n";		

	echo "<div class=\"form-field\">\n";
	echo "<label for=\"notifyemails\">Notification Email Address(es)</label><br/>\n";
	echo "<input type=\"text\" name=\"notifyemails\" value=\"$notifyMails\" />\n";
	echo "<p class=\"description\">Email address(es) that should be notified of new donations.</p>\n";
	echo "</div> <!-- form-field --> \n";
	
	echo "<p><input id=\"submit\" class=\"button\" type=\"submit\" value=\"Update\" name=\"submit\"></p>\n";
	echo "</form>";
	echo "<br/>";

	// Payment gateways
	echo "<h3>Payment Gateways</h3>\n";
	if (has_action('dgx_donate_show_settings_forms'))
	{
		echo "<form method=\"POST\" action=\"\">\n";
		echo "<input type=\"hidden\" name=\"dgx_donate_settings_nonce\" id=\"dgx_donate_settings_nonce\" value=\"$nonce\" />\n";		

		do_action('dgx_donate_show_settings_forms');
			
		echo "<p><input id=\"submit\" class=\"button\" type=\"submit\" value=\"Update\" name=\"submit\"></p>\n";	
		
		echo "</form>";
	}
	else
	{
		echo "<p>Error: No payment gateways found</p>";
	}

	do_action('dgx_donate_settings_page_right');

	do_action('dgx_donate_admin_footer');
	
	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-right -->\n";
	
	echo "<div id=\"col-left\">\n";
	echo "<div class=\"col-wrap\">\n";

	// Giving Levels
	echo "<h3>Giving Levels</h3>";
	echo "<p>Select one or more suggested giving levels for your donors to choose from.</p>";
	echo "<form method=\"POST\" action=\"\">\n";
	echo "<input type=\"hidden\" name=\"dgx_donate_settings_nonce\" id=\"dgx_donate_settings_nonce\" value=\"$nonce\" />\n";
	echo "<input type=\"hidden\" name=\"dgx_donate_giving_levels\" value=\"1\" />";
	$givingLevels = dgx_donate_get_giving_levels();
	foreach ($givingLevels as $givingLevel)
	{
		$key = dgx_donate_get_giving_level_key($givingLevel);
		$checked = "";
		if (dgx_donate_is_giving_level_enabled($givingLevel))
		{
			$checked = " checked ";
		}
		echo "<p><input type=\"checkbox\" name=\"$key\" value=\"yes\" $checked /> $givingLevel </p>";
	}	

	echo "<p><input id=\"submit\" class=\"button\" type=\"submit\" value=\"Update\" name=\"submit\" /></p>\n";
	echo "</form>";
	echo "<br/>";

	// Default state for donor
	echo "<h3>Default State</h3>";
	echo "<p>Select the default state for the donation form.</p>";
	echo "<form method=\"POST\" action=\"\">\n";
	echo "<input type=\"hidden\" name=\"dgx_donate_settings_nonce\" id=\"dgx_donate_settings_nonce\" value=\"$nonce\" />\n";

	$defaultState = get_option('dgx_donate_default_state');
	$selector = dgx_donate_get_state_selector('dgx_donate_default_state', $defaultState);
	echo "<p>$selector</p>";

	echo "<p><input id=\"submit\" class=\"button\" type=\"submit\" value=\"Update\" name=\"submit\" /></p>\n";
	echo "</form>";
	echo "<br/>";

	// Show Tribute Section?
	echo "<h3>Tribute Gift Section</h3>";
	echo "<p>Show or hide the Tribute Gift section of the donation form.</p>";
	echo "<form method=\"POST\" action=\"\">\n";
	echo "<input type=\"hidden\" name=\"dgx_donate_settings_nonce\" id=\"dgx_donate_settings_nonce\" value=\"$nonce\" />\n";

	$show_tribute_section = get_option('dgx_donate_show_tribute_section');
	if ( "true" == $show_tribute_section ) {
		$true_checked = "checked";
	} else {
		$false_checked = "checked";
	}

	echo '<p><input type="radio" name="show_tribute_section" value="true" ' . $true_checked . '/> Show the Tribute Gift form section </p>';
	echo '<p><input type="radio" name="show_tribute_section" value="false" ' . $false_checked . '/> Do not show the Tribute Gift form section</p>';

	echo "<p><input id=\"submit\" class=\"button\" type=\"submit\" value=\"Update\" name=\"submit\" /></p>\n";
	echo "</form>";	

	do_action('dgx_donate_settings_page_left');
	
	echo "</div> <!-- col-wrap -->\n";
	echo "</div> <!-- col-left -->\n";
	echo "</div> <!-- col-container -->\n";
	
	echo "</div> <!-- wrap -->\n"; 
}

/******************************************************************************************************/
function dgx_donate_debug_log_page() {

    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	// If we have form arguments, we must validate the nonce
	if (count($_POST) > 0)
	{
		$nonce = $_POST['dgx_donate_log_nonce'];
		if (!wp_verify_nonce($nonce, 'dgx_donate_log_nonce'))
		{
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	}

    // Save default state
    $log_command = $_POST['dgx_donate_log_cmd'];
    if (!empty($log_command))
    {
    	delete_option( 'dgx_donate_log' );
    	$message = __( 'Log cleared', 'dgx-donate' );
    }

    echo "<div class='wrap'>";
	echo "<div id='icon-edit-pages' class='icon32'></div>";
	echo "<h2>" . __( 'Log', 'dgx-donate' ) . "</h2>";

	// Display any message
	if (!empty($message))
	{
		echo "<div id=\"message\" class=\"updated below-h2\">\n";
		echo "<p>$message</p>\n";
		echo "</div>\n";
	}

	$debug_log_content = get_option( 'dgx_donate_log' );

	if ( empty( $debug_log_content ) ) {
		echo '<p>';
		_e( 'The log is empty.', 'dgx-donate' );
		echo '</p>';
	}
	else {
		echo "<p><textarea readonly style='width: 100%;' rows='20'>";
		foreach ($debug_log_content as $debug_log_entry) {
			echo "$debug_log_entry\n";
		}
		echo "</textarea></p>";

    	// Set up a nonce
   		$nonce = wp_create_nonce('dgx_donate_log_nonce');

		echo "<form method='POST' action=''>";
		echo "<input type='hidden' name='dgx_donate_log_nonce' id='dgx_donate_log_nonce' value='$nonce' />";
		echo "<input type='hidden' name='dgx_donate_log_cmd' value='clear' />";
		echo "<p><input id='submit' class='button' type='submit' value='" . __( 'Clear Log', 'dgx-donate' ) . "' name='submit' /></p>";
		echo "</form>";
	}

	echo "</div> <!-- wrap -->"; 
}

?>