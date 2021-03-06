=== Give - Moneris Payment Gateway ===
Contributors: givewp
Tags: donations, donation, ecommerce, e-commerce, fundraising, fundraiser, moneris, gateway
Requires at least: 4.8
Tested up to: 5.7
Stable tag: 1.1.0
Requires Give: 2.3.0
License: GPLv3
License URI: https://opensource.org/licenses/GPL-3.0

Adds support to accept donations via the Moneris Payment gateway.

== Description ==

This plugin requires the Give plugin activated to function properly. When activated, it adds support to accept donations via the Moneris Payment gateway.

== Installation ==

= Minimum Requirements =

* WordPress 5.0 or greater
* PHP version 5.3 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of Give, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "Give" and click Search Plugins. Once you have found the plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now".

= Manual installation =

The manual installation method involves downloading our donation plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Changelog ==

= 1.1.0: March , 2021  =
* New: Added CVD / CVV (the last-3 numbers on the back of a credit or debit card) validation via the Moneris eFraud API.

= 1.0.1: May 19th, 2020  =
* Fix: We resolved a bug which was causing amounts over $999.99 to be rejected by the Moneris API due to an incorrect currency format. The Moneris API does not want any thousands separators passed in the payment request. We have removed the thousands separators and tested thoroughly to ensure large amounts are fully supported.

= 1.0.0 =
* Initial plugin release. Yippee!
