=== Contact Form 7 - PayPal & Stripe Add-on ===
Contributors: scottpaterson,wp-plugin
Donate link: https://wpplugin.org/donate/
Tags: paypal, stripe, payments, credit card, ecommerce
Author URI: https://wpplugin.org
Requires at least: 3.0
Tested up to: 6.7
Requires PHP: 5.5
Stable tag: 2.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add PayPal and Stripe to Contact Form 7. Accept credit card payments with Stripe & PayPal on your site today. Offical PayPal & Stripe Partner.

== Description ==

###  This plugin seamlessly integrates PayPal & Stripe with Contact Form 7

Do you use Contact Form 7 and want to accept PayPal & Stripe payments easily? This plugin can do just that. It's easy to setup and use. On top of that we're an official PayPal and Stripe partner.

###  Do you have questions or problems?
We're here to help! Please create a support post in the [support forum](https://wordpress.org/support/plugin/contact-form-7-paypal-add-on/) and we'll get back to you right away!

###  Watch this short video to see how the plugin works:

[youtube https://www.youtube.com/watch?v=GZ_lYEAJgsQ]

Each contact form can have its own PayPal & Stripe settings. When a contact form is enabled to use PayPal or Stripe, and the user submits the form it will send the email as usual, then auto redirect. 

Note: This plugin works with both the old and new Contact Form 7 interface. A PayPal account, which is free, is required to use PayPal. A Stripe account, which is also free, is required to use Stripe. You can use the plugin with only PayPal enabled, only Stripe or PayPal and Stripe at the same time.

###   Plugin Features

*	View payments history for PayPal & Stripe
*	Set items name, price, SKU/ID per contact form
*   Built in support for 18 languages (PayPal currently supports 18 languages)
*   Built in support 25 currencies (PayPal currently supports 25 currencies)
*	PayPal testing through SandBox
*	Choose a cancel payment URL
*	Choose a successful payment URL
*  Supports PayPal Express

###   Pro Version
We offer a Pro version of this PayPal plugin for business owners who need more features.

>**[Learn more about the Pro version](https://wpplugin.org/downloads/contact-form-7-paypal-add-on/)**

Here is a short video of how the Pro version works:
[youtube https://www.youtube.com/watch?v=aS9bxBDgpqY]

* Only send Contact Form 7 email if PayPal or Stripe payment is successful.
* No 2% per transaction application fee (only pay normal PayPal & Stripe fees).
* Link a form item to quantity - A form item can be a textbox, dropdown, radio button, etc. anything that is a number.<br />
* Link a form item to price - The price field can be linked to any form item. Pipes are supported to allow for dropdown text options for each price.<br />
* Link up to 5 different price elements per form <br />
* Link form items to option text fields - The text field can be linked to any form item.<br />
* Charge Tax & Shipping<br />
* Skip redirecting based upon form elements<br />
* Amazing plugin support agents from California and Colorado<br />
* Choose a success / cancel  payment URL per contact form<br />

>**[Upgrade to the Pro Version Today!](https://wpplugin.org/downloads/contact-form-7-paypal-add-on/)**

###   Our other Contact Form 7 plugins
> [Contact Form 7 Redirect & Thank You Page](https://wordpress.org/plugins/cf7-redirect-thank-you-page)

###   About Us
WP Plugin is based in Boulder, Colorado. You can visit the WP Plugin website at [wpplugin.org](https://wpplugin.org). Various trademarks held by their respective owners.

== Installation ==

= Automatic Installation =
> 1. Sign in to your WordPress site as an administrator.
> 2. In the main menu go to Plugins -> Add New.
> 3. Search for Contact Form 7 - PayPal Add-on and click install.
> 4. That's it. You are now ready to start accepting PayPal payment on your website through your contact form.

== Frequently Asked Questions ==

= Where can I find complete documentation? =
Full searchable docs for Contact Form 7 - PayPal & Stripe Add-on can be found here: [https://wpplugin.org/documentation/](https://wpplugin.org/documentation/)

= How do I see a list of my sales? =
when you install the app, in the "Contact" menu, you will see an option for "PayPal & Stripe Payments".

= Can I let the user choose how they want to pay, either using PayPal or Stripe? =
Yes, you can let the user choose how they want to pay. Documentation for this feature can be found here: [https://wpplugin.org/documentation/paypal-stripe-gateway-code/](https://wpplugin.org/documentation/paypal-stripe-gateway-code/)

= Do I have to use PayPal & Stripe at the same time? =
No, you don't have to. You can use one or both payment gateways at the same time.

= Can I only send the email after a successful PayPal or Stripe payment? =
Yes, this is a feature of our Pro plugin [https://wpplugin.org/downloads/contact-form-7-paypal-add-on/](https://wpplugin.org/downloads/contact-form-7-paypal-add-on/)


== Screenshots ==

== Screenshots ==
1. Options while editing a contact form - Note: This plugin works with both the old and new Contact Form 7 interface, screenshots from old version.
2. PayPal settings page
3. Stripe settings page
4. PayPal & Stripe payments history


== Changelog ==

= 2.3.2 =
* 11/5/24
* Fix - Small security issue fixed reported by Wordfence.

= 2.3.1 =
* 9/27/24
* Fix - Small security issue fixed.

= 2.3 =
* 8/22/24
* New - Added the "Request Method" feature. This allow you to control if the form uses Admin Ajax or the REST API for redirection. This settings can be found on the Settings page in Other tab.

= 2.2.1 =
* 6/17/24
* New - Added support for Ron - Romanian Currency in Stripe (It's not supported by PayPal)

= 2.2 =
* 2/25/24
* Fix - Security issue fixed.

= 2.1 =
* 12/6/23
* Update - Updated Stripe Library - Old library was not compatable with PHP 8.0+.
* Update - Updated PayPal & Stripe update noficiation bars.
* Fix - Security issue

= 2.0 =
* 11/3/23
* New - Added PayPal Commerce Platform Integration


= 1.9.4 =
* 3/20/23
* Fix - Security issue
* Fix - PHP error message resulting from Stripe mode variable


= 1.9.3 =
* 5/7/21
* Fix - Undefined index issue - https://wordpress.org/support/topic/undefined-index-cf7pp_stripe_email/
* Fix - Removed Settings Page Extension tab. It was causing errors for a few users and slowed down loading the settings page.

= 1.9.2 =
* 4/25/21
* Fix - Fixed PHP error caused by a few webhosting companies disabling PHP allow_url_fopen. This caused Stripe Connect to have multiple errors.

= 1.9.1 =
* 4/7/21
* Fix - Fixed PHP error caused by 1.9 release. This was related to using a version of PHP > 7.4.

= 1.9 =
* 4/6/21
* New - Added Stripe Connect

= 1.8.4 =
* 2/19/21
* Fix - Fixed issue with redirection and email sending if URL has a query string in it.

= 1.8.3 =
* 2/19/21
* Fix - Fixed issue casued by anchor tag in URL causing Stripe redirect to fail.

= 1.8.2 =
* 2/3/21
* Fix - Fixed issue caused by Yoast making form to redirect to homepage.
* Fix - Fixed issue with JS files not including version number causing them to be cached.

= 1.8.1 =
* 1/28/21
* New - PayPal & Stripe admin payment history
* New - Stripe will automatically register and check webhook for live and sandbox payments
* New - Local environment helper admin notice
* New - Added admin review notice
* Update - Updated PayPal IPN code

= 1.7 =
* 12/7/20
* New - Stripe now redirects to hosted Stripe checkout page.
* New - Stripe is now fully SCA complient.
* New - Added many more helpful error notices so that site owners can more quickly solve problems.

= 1.6.9 =
* 10/14/20
* Fix - Fixed bug related to PHP setcookie.

= 1.6.8 =
* 8/27/20
* Fix - Fixed bug related to Japanese JPY currency format.

= 1.6.7 =
* 8/10/20
* New - Added ability to change between cookie use and session use. Some servers support one or the other.
* Fix - Changed the way cookies work.

= 1.6.6 =
* 8/8/20
* New - Added new redirect method. Can be used for some sites that have trouble redirecting to PayPal or Stripe.
* Fix - Fixed settings page slow to load issue due to transient name problem.

= 1.6.5 =
* 7/9/20
* New - Removed PHP Session support, now the pluign uses PHP Cookies.
* Fix - The plugin no longer causes an issue with WordPress Site Health Performance.

= 1.6.4 =
* 7/4/20
* Fix - Contact Form 7 5.2 broke redirecting to PayPal or Stripe.

= 1.6.3 =
* 2/4/20
* Fix - CSS style issue on settings page, extensions tab.
* Fix - Changed getting started text.
* Tested - Tested up to 5.3.x

= 1.6.2 =
* 4/26/19
* Fix - Changed redirect URL from using WordPress's site URL to home URL. This fixes a problem on sites with a different WordPress Address and Site Address.

= 1.6.1 =
* 8/20/18
* Change - Changed the hidden HTML form names on the tabs settings page to fix a conflict with the plugin Frontend Registration - Contact Form 7.

= 1.6 =
* 7/1/18
* New - Added ability to link form email field to Stripe.
* New - Added ability to redirect to success page after Stripe payment.
* Fix - Undefined index error related to settings redirect variable.
* Fix - Undefined JS ajax object error with failed credit card.

= 1.5.7 =
* 5/28/18
* Fix - PayPal rediect encoding problem.

= 1.5.6 =
* 5/21/18
* Fix - Added HTTPS notification on settings page
* New - Added Extensions tab on settings page

= 1.5.5 =
* 3/13/18
* Fix - Only load files from Stripe if needed.

= 1.5.4 =
* 2/19/18
* Fix - Stripe checkout was giving an error message if the Stripe test keys were not entered.

= 1.5.3 =
* 2/9/18
* Fix - Was not redirecting to Stripe, if only Stripe was enabled.
* New - Added Test Mode indicator on Stripe mode form, if Stripe is being used in Sandbox mode.

= 1.5.2 =
* 2/7/18
* Fix - Plugin had a conflict with the Divi theme's full page width.

= 1.5.1 =
* 2/6/18
* Fix - Not all forms where redirecting on some sites.

= 1.5 =
* 2/6/18
* Major Release - Added Stripe to the plugin
* Change - The majority of the plugin has been completely rewritten
* Fix - The plugin now works with Contact Form 7 version 5

= 1.4.3 =
* 10/23/17
* Fix - Plugin should not work with many more Contact Form 7 extensions, such as Mailchimp, Google Sheets, Datepicker, etc.
* Fix - Currency will now pass through a filter, this is useful as PayPal does not accept $ anymore in front of amounts.

= 1.4.2 =
* 9/15/17
* Bug - Form occasionally would redirect to site homepage even with the form not having PayPal enabled.
* Bug - Spelling mistake.

= 1.4.1 =
* 9/1/17
* Bug - Fixed default redirect method if variable has not been previously set

= 1.4 =
* 8/31/17
* New - Added new redirect method
* New - Added option to change the redirect method on the settings page
* Update - Removed the need for the plugin to write to wp-config
* Update - Updated list of available Pro version features
* Update - Changed how the plugin sends POST data to PayPal
* Update - Cleaned up the code
* Update - Updated the Settings Page usage instructions

= 1.3.5 =
* 6/15/17
* Update - Tested up to WordPress version 4.8
* Fix - Fixed code formatting issues
* Fix - Fixed language text domain issues

= 1.3.4 =
* 3/8/16
* Update - Updated tested up to tag.
* Update - Updated pro url links.

= 1.3.3 =
* 1/21/16
* Bug fix - Settings page not saving on some server configurations.

= 1.3.2 =
* 11/13/15
* Added feature - Added English - UK option to language list - this effects which PayPal page the customer is redirected to.

= 1.3.1 =
* 9/8/15
* Bug fix - Plugin conflict with another plugin

= 1.3 =
* Fix: Compatibility fix for new layout of Contact Form 7 4.2

= 1.2 =
* Fix: Fixed failed to open stream problem
* Update: Updated features available in pro version

= 1.1 =
* Fixed failed to open stream problem
* Fixed Support link
* Added Edit link
* Added Settings link

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.5 =
* 2/6/18
* Major Release - Added Stripe to the plugin

= 1.4.3 =
* 10/23/17
* Fix - Plugin should not work with many more Contact Form 7 extensions, such as Mailchimp, Google Sheets, Datepicker, etc.
* Fix - Currency will now pass through a filter, this is useful as PayPal does not accept $ anymore in front of amounts.

= 1.4.2 =
* 9/15/17
* Bug - Form occasionally would redirect to site homepage even with the form not having PayPal enabled.
* Bug - Spelling mistake.

= 1.4.1 =
* 9/1/17
* Bug - Fixed default redirect method if variable has not been previously set

= 1.4 =
* 8/31/17
* New - Added new redirect method
* New - Added option to change the redirect method on the settings page
* Update - Removed the need for the plugin to write to wp-config
* Update - Updated list of available Pro version features
* Update - Changed how the plugin sends POST data to PayPal
* Update - Cleaned up the code
* Update - Updated the Settings Page usage instructions

= 1.3.5 =
* 6/15/17
* Update - Tested up to WordPress version 4.8
* Fix - Fixed code formatting issues
* Fix - Fixed language text domain issues

= 1.3.4 =
* 3/8/16
* Update - Updated tested up to tag.
* Update - Updated pro url links.

= 1.3.3 =
* 1/21/16
* Bug fix - Settings page not saving on some server configurations.

= 1.3.2 =
* 11/13/15
* Added feature - Added English - UK option to language list - this effects which PayPal page the customer is redirected to.

= 1.3.1 =
* 9/8/15
* Bug fix - Plugin conflict with another plugin

= 1.3 =
Fix: Compatibility fix for new layout of Contact Form 7 4.2

= 1.2 =
Fix: Fixed failed to open stream problem
Update: Updated features available in pro version

= 1.1 =
Fixed failed to open stream problem
Fixed Support link
Added Edit link
Added Settings link

= 1.0 =
Initial release