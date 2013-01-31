// Copyright 2012 Designgeneers! Web Design (email: info@designgeneers.com)
// 

function DgxDonateTrim(s) {

	if (s == undefined)
	{
		s = "";
	}
	
	s = s.replace(/(^\s*)|(\s*$)/gi,"");
	s = s.replace(/[ ]{2,}/gi," ");
	s = s.replace(/\n /,"\n");
	return s;
}

function DgxDonateLooksLikeMail(str) {
    var lastAtPos = str.lastIndexOf('@');
    var lastDotPos = str.lastIndexOf('.');
    return (lastAtPos < lastDotPos && lastAtPos > 0 && str.indexOf('@@') == -1 && lastDotPos > 2 && (str.length - lastDotPos) > 2);
}

function DgxDonateCountNeedles(needle, haystack)
{
	var count = 0;
    var index = -1;
    index = haystack.indexOf(needle, index + 1);
    while (index != -1) {
        count++;
        index = haystack.indexOf(needle, index + 1);
    }

    return count;
}

function DgxDonateIsValidAmount(amount)
{
	// Empty amounts are not allowed
	if (amount == "")
	{
		return false;
	}

	// Check for anything other than numbers and decimal points	
	var matchTest = amount.match(/[^0123456789.]/g);
	if (matchTest != null)
	{
		alert('Please use only numbers when specifying your donation amount.');
		return false;
	}

	// Count the number of decimal points
	var pointCount = DgxDonateCountNeedles(".", amount);

	// If more than one decimal point, fail right away
	if (pointCount > 1)
	{
		return false;
	}

	// A leading zero is not allowed
	if (amount.substr(0,1) == "0")
	{
		return false;
	}

	// A leading decimal point is not allowed (minimum donation is 1.00)
	if (amount.substr(0,1) == ".")
	{
		return false;
	}

	// If we have a decimal point and there is anything other than two digits after it, fail
	if (pointCount == 1)
	{
		var pointIndex = amount.indexOf(".");
		if (pointIndex + 2 != (amount.length - 1))
		{
			return false;
		}
	}

	return true;
}

