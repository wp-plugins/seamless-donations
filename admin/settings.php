<?php
/*
Seamless Donations by David Gewirtz, adopted from Allen Snook

Lab Notes: http://zatzlabs.com/lab-notes/
Plugin Page: http://zatzlabs.com/seamless-donations/
Contact: http://zatzlabs.com/contact-us/

Copyright (c) 2015 by David Gewirtz
*/

//// SETTINGS - TAB ////
function seamless_donations_admin_settings ( $setup_object ) {

	do_action ( 'seamless_donations_admin_settings_before', $setup_object );

	// create the admin tab menu
	seamless_donations_admin_settings_menu ( $setup_object );

	// create the sections
	seamless_donations_admin_settings_section_emails ( $setup_object );
	seamless_donations_admin_settings_section_paypal ( $setup_object );
	seamless_donations_admin_settings_section_tabs ( $setup_object );
	seamless_donations_admin_settings_section_debug ($setup_object);

	do_action ( 'seamless_donations_admin_settings_after', $setup_object );

	add_filter (
		'validate_page_slug_seamless_donations_admin_settings',
		'validate_page_slug_seamless_donations_admin_settings_callback',
		10, // priority (for this, always 10)
		3 ); // number of arguments passed (for this, always 3)
}

//// SETTINGS - MENU ////
function seamless_donations_admin_settings_menu ( $_setup_object ) {

	$sub_menu_array = array(
		'title'     => __ ( 'Settings', 'seamless-donations' ),
		'page_slug' => 'seamless_donations_admin_settings',
	);
	$sub_menu_array = apply_filters ( 'seamless_donations_admin_settings_menu', $sub_menu_array );
	$_setup_object->addSubMenuPage ( $sub_menu_array );
}

//// SETTINGS - PROCESS ////
function validate_page_slug_seamless_donations_admin_settings_callback (
	$_submitted_array, $_existing_array, $_setup_object ) {

	$_submitted_array = apply_filters (
		'validate_page_slug_seamless_donations_admin_settings_callback',
		$_submitted_array, $_existing_array, $_setup_object );

	$section = seamless_donations_get_submitted_admin_section ( $_submitted_array );

	switch( $section ) {
		case 'seamless_donations_admin_settings_section_emails': // SAVE EMAILS //
			$email = $_submitted_array[ $section ]['dgx_donate_notify_emails'];
			$email = sanitize_email($email);
			if( ! is_email ( $email ) ) {
				$_aErrors[ $section ]['dgx_donate_notify_emails'] = __ (
					'Valid email address required.', 'seamless-donations' );
				$_setup_object->setFieldErrors ( $_aErrors );
				$_setup_object->setSettingNotice (
					__ ( 'There were errors in your submission.', 'seamless-donations' ) );

				return $_existing_array;
			}
			update_option ( 'dgx_donate_notify_emails', $email );
			$_setup_object->setSettingNotice ( 'Form updated successfully.', 'updated' );
			break;
		case 'seamless_donations_admin_settings_section_paypal': // SAVE PAYPAL //
			$email  = $_submitted_array[ $section ]['dgx_donate_paypal_email'];
			$email = sanitize_email($email);
			$option = $_submitted_array[ $section ]['dgx_donate_paypal_server'];
			if( ! is_email ( $email ) ) {
				$_aErrors[ $section ]['dgx_donate_paypal_email'] = __ (
					'Valid email address required.', 'seamless-donations' );
				$_setup_object->setFieldErrors ( $_aErrors );
				$_setup_object->setSettingNotice (
					__ ( 'There were errors in your submission.', 'seamless-donations' ) );

				return $_existing_array;
			}
			update_option ( 'dgx_donate_paypal_email', $email );
			update_option ( 'dgx_donate_paypal_server', $option );
			$_setup_object->setSettingNotice ( 'Form updated successfully.', 'updated' );
			break;
		case 'seamless_donations_admin_settings_section_tabs': // SAVE TABS //
			update_option ( 'dgx_donate_display_admin_donors_tab', 'show' );
			update_option ( 'dgx_donate_display_admin_donations_tab', 'show' );
			update_option ( 'dgx_donate_display_admin_funds_tab', 'show' );
			$_setup_object->setSettingNotice ( 'Form updated successfully.', 'updated' );
			break;
		case 'seamless_donations_admin_settings_section_debug': // SAVE DEBUG //
			update_option (
				'dgx_donate_debug_mode', $_submitted_array[ $section ]['dgx_donate_debug_mode'] );
			$_setup_object->setSettingNotice ( 'Form updated successfully.', 'updated' );
			break;
		case 'seamless_donations_admin_settings_section_extension': // LET EXTENSIONS DO THE PROCESSING
			break;
		default:
			$_setup_object->setSettingNotice (
				__ ( 'There was an unexpected error in your entry.', 'seamless-donations' ) );
	}
}

