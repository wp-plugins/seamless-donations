=== Plugin Name ===
Contributors: allendav, designgeneers
Donate link: http://www.designgeneers.com/
Tags: donation, donations, paypal, donate, non-profit, charity, gifts
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 2.4.2
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Receive donations, track donors and send customized thank you messages with Seamless Donations for WordPress.

== Description ==

Need more than just a PayPal donation button?  Would you like to allow your visitors to donate in honor of
someone?  Invite them to subscribe to your mailing list?  Allow them to mark their donation anonymous?  Track
donors and donations?  Seamless Donations by Designgeneers does all this and more - and all you need to do
is embed a simple shortcode and supply your PayPal Website Payments Standard email address to start receiving
donations today.

== Installation ==

1. Upload/install the Seamless Donations plugin
2. Activate the plugin
3. Set the email address for PayPal donations in the plugin settings
4. Create a new blank page (e.g. Donate Online)
5. Add the following shortcode to the page : [dgx-donate]
6. That's it - you're now receiving donations!

== Frequently Asked Questions ==

= Does this work with PayPal Website Payments Standard? =

Yes!

= Do I have to pay a monthly fee to PayPal to use this? =

No!  Website Payments Standard has no monthly cost.  They do keep 2-3% of the donation.

= Can I customize the thank you message emailed to donors? =

Yes!

= Can I have multiple emails addresses receive notification when a donation is made? =

Yes!

== Screenshots ==

1. The donation form your visitor sees
2. Optional tribute gift section expanded
3. Dashboard >> Seamless Donations Main Menu
4. Dashboard >> Donations
5. Dashboard >> Donors
6. Dashboard >> Thank You Email Templates

== Changelog ==

= 2.4.2 =
* Automatically trim whitespace from PayPal email address to avoid common validation error and improve usability.

= 2.4.1 =
* Changed mail function to use WordPress wp_mail instead of PHP mail - this should help avoid dropped emails

= 2.4.0 =
* Added the ability to export donation information to spreadsheet (CSV - comma separated values)

= 2.3.0 =
* Added a setting to allow you to turn the Tribute Gift section of the form off if you'd like

= 2.2.0 =
* Added the ability to delete a donation (e.g. if you create a test donation)

= 2.1.7 =
* Rolled back change in 2.1.6 for ajax display due to unanticipated problem with search

= 2.1.6 =
* Added ajax error display to aid in debugging certain users not being able to complete donations on their sites

= 2.1.5 =
* Changed plugin name to simply Seamless Donations

= 2.1.4 =
* Added logging, log menu item and log display to help troubleshoot IPN problems

= 2.1.3 =
* Changed PayPal cmd from _cart to _donations to avoid donations getting delayed

= 2.1.2 =
* Removed tax deductible from donation form, since not everyone using the plugin is a charity

= 2.1.1 =
* Added missing states - AK and AL - to donation form
* Added more checks for invalid donation amounts (minimum donation is set to 1.00)
* Added support for WordPress installations using old-style (not pretty) permalinks
* Fix bug that caused memorial gift checkbox to be ignored

= 2.1.0 =
* Added new suggested giving amounts
* Now allows you to choose which suggested giving amounts are displayed on the donation form
* Added ability to change the default state for the donation form

= 2.0.2 =
* Initial release to WordPress.Org repository