function DgxDonateDoCheckout()
{
	// First we do a client side validation
	// We should also do a server side validation in the ajax handler
	
	// Reset the error message field
	jQuery('.dgx-donate-error-msg').html("");
	jQuery('.dgx-donate-error-msg').css('visibility', 'hidden');
	
	// Check for missing or invalid data
	// Flag the missing places with background soft red
	// Send back a false if anything amiss
	var formValidates = true;

	// Reset any input alert colors
	jQuery('#dgx-donate-form').find("input").removeClass('dgx-donate-invalid-input');
	
	// Get the form data
	var values = {};
	jQuery.each(jQuery('#dgx-donate-form').serializeArray(), function(i, field) {
			values[field.name] = field.value;
			}); 

	var sessionID = values['_dgx_donate_session_id'];
	var donationAmount = DgxDonateTrim(values['_dgx_donate_amount']);
	var userAmount = DgxDonateTrim(values['_dgx_donate_user_amount']);
	var repeating = DgxDonateTrim(values['_dgx_donate_repeating']);
	var designated = DgxDonateTrim(values['_dgx_donate_designated']);
	var designatedFund = DgxDonateTrim(values['_dgx_donate_designated_fund']);
	var increaseToCover = DgxDonateTrim(values['_dgx_donate_increase_to_cover']);
	var anonymous = DgxDonateTrim(values['_dgx_donate_anonymous']);
	var tributeGift = DgxDonateTrim(values['_dgx_donate_tribute_gift']);
	var memorialGift = DgxDonateTrim(values['_dgx_donate_memorial_gift']);
	var honoreeName = DgxDonateTrim(values['_dgx_donate_honoree_name']);
	var honorByEmail = DgxDonateTrim(values['_dgx_donate_honor_by_email']);
	var honoreeEmailName = DgxDonateTrim(values['_dgx_donate_honoree_email_name']);
	var honoreeEmail = DgxDonateTrim(values['_dgx_donate_honoree_email']);
	var honoreePostName = DgxDonateTrim(values['_dgx_donate_honoree_post_name']);
	var honoreeAddress = DgxDonateTrim(values['_dgx_donate_honoree_address']);
	var honoreeCity = DgxDonateTrim(values['_dgx_donate_honoree_city']);
	var honoreeState = DgxDonateTrim(values['_dgx_donate_honoree_state']);
	var honoreeCity = DgxDonateTrim(values['_dgx_donate_honoree_city']);
	var honoreeZip = DgxDonateTrim(values['_dgx_donate_honoree_zip']);
	var firstName = DgxDonateTrim(values['_dgx_donate_donor_first_name']);
	var lastName = DgxDonateTrim(values['_dgx_donate_donor_last_name']);
	var phone = DgxDonateTrim(values['_dgx_donate_donor_phone']);
	var email = DgxDonateTrim(values['_dgx_donate_donor_email']);
	var addToMailingList = DgxDonateTrim(values['_dgx_donate_add_to_mailing_list']);
	var address = DgxDonateTrim(values['_dgx_donate_donor_address']);
	var address2 = DgxDonateTrim(values['_dgx_donate_donor_address2']);
	var city = DgxDonateTrim(values['_dgx_donate_donor_city']);
	var state = DgxDonateTrim(values['_dgx_donate_donor_state']);
	var zip = DgxDonateTrim(values['_dgx_donate_donor_zip']);
	var increaseToCover = DgxDonateTrim(values['_dgx_donate_increase_to_cover']);
	var paymentMethod = DgxDonateTrim(values['_dgx_donate_payment_method']);
	var cardNumber = DgxDonateTrim(values['_dgx_donate_card_number']);
	var cardCCV = DgxDonateTrim(values['_dgx_donate_card_ccv']);
	var cardExpMonth = DgxDonateTrim(values['_dgx_donate_card_exp_month']);
	var cardExpYear = DgxDonateTrim(values['_dgx_donate_card_exp_year']);
	var nameOnCard = DgxDonateTrim(values['_dgx_donate_card_name_on_card']);
	var referringUrl = location.href;

	var amount = "";
	
	if (donationAmount == "OTHER")
	{
		amount = userAmount;
	}
	else
	{
		amount = donationAmount;
	}

	if (!DgxDonateIsValidAmount(amount))
	{
		formValidates = false;
		DgxDonateMarkInvalid("_dgx_donate_user_amount");	
	}

	if (tributeGift == 'on')
	{
		if (honoreeName == "")
		{
			formValidates = false;
			DgxDonateMarkInvalid("_dgx_donate_honoree_name");
		}
		if (honorByEmail == 'TRUE')
		{
			if (honoreeEmailName == "")
			{
				formValidates = false;
				DgxDonateMarkInvalid("_dgx_donate_honoree_email_name");
			}
			if (honoreeEmail == "")
			{
				formValidates = false;
				DgxDonateMarkInvalid("_dgx_donate_honoree_email");
			}
		}
		else /* honor by postal mail */
		{
			if (honoreePostName == "")
			{
				formValidates = false;
				DgxDonateMarkInvalid("_dgx_donate_honoree_post_name");
			}
			if (honoreeAddress == "")
			{
				formValidates = false;
				DgxDonateMarkInvalid("_dgx_donate_honoree_address");
			}
			if (honoreeCity == "")
			{
				formValidates = false;
				DgxDonateMarkInvalid("_dgx_donate_honoree_city");
			}
			if (honoreeZip == "")
			{
				formValidates = false;
				DgxDonateMarkInvalid("_dgx_donate_honoree_zip");
			}		
		}
	}
	
	if (firstName == "")
	{
		formValidates = false;
		DgxDonateMarkInvalid("_dgx_donate_donor_first_name");
	}
	
	if (lastName == "")
	{
		formValidates = false;
		DgxDonateMarkInvalid("_dgx_donate_donor_last_name");
	}
	
	if (phone == "")
	{
		formValidates = false;
		DgxDonateMarkInvalid("_dgx_donate_donor_phone");
	}	

	if (email == "")
	{
		formValidates = false;
		DgxDonateMarkInvalid("_dgx_donate_donor_email");
	}
	
	if (address == "")
	{
		formValidates = false;
		DgxDonateMarkInvalid("_dgx_donate_donor_address");
	}
	
	if (city == "")
	{
		formValidates = false;
		DgxDonateMarkInvalid("_dgx_donate_donor_city");
	}	

	if (zip == "")
	{
		formValidates = false;
		DgxDonateMarkInvalid("_dgx_donate_donor_zip");
	}
	
	if (!formValidates)
	{
		alert('Some required information is missing or invalid.  Please complete the fields highlighted in red');
		return false;
	}
	
	// If validation succeeds, post the data to ajax to create a transient
	// and update the hidden form with the visible form values that PayPal cares about

	jQuery('#dgx-donate-hidden-form').find('input[name="first_name"]').val(firstName);
	jQuery('#dgx-donate-hidden-form').find('input[name="last_name"]').val(lastName);
	jQuery('#dgx-donate-hidden-form').find('input[name="address1"]').val(address);
	jQuery('#dgx-donate-hidden-form').find('input[name="address2"]').val(address2);
	jQuery('#dgx-donate-hidden-form').find('input[name="city"]').val(city);
	jQuery('#dgx-donate-hidden-form').find('input[name="state"]').val(state);
	jQuery('#dgx-donate-hidden-form').find('input[name="zip"]').val(zip);
	// jQuery('#dgx-donate-hidden-form').find('input[name="country"]').val("xxx");
	jQuery('#dgx-donate-hidden-form').find('input[name="email"]').val(email);
	jQuery('#dgx-donate-hidden-form').find('input[name="custom"]').val(sessionID);
	jQuery('#dgx-donate-hidden-form').find('input[name="amount"]').val(amount);

	// Disable the pay button
	var payButton = jQuery('#dgx-donate-form').find("input[type='submit']");
	payButton.attr("disabled", "disabled"); // To disable
	payButton.removeClass("dgx-donate-pay-enabled");
	payButton.addClass("dgx-donate-pay-disabled");

	// Turn on the busy graphic
	jQuery('.dgx-donate-busy').css('visibility', 'visible');
	
	// Send the request
	
	var nonce = dgxDonateAjax.nonce;

	var data = { action: 'dgx_donate_paypalstd_ajax_checkout', referringUrl: referringUrl, nonce: nonce, sessionID: sessionID,
		donationAmount: donationAmount, userAmount: userAmount, repeating: repeating, designated: designated,
		designatedFund: designatedFund, increaseToCover: increaseToCover, anonymous: anonymous,
		tributeGift: tributeGift, honoreeName: honoreeName, honorByEmail: honorByEmail, honoreeEmail: honoreeEmail,
		memorialGift: memorialGift, honoreeEmailName: honoreeEmailName, honoreePostName: honoreePostName,
		honoreeAddress: honoreeAddress, honoreeCity: honoreeCity, honoreeState: honoreeState, honoreeZip: honoreeZip,
		firstName: firstName, lastName: lastName, phone: phone, email: email, addToMailingList: addToMailingList,
		address: address, address2: address2, city: city, state: state, zip: zip, increaseToCover: increaseToCover,
		paymentMethod: paymentMethod };

	jQuery.post( dgxDonateAjax.ajaxurl, data, DgxDonateCallback );

	return false;
}

