<?php
/*
* $Id: checkout_guest.php
* $Loc: /
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

  require 'includes/application_top.php';

// needs to be included earlier to set the success message in the messageStack
  require language::map_to_translation('checkout_guest.php');

  if ($_SESSION['cart']->count_contents() < 1) Href::redirect($Linker->build('shopping_cart.php'));

  if (isset($_SESSION['customer_id'])) Href::redirect($Linker->build('checkout_shipping.php'));

  $message_stack_area = 'checkout_guest';

  $page_fields = $customer_data->get_fields_for_page('checkout_guest');
  $customer_details = null;

  if (Form::validate_action_is('process')) {
    $customer_details = $customer_data->process($page_fields);

    $hooks->cat('injectFormVerify');

    if (Form::is_valid()) {
      $customer_data->create($customer_details);

      $customer = new customer($customer_data->get('id', $customer_details));
      $_SESSION['customer_id'] = $customer->get_id();
      $customer_id =& $_SESSION['customer_id'];

      $_SESSION['customer_is_guest'] = 1;

       Form::reset_session_token();
      $_SESSION['cart']->restore_contents();

      $hooks->cat('postRegistration');

      Href::redirect($Linker->build('checkout_shipping.php'));
    }
  }

  $grouped_modules = $customer_data->get_grouped_modules();

  $customer_data_group_query = $db->query(sprintf(<<<'EOSQL'
SELECT *
 FROM customer_data_groups
 WHERE language_id = %d
 ORDER BY cdg_vertical_sort_order
EOSQL
    , (int)$_SESSION['languages_id']));

  require $Template->map(__FILE__, 'page');

  require 'includes/application_bottom.php';
