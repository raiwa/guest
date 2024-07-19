<?php
/*
* $Id: pwa.php
* $Loc: /includes/languages/english/hooks/shop/siteWide/
*
* Name: PWAGuestAccount
* Version: 4.5.4
* Release Date: 2024-07-19
* Author: Rainer Schmied
* 	 phoenixcartaddonsaddons.com / raiwa@phoenixcartaddons.com
*
* License: Released under the GNU General Public License
*
* Comments: Author: [Rainer Schmied @raiwa]
* Author URI: [www.phoenixcartaddons.com]
* 
* CE Phoenix, E-Commerce made Easy
* https://phoenixcart.org
* 
* Copyright (c) 2021 Phoenix Cart
* 
* 
*/

// Language definitions used in checkout_success.php
const HOOK_PWA_TEXT_PRODUCTS = '<strong>Please note the item(s) purchased:</strong>';
const HOOK_PWA_DOWNLOAD = '<br>You can download your products at a later time in your Account at "View Orders" page if you opt for a permanent account by setting your password.';

// Language definitions used in checkout_process.php for order confirmation mail
const HOOK_PWA_EMAIL_WARNING = 'NOTE: This email address has been submitted by a visitor to our online-shop. If you were not this visitor, please send a message to:  ' . STORE_OWNER_EMAIL_ADDRESS . '. Thank you for your purchase and have a nice day.';
const HOOK_PWA_EMAIL_DOWNLOAD = 'If you experience any difficulty to download the purchased product, please contact us on our <a class="btn btn-info" role="button" href="%s">Contact Us</a> page';
const HOOK_PWA_EMAIL_REVIEWS = 'We would like to ask you to leave a review of the products you have purchased';