//// SETTINGS - SECTION - EMAILS ////
function seamless_donations_admin_settings_section_emails ( $_setup_object ) {

	// Test email section
	$section_desc = 'Enter one or more emails that should be notified when a new donation arrives. ';
	$section_desc .= 'You can separate multiple email addresses with commas.';

	$settings_emails_section
		                     = array(
		'section_id'  => 'seamless_donations_admin_settings_section_emails',    // the section ID
		'page_slug'   => 'seamless_donations_admin_settings',    // the page slug that the section belongs to
		'title'       => __ ( 'Notification Emails', 'seamless-donations' ),   // the section title
		'description' => __ ( $section_desc, 'seamless-donations' ),
	);
	$settings_emails_section = apply_filters (
		'seamless_donations_admin_settings_section_emails', $settings_emails_section );

	$settings_emails_options = array(
		array(
			'field_id'    => 'dgx_donate_notify_emails',
			'type'        => 'text',
			'title'       => __ ( 'Notification Email Address(es)', 'seamless-donations' ),
			'description' => __ (
				'Email address(es) that should be notified (e.g. administrators) of new donations.',
				'seamless-donations' ),
			'attributes'  => array(
				'size' => 80,
			),
		),
		array(
			'field_id' => 'submit',
			'type'     => 'submit',
			'label'    => __ ( 'Update', 'seamless-donations' ),
		)
	);
	$settings_emails_options = apply_filters (
		'seamless_donations_admin_settings_section_emails_options', $settings_emails_options );

	seamless_donations_process_add_settings_fields_with_options (
		$settings_emails_options, $_setup_object, $settings_emails_section );
}

//// SETTINGS - SECTION - PAYPAL ////
function seamless_donations_admin_settings_section_paypal ( $_setup_object ) {

	// Test email section
	$section_desc = 'Set up your PayPal deposit information.';

	$settings_paypal_section
		= array(
		'section_id'  => 'seamless_donations_admin_settings_section_paypal',    // the section ID
		'page_slug'   => 'seamless_donations_admin_settings',    // the page slug that the section belongs to
		'title'       => __ ( 'PayPal Settings', 'seamless-donations' ),   // the section title
		'description' => __ ( $section_desc, 'seamless-donations' ),
	);

	$form_display_options = array(
		'LIVE'    => 'Live (Production Server)',
		'SANDBOX' => 'Sandbox (Test Server)',
	);
	$notify_url           = plugins_url ( '/dgx-donate-paypalstd-ipn.php', dirname(__FILE__) );

	$settings_paypal_section = apply_filters (
		'seamless_donations_admin_settings_section_paypal', $settings_paypal_section );

	$settings_paypal_options = array(
		array(
			'field_id'    => 'dgx_donate_paypal_email',
			'type'        => 'text',
			'title'       => __ ( 'PayPal Email Address', 'seamless-donations' ),
			'description' => __ (
				'The email address at which to receive payments.', 'seamless-donations' ),
			'attributes'  => array(
				'size' => 40,
			),
		),
		array(
			'field_id' => 'dgx_donate_paypal_server',
			'title'    => __ ( 'PayPal Interface Mode', 'seamless-donations' ),
			'type'     => 'select',
			'default'  => 'LIVE', // the index key of the label array below
			'label'    => $form_display_options
		),
		array(
			'field_id'     => 'settings_paypal_ipn_url',
			'title'        => __ ( 'PayPal IPN URL', 'seamless-donations' ),
			'type'         => 'ipn_url_html',
			'before_field' => $notify_url,
		),
		array(
			'field_id' => 'submit',
			'type'     => 'submit',
			'label'    => __ ( 'Save PayPal Settings', 'seamless-donations' ),
		)
	);
	$settings_paypal_options = apply_filters (
		'seamless_donations_admin_settings_section_paypal_options', $settings_paypal_options );

	seamless_donations_process_add_settings_fields_with_options (
		$settings_paypal_options, $_setup_object, $settings_paypal_section );
}