function DgxDonateCallback(data)
{	
	// Turn off the processing graphic
	jQuery('.dgx-donate-busy').css('visibility', 'hidden');

	// Submit the hidden form to take the user to PayPal
	jQuery('#dgx-donate-hidden-form').submit();

}

function DgxDonateMarkInvalid(fieldname)
{
	var selector = "input[name=" + fieldname + "]";
	jQuery('#dgx-donate-form').find(selector).addClass('dgx-donate-invalid-input');
}

function DgxDonateAjaxError( event, jqxhr, settings, exception ) {
	// Turn off the processing graphic
	jQuery('.dgx-donate-busy').css('visibility', 'hidden');

	// Display the error
	alert ( "An Ajax error occurred while requesting the resource - " + settings.url + " - No donation was completed.  Please try again later.");

	return false;
}

jQuery(document).ready(function() {	
	
	// Make sure the payment button is rendered correctly and the payment
	// progress ajax graphic is not visible on start
	// Re-enable the submit button
	
	var payButton = jQuery('#dgx-donate-form').find("input[type='submit']");
	payButton.removeAttr("disabled");
	payButton.removeClass("dgx-donate-pay-disabled");
	payButton.addClass("dgx-donate-pay-enabled");
	
	// Turn off the processing graphic
	jQuery('.dgx-donate-busy').css('visibility', 'hidden');

	// Register our AJAX error handler
	// jQuery(document).ajaxError( DgxDonateAjaxError );
});

