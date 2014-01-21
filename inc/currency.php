<?php

function dgx_donate_get_currencies() {
	$currencies = array(
		'AUD' => array( 'name' => 'Australian Dollar', 'symbol' => '$' ),
		'CAD' => array( 'name' => 'Canadian Dollar', 'symbol' => '$' ),
		'EUR' => array( 'name' => 'Euro', 'symbol' => '&euro;' ),
		'GBP' => array( 'name' => 'Pound Sterling', 'symbol' => '&pound;' ),
		'JPY' => array( 'name' => 'Japanese Yen', 'symbol' => '&yen;' ),
		'USD' => array( 'name' => 'U.S. Dollar', 'symbol' => '$' )
	);

	return $currencies;
}

/*
 * From https://developer.paypal.com/docs/classic/api/currency_codes/
 */
function dgx_donate_get_currency_selector( $select_name, $select_initial_value)
{
	$output = "<select id='" . esc_attr( $select_name ) . "' name='" . esc_attr( $select_name ) . "'>";
	
	$currencies = dgx_donate_get_currencies();

	foreach ( $currencies as $currency_code => $currency_details ) {
		$selected = "";
		if ( strcasecmp( $select_initial_value, $currency_code ) == 0 ) {
			$selected = " selected ";
		}
		$output .= "<option value='" . esc_attr( $currency_code ) . "'" . esc_attr( $selected ) . ">" . esc_html( $currency_details['name'] ) ."</option>";
	}
	
	$output .= "</select>";

	return $output;
}

function dgx_donate_get_escaped_formatted_amount( $amount, $decimal_places = 2, $currency_code = '' ) {
	if ( empty( $currency_code ) ) {
		$currency_code = get_option( 'dgx_donate_currency' );
	}

	$currencies = dgx_donate_get_currencies();
	$currency = $currencies[$currency_code];
	$currency_symbol = $currency['symbol'];

	return $currency_symbol . esc_html( number_format( $amount, $decimal_places ) );
}

function dgx_donate_get_plain_formatted_amount( $amount, $decimal_places = 2, $currency_code = '', $append_currency_code = false ) {
	if ( empty( $currency_code ) ) {
		$currency_code = get_option( 'dgx_donate_currency' );
	}

	$formatted_amount = number_format( $amount, $decimal_places );
	if ( $append_currency_code ) {
		$formatted_amount .= " (" . $currency_code . ")";
	}

	return $formatted_amount;
}

function dgx_donate_get_donation_currency_code( $donation_id ) {
	/* gets the currency code for the donation */
	/* updates donations without one (pre version 2.8.1) as USD */
	$currency_code = get_post_meta( $donation_id, '_dgx_donate_donation_currency', true );
	if ( empty( $currency_code ) ) {
		$currency_code = "USD";
		update_post_meta( $donation_id, '_dgx_donate_donation_currency', $currency_code );
	}
	return $currency_code;
}