<?php

/* Copyright 2013 Allen Snook (email: allendav@allendav.com) */

class Dgx_Donate_Admin_Settings_View {
	function __construct() {
		add_action( 'dgx_donate_menu', array( $this, 'menu_item' ), 13 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}
	
	function menu_item() {
		add_submenu_page(
			'dgx_donate_menu_page',
			__( 'Settings', 'dgx-donate' ),
			__( 'Settings', 'dgx-donate' ),
			'manage_options',
			'dgx_donate_settings_page',
			array( $this, 'menu_page' )
		);
	}
	
	function menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'dgx-donate' ) );
		}

		// If we have form arguments, we must validate the nonce
		if ( count( $_POST ) ) {
			$nonce = $_POST['dgx_donate_settings_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'dgx_donate_settings_nonce' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'dgx-donate' ) );
			}
		}

		// Save our global settings
		$notification_emails = ( isset( $_POST['notifyemails'] ) ) ? $_POST['notifyemails'] : '';
		if ( ! empty( $notification_emails ) ) {
			update_option( 'dgx_donate_notify_emails', $notification_emails );
			$message = __( 'Settings updated.', 'dgx-donate' );
		}

		// Save giving level selections
		$giving_levels = ( isset( $_POST['dgx_donate_giving_levels'] ) ) ? $_POST['dgx_donate_giving_levels'] : '';
		if ( ! empty( $giving_levels ) ) {
			dgx_donate_save_giving_levels_settings();
			$message = __( 'Settings updated.', 'dgx-donate' );
		}

		// Whether to show the tribute section or not
		$show_tribute_section = ( isset( $_POST['show_tribute_section'] ) ) ? $_POST['show_tribute_section'] : '';
		if ( ! empty( $show_tribute_section ) ) {
			if ( "true" == $show_tribute_section ) {
				update_option( 'dgx_donate_show_tribute_section', 'true' );
			} else {
				update_option( 'dgx_donate_show_tribute_section', 'false' );
			}
			$message = __( 'Settings updated.', 'dgx-donate' );
		}

		// Whether to show the employer section or not
		$show_employer_section = ( isset( $_POST['show_employer_section'] ) ) ? $_POST['show_employer_section'] : '';
		if ( ! empty( $show_employer_section ) ) {
			if ( "true" == $show_employer_section ) {
				update_option( 'dgx_donate_show_employer_section', 'true' );
			} else {
				update_option( 'dgx_donate_show_employer_section', 'false' );
			}
			$message = "Settings updated.";
		}

		// Where to load the scripts
		$scripts_in_footer = ( isset( $_POST['scripts_in_footer'] ) ) ? $_POST['scripts_in_footer'] : '';
		if ( ! empty( $scripts_in_footer ) ) {
			if ( "true" == $scripts_in_footer ) {
				update_option( 'dgx_donate_scripts_in_footer', 'true' );
			} else {
				update_option( 'dgx_donate_scripts_in_footer', 'false' );
			}
			$message = __( 'Settings updated.', 'dgx-donate' );
		}

		// Save currency
		$currency = ( isset( $_POST['dgx_donate_currency'] ) ) ? $_POST['dgx_donate_currency'] : '';
		if ( ! empty( $currency ) ) {
			update_option( 'dgx_donate_currency', $currency );
			$message = __( 'Settings updated.', 'dgx-donate' );
		}

		// Save default country
		$default_country = ( isset( $_POST['dgx_donate_default_country'] ) ) ? $_POST['dgx_donate_default_country'] : '';
		if ( ! empty( $default_country ) ) {
			update_option( 'dgx_donate_default_country', $default_country );
			$message = __( 'Settings updated.', 'dgx-donate' );
		}

		// Save default state
		$default_state = ( isset( $_POST['dgx_donate_default_state'] ) ) ? $_POST['dgx_donate_default_state'] : '';
		if ( ! empty( $default_state ) ) {
			update_option( 'dgx_donate_default_state', $default_state );
			$message = __( 'Settings updated.', 'dgx-donate' );
		}

		// Save default province
		$default_province = ( isset( $_POST['dgx_donate_default_province'] ) ) ? $_POST['dgx_donate_default_province'] : '';
		if ( ! empty( $default_province ) ) {
			update_option( 'dgx_donate_default_province', $default_province );
			$message = __( 'Settings updated.', 'dgx-donate' );
		}

		// Save each payment gateway's settings
		do_action( 'dgx_donate_save_settings_forms' );

		// Set up a nonce
		$nonce = wp_create_nonce( 'dgx_donate_settings_nonce' );

		echo "<div class='wrap'>\n";
		echo "<div id='icon-edit-pages' class='icon32'></div>\n";
		echo "<h2>" . esc_html__( 'Settings', 'dgx-donate' ) . "</h2>\n";

		// Display any message
		if ( ! empty( $message ) ) {
			echo "<div id='message' class='updated below-h2'>\n";
			echo "<p>" . esc_html( $message ) . "</p>\n";
			echo "</div>\n";
		}
		
		echo "<div id='col-container'>\n";
		echo "<div id='col-right'>\n";
		echo "<div class='col-wrap'>\n";
		
		// Notification Emails
		echo "<h3>" . esc_html__( 'Notification Emails', 'dgx-donate' ) . "</h3>\n";
		echo "<p>" . esc_html__( 'Enter one or more emails that should be notified when a new donation arrives.  You can separate multiple email addresses with commas.', 'dgx-donate' ). "</p>";
	
		$notify_emails = get_option('dgx_donate_notify_emails');
		echo "<form method='POST' action=''>\n";
		echo "<input type='hidden' name='dgx_donate_settings_nonce' value='" . esc_attr( $nonce ) . "' />\n";
	
		echo "<div class='form-field'>\n";
		echo "<label for='notifyemails'>" . esc_html__( 'Notification Email Address(es)', 'dgx-donate' ) . "</label><br/>\n";
		echo "<input type='text' name='notifyemails' value='" . esc_attr( $notify_emails ) ."' />\n";
		echo "<p class='description'>" . esc_html__( 'Email address(es) that should be notified (e.g. administrators) of new donations.', 'dgx-donate' ) . "</p>\n";
		echo "</div>\n";
		
		echo "<p><input id='submit' class='button' type='submit' value='" . esc_attr__( 'Update', 'dgx-donate' ) . "' name='submit'></p>\n";
		echo "</form>";
		echo "<br/>";
	
		// Payment gateways
		echo "<h3>" . esc_html( 'Payment Gateways', 'dgx-donate' ) . "</h3>\n";
		if ( has_action( 'dgx_donate_show_settings_forms' ) ) {
			echo "<form method='POST' action=''>\n";
			echo "<input type='hidden' name='dgx_donate_settings_nonce' value='" . esc_attr( $nonce ) . "' />\n";
	
			do_action( 'dgx_donate_show_settings_forms' );
			echo "<p><input id='submit' class='button' type='submit' value='" . esc_html__( 'Update', 'dgx-donate' ) . "' name='submit'></p>\n";
			echo "</form>";
		} else {
			echo "<p>" . esc_html__( 'Error: No payment gateways found', 'dgx-donate' ) . "</p>";
		}

		do_action( 'dgx_donate_settings_page_right' );
		do_action( 'dgx_donate_admin_footer' );
		
		echo "</div>\n";
		echo "</div>\n";
		
		echo "<div id='col-left'>\n";
		echo "<div class='col-wrap'>\n";

		// Giving Levels
		echo "<h3>" . esc_html__( 'Giving Levels', 'dgx-donate' ) . "</h3>";
		echo "<p>" . esc_html__( 'Select one or more suggested giving levels for your donors to choose from.', 'dgx-donate' ) . "</p>";
		echo "<form method='POST' action=''>\n";
		echo "<input type='hidden' name='dgx_donate_settings_nonce' value='" . esc_attr( $nonce ) . "' />\n";
		echo "<input type='hidden' name='dgx_donate_giving_levels' value='1' />";
		$giving_levels = dgx_donate_get_giving_levels();
		foreach ( $giving_levels as $giving_level ) {
			$key = dgx_donate_get_giving_level_key( $giving_level );
			echo "<p><input type='checkbox' name='" . esc_attr( $key ) . "' value='yes' ";
			checked( dgx_donate_is_giving_level_enabled( $giving_level ) );
			echo " />" . esc_html( $giving_level ) . "</p>";
		}

		echo "<p><input id='submit' class='button' type='submit' value='" . esc_attr__( 'Update', 'dgx-donate' ) . "' name='submit' /></p>\n";
		echo "</form>";
		echo "<br/>";

		// Currency
		echo "<h3>" . esc_html__( 'Currency', 'dgx-donate' ) . "</h3>";
		echo "<p>" . esc_html__( "Select the currency you'd like to receive donations in.", 'dgx-donate' ) . "</p>";
		echo "<form method='POST' action=''>\n";
		echo "<input type='hidden' name='dgx_donate_settings_nonce' value='" . esc_attr( $nonce ) . "' />\n";
		$currency = get_option( 'dgx_donate_currency' );
		echo "<p>";
		echo dgx_donate_get_currency_selector( 'dgx_donate_currency', $currency );
		echo "</p>";
		echo "<p><input id='submit' class='button' type='submit' value='" . esc_attr__( 'Update', 'dgx-donate' ) . "' name='submit' /></p>\n";
		echo "</form>";
		echo "<br/>";

		// Default country/state/province for donor
		// jQuery will take care of hiding / showing the state and province selector based on the country code
		echo "<h3>" . esc_html__( 'Default Country / State / Province', 'dgx-donate' ) . "</h3>";
		echo "<p>" . esc_html__( 'Select the default country / state / province for the donation form.', 'dgx-donate' ) . "</p>";

		echo "<div class='dgx_donate_geography_selects'>";
		echo "<form method='POST' action=''>\n";
		echo "<input type='hidden' name='dgx_donate_settings_nonce' value='" . esc_attr( $nonce ) . "' />\n";

		$default_country = get_option( 'dgx_donate_default_country' );
		echo "<p>";
		echo dgx_donate_get_country_selector( 'dgx_donate_default_country', $default_country );
		echo "</p>";

		$default_state = get_option( 'dgx_donate_default_state' );
		echo "<p>";
		echo dgx_donate_get_state_selector( 'dgx_donate_default_state', $default_state );
		echo "</p>";

		$default_province = get_option( 'dgx_donate_default_province' );
		echo "<p>";
		echo dgx_donate_get_province_selector( 'dgx_donate_default_province', $default_province );
		echo "</p>";

		echo "<p><input id='submit' class='button' type='submit' value='" . esc_attr__( 'Update', 'dgx-donate' ) . "' name='submit' /></p>\n";
		echo "</form>";
		echo "</div>"; // dgx_donate_geography_selects
		echo "<br/>";

		// Show Tribute Section?
		echo "<h3>" . esc_html__( 'Tribute Gift Section', 'dgx-donate' ) . "</h3>";
		echo "<p>" . esc_html__( 'Show or hide the Tribute Gift section of the donation form.', 'dgx-donate' ) . "</p>";
		echo "<form method='POST' action=''>\n";
		echo "<input type='hidden' name='dgx_donate_settings_nonce' value='" . esc_attr( $nonce ) . "' />\n";

		$show_tribute_section = get_option( 'dgx_donate_show_tribute_section' );
		$true_checked = ( 'true' == $show_tribute_section ) ? "checked" : '';
		$false_checked = ( 'false' == $show_tribute_section) ? "checked" : '';

		echo "<p><input type='radio' name='show_tribute_section' value='true' $true_checked />" . esc_html__( 'Show the Tribute Gift form section', 'dgx-donate' ) . "</p>";
		echo "<p><input type='radio' name='show_tribute_section' value='false' $false_checked />" . esc_html__( 'Do not show the Tribute Gift form section', 'dgx-donate' ) . "</p>";

		echo "<p><input id='submit' class='button' type='submit' value='" . esc_attr__( 'Update', 'dgx-donate' ) . "' name='submit'/></p>\n";
		echo "</form>";
		echo "<br/>";

		// Show Employer Section?
		echo "<h3>" . esc_html__( 'Employer Match Section', 'dgx-donate' ) . "</h3>";
		echo "<p>" . esc_html__( 'Show or hide the Employer Match section of the donation form.', 'dgx-donate' ) . "</p>";
		echo "<form method='POST' action=''>\n";
		echo "<input type='hidden' name='dgx_donate_settings_nonce' value='" . esc_attr( $nonce ) . "' />\n";

		$show_employer_section = get_option( 'dgx_donate_show_employer_section' );
		$true_checked = ( 'true' == $show_employer_section ) ? "checked" : '';
		$false_checked = ( 'false' == $show_employer_section) ? "checked" : '';

		echo "<p><input type='radio' name='show_employer_section' value='true' $true_checked />" . esc_html__( 'Show the Employer Match form section', 'dgx-donate' ) . "</p>";
		echo "<p><input type='radio' name='show_employer_section' value='false' $false_checked />" . esc_html__( 'Do not show the Employer Match form section', 'dgx-donate' ) . "</p>";

		echo "<p><input id='submit' class='button' type='submit' value='" . esc_attr__( 'Update', 'dgx-donate' ) . "' name='submit'/></p>\n";
		echo "</form>";
		echo "<br/>";

		// Load Scripts Where?
		echo "<h3>" . esc_html__( 'Scripts', 'dgx-donate' ) . "</h3>";
		echo "<p>" . esc_html__( 'Whether to load scripts in the header or footer.', 'dgx-donate' ) . "</p>";
		echo "<form method='POST' action=''>\n";
		echo "<input type='hidden' name='dgx_donate_settings_nonce' value='" . esc_attr( $nonce ) . "' />\n";

		$scripts_in_footer = get_option( 'dgx_donate_scripts_in_footer' );
		$true_checked = ( 'true' == $scripts_in_footer ) ? "checked" : '';
		$false_checked = ( 'false' == $scripts_in_footer) ? "checked" : '';

		echo "<p><input type='radio' name='scripts_in_footer' value='false' $false_checked />" . esc_html__( 'Load scripts in the header (default)', 'dgx-donate' ) . "</p>";
		echo "<p><input type='radio' name='scripts_in_footer' value='true' $true_checked />" . esc_html__( 'Load scripts in the footer', 'dgx-donate' ) . "</p>";

		echo "<p><input id='submit' class='button' type='submit' value='" . esc_attr__( 'Update', 'dgx-donate' ) . "' name='submit'/></p>\n";
		echo "</form>";

		do_action( 'dgx_donate_settings_page_left' );

		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n"; 
	}

	function admin_enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
		$script_url = plugins_url( '../js/geo-selects.js', __FILE__ ); 
		wp_enqueue_script( 'dgx_donate_geo_selects_script', $script_url, array( 'jquery' ) );
	}
}

$dgx_donate_admin_settings_view = new Dgx_Donate_Admin_Settings_View();
