<?php
/*
  $Id$

  Purchase without Account for Phoenix
  Version 4.6.1. Phoenix
  by @raiwa
  info@oscaddons.com
  www.oscaddons.com
  all credits to @deDocta

  Copyright (c) 2021 Rainer Schmied

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
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
SELECT customer_data_groups_id, customer_data_groups_name
 FROM customer_data_groups
 WHERE language_id = %d
 ORDER BY cdg_vertical_sort_order, cdg_horizontal_sort_order
EOSQL
    , (int)$_SESSION['languages_id']));

  require $Template->map(__FILE__, 'page');

  require 'includes/application_bottom.php';
