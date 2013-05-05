<?php
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

include "../../../wp-config.php";
include_once "./dgx-donate.php";

//////////////////////////////////////////////////////////////////////////////////
// Production or Test Sandbox?

$chatbackUrl = "ssl://www.paypal.com";
$hostHeader = "Host: www.paypal.com\r\n";

$payPalServer = get_option('dgx_donate_paypal_server');
if ($payPalServer == "SANDBOX")
{
	$chatbackUrl = "ssl://www.sandbox.paypal.com";
	$hostHeader = "Host: www.sandbox.paypal.com\r\n";
}

//////////////////////////////////////////////////////////////////////////////////
// Read the post from PayPal and add cmd

$req = 'cmd=_notify-validate';
if (function_exists('get_magic_quotes_gpc')) {
	$get_magic_quotes_exits = true;
}

foreach ($_POST as $key => $value) // Handle escape characters, which depends on setting of magic quotes
{
	$paypalData .= "$key : $value\n";
	if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1){
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}

dgx_donate_debug_log("IPN processing start");

//////////////////////////////////////////////////////////////////////////////////
// Post back to PayPal to validate

$header = "";
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= $hostHeader;
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

$sessionID = $_POST["custom"];

$fp = fsockopen ($chatbackUrl, 443, $errno, $errstr, 30);
if ($fp)
{
	fputs ($fp, $header . $req);
	while (!feof($fp))
    {
		$res = fgets ($fp, 1024);
		
		if (strcmp ($res, "VERIFIED") == 0)
	    {
			dgx_donate_debug_log("IPN VERIFIED for sessionID " . $sessionID);

			$paymentStatus = $_POST["payment_status"];
			dgx_donate_debug_log("Payment status = " . $paymentStatus . " for sessionID " . $sessionID);

			if ($paymentStatus == "Completed")
			{
				// Fetch the data from the transient
				$donationFormData = get_transient($sessionID);
		
				// If it is empty, log an error
				if (empty($donationFormData))
				{
					dgx_donate_debug_log("Unrecognized sessionID " . $sessionID);
				}
				else
				{
					dgx_donate_debug_log("Successfully found transient for sessionID " . $sessionID);

					// Create a donation record
					$donationID = dgx_donate_create_post($donationFormData);

					dgx_donate_debug_log("Created donation, donationID =  " . $donationID);

					// Send admin notification
					dgx_donate_send_donation_notification($donationID);

					// Send donor notification
					dgx_donate_send_thank_you_email($donationID);

					// Clear the transient
					delete_transient($sessionID);
				}
			}
		}
		else if (strcmp ($res, "INVALID") == 0)
		{
			dgx_donate_debug_log("IPN failed (INVALID) for sessionID " . $sessionID);
		}
	}
}
else
{
	dgx_donate_debug_log("IPN failed (unable to open chatbackurl)");
}
fclose ($fp);

//////////////////////////////////////////////////////////////////////////////////
// Must send something interesting back too

echo "content-type: text/plain\n\n";

?>
