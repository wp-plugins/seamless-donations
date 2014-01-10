<?php

/* Copyright 2013 Allen Snook (email: allendav@allendav.com) */

class Dgx_Donate_Admin_Completed_View {
	function __construct() {
		add_action( 'dgx_donate_menu', array( $this, 'menu_item' ), 11 );
	}
	
	function menu_item() {
		add_submenu_page(
			'dgx_donate_menu_page',
			__( 'Thank You Page', 'dgx-donate' ),
			__( 'Thank You Page', 'dgx-donate' ),
			'manage_options',
			'dgx_donate_thank_you_page',
			array( $this, 'menu_page' )
		);
	}
	
	function menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
	      wp_die( __( 'You do not have sufficient permissions to access this page.', 'dgx-donate' ) );
	    }
	
		// Get form arguments
		$thank_you_text = "";
		if ( isset( $_POST['thankstext'] ) ) {
			$thank_you_text = $_POST['thankstext'];
		}
	
		// If we have form arguments, we must validate the nonce
		if ( count( $_POST ) ) {
			$nonce = $_POST['dgx_donate_thanks_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'dgx_donate_thanks_nonce' ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'dgx-donate' ) );
			}
		}
	
	    // If they made changes, save them
		if ( ! empty( $thank_you_text ) ) {
			update_option( 'dgx_donate_thanks_text', $thank_you_text );
	    	$message = __( "Thank you page content updated.", 'dgx-donate' );
		}
	
		do_action( 'dgx_donate_thanks_page_load' );
	    
	    // Otherwise, proceed
	
		echo "<div class='wrap'>\n";
		echo "<div id='icon-edit-pages' class='icon32'></div>\n";
		echo "<h2>" . esc_html__( 'Email Templates', 'dgx-donate' ) . "</h2>\n";    
	    
		// Display any message
		if ( ! empty( $message ) ) {
			echo "<div id='message' class='updated below-h2'>\n";
			echo "<p>" . esc_html( $message ) . "</p>\n";
			echo "</div>\n";
		}

		$thank_you_text = get_option( 'dgx_donate_thanks_text' );
		$thank_you_text = stripslashes( $thank_you_text );
	
		$nonce = wp_create_nonce( 'dgx_donate_thanks_nonce' );
	
		echo "<div id='col-container'>\n";
		echo "<div id='col-right'>\n";
		echo "<div class='col-wrap'>\n";
		
		echo "<h3>" . esc_html__( 'Thank You Page', 'dgx-donate' ) . "</h3>\n";
		
		echo "<form method='POST' action=''>\n";	
		echo "<input type='hidden' name='dgx_donate_thanks_nonce' value='" . esc_attr( $nonce ) . "' />\n";	
		
		echo "<div class='form-field'>\n";
		echo "<label for='thankstext'>" . esc_html__( 'Thank You Page Text', 'dgx-donate' ) . "</label><br/>\n";
		echo "<textarea name='thankstext' rows='6' cols='40'>" . esc_textarea( $thank_you_text ) . "</textarea>\n";
		echo "<p class='description'>" . esc_html__( 'The text to display to the donor after they complete their donation.', 'dgx-donate' ) . "</p>\n";
		echo "</div>\n";
		
		echo "<p><input id='submit' class='button' type='submit' value='" . esc_attr( 'Save Changes', 'dgx=donate' ) ."' name='submit'></p>\n";
		
		echo "</form>";
	
		do_action('dgx_donate_thanks_page_right');
	
		do_action('dgx_donate_admin_footer');
		
		echo "</div>\n";
		echo "</div>\n";
		
		echo "<div id='col-left'>\n";
		echo "<div class='col-wrap'>\n";
	
		echo "<h3>" . esc_html__( 'Thank You Page', 'dgx-donate' ) . "</h3>\n";
		echo "<p>" . esc_html( 'On this page you can configure a special thank you message which will appear to your donors after they complete their donation.  This is separate from the thank you email that gets emailed to your donor.', 'dgx-donate' );
		echo "</p>\n";
	
		echo "</form>";
	
		do_action( 'dgx_donate_thanks_page_left' );
		
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		
		echo "</div>\n"; 
	}
}

$dgx_donate_admin_completed_view = new Dgx_Donate_Admin_Completed_View();