//// SETTINGS - SECTION - TABS ////
function seamless_donations_admin_settings_section_tabs ( $_setup_object ) {

	// Test email section
	$section_desc = 'Restore hidden legacy admin tabs. ';
	$section_desc .= "These tabs were hidden because they're no longer relevant to this interface.";

	$settings_tabs_section
		                   = array(
		'section_id'  => 'seamless_donations_admin_settings_section_tabs',    // the section ID
		'page_slug'   => 'seamless_donations_admin_settings',    // the page slug that the section belongs to
		'title'       => __ ( 'Restore Hidden Tabs', 'seamless-donations' ),   // the section title
		'description' => __ ( $section_desc, 'seamless-donations' ),
	);
	$settings_tabs_section = apply_filters (
		'seamless_donations_admin_settings_section_tabs', $settings_tabs_section );

	$settings_tabs_options = array(
		array(
			'field_id' => 'submit',
			'type'     => 'submit',
			'label'    => __ ( 'Show All Tabs', 'seamless-donations' ),
		)
	);
	$settings_tabs_options = apply_filters (
		'seamless_donations_admin_settings_section_tabs_options', $settings_tabs_options );

	seamless_donations_process_add_settings_fields_with_options (
		$settings_tabs_options, $_setup_object, $settings_tabs_section );
}

//// SETTINGS - SECTION - DEBUG ////
function seamless_donations_admin_settings_section_debug ( $_setup_object ) {

	$section_desc = 'Enables certain Seamless Donations debugging features. Reduces security. ';
	$section_desc .= 'Displays annoying (but effective) warning message until turned off.';

	$debug_section = array(
		'section_id'  => 'seamless_donations_admin_settings_section_debug',    // the section ID
		'page_slug'   => 'seamless_donations_admin_settings',    // the page slug that the section belongs to
		'title'       => __ ( 'Debug Mode', 'seamless-donations' ),   // the section title
		'description' => __ ( $section_desc, 'seamless-donations' ),
	);

	$debug_section = apply_filters ( 'seamless_donations_admin_settings_section_debug', $debug_section );

	$debug_options = array(
		array(
			'field_id'    => 'dgx_donate_debug_mode',
			'title'       => __ ( 'Debug Mode', 'seamless-donations' ),
			'type'        => 'checkbox',
			'label'       => __ ( 'Enable debug mode', 'seamless-donations'),
			'default'     => false,
			'after_label' => '<br />',
		),
		array(
			'field_id' => 'submit',
			'type'     => 'submit',
			'label'    => __ ( 'Save Debug Mode', 'seamless-donations' ),
		)
	);

	$debug_options = apply_filters (
		'seamless_donations_admin_settings_section_debug_options', $debug_options );

	seamless_donations_process_add_settings_fields_with_options (
		$debug_options, $_setup_object, $debug_section );
}