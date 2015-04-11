<?php

/* Copyright 2013 Allen Snook (email: allendav@allendav.com) */
/* Copyright 2015 David Gewirtz, based on code by Allen Snook */

class Dgx_Donate_Admin_Main_View {
	function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_item' ), 9 );
	}

	function menu_item() {
		add_menu_page(
			__( 'Seamless Donations', 'dgx-donate' ),
			__( 'Seamless Donations', 'dgx-donate' ),
			'manage_options',
			'dgx_donate_menu_page',
			array( $this, 'menu_page' )
		);
		do_action( 'dgx_donate_menu' );
	}

	function menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'dgx-donate' ) );
		}

		$donor_id = isset( $_GET['donor'] ) ? $_GET['donor'] : '';
		$donation_id = isset( $_GET['donation'] ) ? $_GET['donation'] : '';

		if ( ! empty( $donor_id ) ) {
			Dgx_Donate_Admin_Donor_Detail_View::show( $donor_id );
		} else if ( ! empty( $donation_id ) ) {
			Dgx_Donate_Admin_Donation_Detail_View::show( $donation_id );
		} else {
			self::show();
		}
	}

	static function show() {
		echo "<div class='wrap'>\n";
		echo "<div id='icon-edit-pages' class='icon32'></div>\n";
		echo "<h2>" . esc_html__( 'Seamless Donations for WordPress', 'dgx-donate' ) . "</h2>\n";

		// Quick Links
		$quick_links = array(
			array(
				'title' => __( 'Donations', 'dgx-donate' ),
				'url' => get_admin_url() . "admin.php?page=dgx_donate_donation_report_page"
			),
			array(
				'title' => __( 'Donors', 'dgx-donate' ),
				'url' => get_admin_url() . "admin.php?page=dgx_donate_donor_report_page"
			),
			array(
				'title' => __( 'Funds', 'dgx-donate' ),
				'url' => get_admin_url() . "admin.php?page=dgx_donate_funds_page"
			),
			array(
				'title' => __( 'Thank You Emails', 'dgx-donate' ),
				'url' => get_admin_url() . "admin.php?page=dgx_donate_template_page"
			),
			array(
				'title' => __( 'Thank You Page', 'dgx-donate' ),
				'url' => get_admin_url() . "admin.php?page=dgx_donate_thank_you_page"
			),
			array(
				'title' => __( 'Form Options', 'dgx-donate' ),
				'url' => get_admin_url() . "admin.php?page=dgx_donate_form_options_page"
			),
			array(
				'title' => __( 'Settings', 'dgx-donate' ),
				'url' => get_admin_url() . "admin.php?page=dgx_donate_settings_page"
			),
			array(
				'title' => __( 'Log', 'dgx-donate' ),
				'url' => get_admin_url() . "admin.php?page=dgx_donate_debug_log_page"
			),
			array(
				'title' => __( 'Help/FAQ', 'dgx-donate' ),
				'url' => get_admin_url() . "admin.php?page=dgx_donate_help_page"
			),
		);

		echo "<p><strong>" . __( 'Quick Links', 'dgx-donate' ) . ": ";

		$count = 0;
		foreach( (array) $quick_links as $quick_link ) {
			echo "<a href='" . esc_url( $quick_link['url'] ) . "'><strong>" . esc_html( $quick_link['title'] ) . "</strong></a>";
			$count++;
			if ( $count != count( $quick_links ) ) {
				echo " | ";
			}
		}
		echo "</p>";

		// Seamless Donations 4.0 warning code

		if(isset($_GET['update_notify'])) {
			if ($_GET['update_notify'] == "dismiss") {
				update_option("dgx_donate_initial_40_update_warning", "40A1UPDATEWARNED"); // initial warning showed
			}
		}
		echo '<div style="padding:5px; background-color:red; color:white; line-height:2em">';
		echo '<H1>**** Alert ****</h1><h1>Seamless Donations is getting a major upgrade soon (May/June 2015).</H1>';
		echo '<h2 style="color:white">The upgrade, for version 4.0, will provide substantial new capabilities ';
		echo 'for the plugin, but the architectural ';
		echo 'changes <b>may impact customizations you have made to the plugin</b>. ';
		echo 'Please visit ';
		echo '<A HREF="http://zatzlabs.com/lab-notes/" style="color:white">David\'s Lab Notes</A> ';
		echo 'for ongoing development news or subscribe below.</h2>';

		?>
<!-- Begin MailChimp Signup Form -->
<link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
<style type="text/css">
	#mc_embed_signup{background-color: red; color: white; clear:left; font:14px Helvetica,Arial,sans-serif; }
	/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
</style>
<div id="mc_embed_signup">
	<form action="//zatzlabs.us10.list-manage.com/subscribe/post?u=81b10c30eeed8b4ec79c86d53&amp;id=f56ca4c04e" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
		<div id="mc_embed_signup_scroll">
			<h2 style="color:white">Subscribe to David's Lab Notes now</h2>
			<div class="mc-field-group">
				<label for="mce-EMAIL">Email Address </label>
				<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
			</div>
			<div id="mce-responses" class="clear">
				<div class="response" id="mce-error-response" style="display:none"></div>
				<div class="response" id="mce-success-response" style="display:none"></div>
			</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
			<div style="position: absolute; left: -5000px;"><input type="text" name="b_81b10c30eeed8b4ec79c86d53_f56ca4c04e" tabindex="-1" value=""></div>
			<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
		</div>
	</form>
</div>
<script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
<!--End mc_embed_signup-->
<?php
		echo "</div>";

		// Recent Donations
		echo "<div id='col-container'>\n";
		echo "<div id='col-right'>\n";
		echo "<div class='col-wrap'>\n";

		echo "<h3>" . esc_html__( 'Recent Donations', 'dgx-donate' ) . "</h3>\n";

		$args = array(
			'numberposts'     => '10',
			'post_type'       => 'dgx-donation'
		);

		$my_donations = get_posts( $args );

		if ( count( $my_donations ) ) {
			echo "<table class='widefat'><tbody>\n";
			echo "<tr>";
			echo "<th>" . esc_html__( 'Date', 'dgx-donate' ) . "</th>";
			echo "<th>" . esc_html__( 'Donor', 'dgx-donate' ) . "</th>";
			echo "<th>" . esc_html__( 'Amount', 'dgx-donate' ) . "</th>";
			echo "</tr>\n";

			foreach ( (array) $my_donations as $my_donation ) {
				$donation_id = $my_donation->ID;

				$year = get_post_meta( $donation_id, '_dgx_donate_year', true );
				$month = get_post_meta( $donation_id, '_dgx_donate_month', true );
				$day = get_post_meta( $donation_id, '_dgx_donate_day', true );
				$time = get_post_meta( $donation_id, '_dgx_donate_time', true );
				$donation_date = $month . "/" . $day . "/" . $year;

				$first_name = get_post_meta( $donation_id, '_dgx_donate_donor_first_name', true );
				$last_name = get_post_meta( $donation_id, '_dgx_donate_donor_last_name', true );
				$donor_email = get_post_meta( $donation_id, '_dgx_donate_donor_email', true );
				$donor_detail = dgx_donate_get_donor_detail_link( $donor_email );

				$amount = get_post_meta( $donation_id, '_dgx_donate_amount', true );
				$currency_code = dgx_donate_get_donation_currency_code( $donation_id );
				$formatted_amount = dgx_donate_get_escaped_formatted_amount( $amount, 2, $currency_code );

				$donation_detail = dgx_donate_get_donation_detail_link( $donation_id );
				echo "<tr>";
				echo "<td>";
				echo "<a href='" . esc_url( $donation_detail ) . "'>";
				echo esc_html( $donation_date . ' ' . $time) . "</a>";
				echo "</td>";
				echo "<td>";
				echo "<a href='" . esc_url( $donor_detail ) . "'>";
				echo esc_html( $first_name . ' ' . $last_name ) . "</a>";
				echo "</td>";
				echo "<td>" . $formatted_amount . "</td>";
				echo "</tr>\n";
		}

			echo "</tbody></table>\n";
		} else {
			echo "<p>" . esc_html__( 'No donations found.', 'dgx-donate' ) . "</p>\n";
		}

		do_action( 'dgx_donate_main_page_right' );
		do_action( 'dgx_donate_admin_footer' );

		echo "</div> <!-- col-wrap -->\n";
		echo "</div> <!-- col-right -->\n";


		echo "<div id='col-left'>\n";
		echo "<div class='col-wrap'>\n";

		echo "<h3>" . esc_html__( "Latest News", 'dgx-donate' ) . "</h3>";


		// regular news feed
		echo "<div class='rss-widget'>";
		wp_widget_rss_output( array(
			'url' => 'http://zatzlabs.com/feed/',
			'title' => __( "What's up with Seamless Donations", 'dgx-donate' ),
			'items' => 3,
			'show_summary' => 1,
			'show_author' => 0,
			'show_date' => 1
		) );
		echo "</div>";

		do_action( 'dgx_donate_main_page_left' );

		echo "</div> <!-- col-wrap -->\n";
		echo "</div> <!-- col-left -->\n";
		echo "</div> <!-- col-container -->\n";

		echo "</div> <!-- wrap -->\n";
	}
}

$dgx_donate_admin_main_view = new Dgx_Donate_Admin_Main_View();
