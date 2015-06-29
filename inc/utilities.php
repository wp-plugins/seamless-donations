<?php

// quick array name-of function
// from http://php.net/manual/en/function.key.php
function seamless_donations_name_of ( array $a, $pos ) {

	$temp = array_slice ( $a, $pos, 1, true );

	return key ( $temp );
}

function seamless_donations_debug_alert( $a ) {
	echo "<script>";
	echo 'alert("' . $a . '");';
	echo "</script>";
}

function seamless_donations_debug_log( $a ) {
	echo "<script>";
	echo 'console.log("' . $a . '");';
	echo "</script>";
}

// This function builds both options and settings based on passed arrays
// The $options_array is an array that would be passed to the addSettingsField method
// If $settings_array is passed (not false), it will create a section and add the options to that section
function seamless_donations_process_add_settings_fields_with_options ( $options_array, $apf_object, $settings_array = array() ) {

	if( count ( $settings_array ) > 0 ) {
		$apf_object->addSettingSections ( $settings_array );
		$section_id = $settings_array['section_id'];
	}

	for( $i = 0; $i < count ( $options_array ); ++ $i ) {

		// read in stored options
		// by using this approach, we don't need to special-case for
		// fields and field types that don't save option data
		$option = $options_array[ $i ]['field_id'];

		$stored_option = get_option ( $option, false );
		if( $stored_option != false ) {
			$options_array[ $i ]['default'] = $stored_option;
		}

		// build up the settings field display
		if( count ( $settings_array ) > 0 ) {
			$apf_object->addSettingFields ( $section_id, $options_array[ $i ] );
		} else {
			$apf_object->addSettingFields ( $options_array[ $i ] );
		}
	}
}

// scans the admin UI sections, looks for a 'submit' type field named 'submit' that has a non-null value
// this is admittedly less efficient than just picking values out of the array, but it makes for
// considerably easier-to-read code for admin form processing. Given that admin submits are relatively
// rare and the array scan is short, it's a fair trade-off for more maintainable code
function seamless_donations_get_submitted_admin_section ($_the_array) {
	$slug = $_POST['page_slug'];
	for ($i=0 ; $i<count($_the_array); ++$i) {
		$key = seamless_donations_name_of($_the_array, $i);
		if(strpos($key, $slug) === 0) { // key begins with slug
			if(isset($_the_array[$key]['submit'])){
				if($_the_array[$key]['submit'] != NULL) {
					return($key);
				}
			}
		}
	}
	return false;
}

