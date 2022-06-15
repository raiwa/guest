<?php
/*
  $Id$

  Purchase without Account for Phoenix
  Version 4.5.3 Phoenix
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

  chdir('../../../../');
  require 'includes/application_top.php';

  if ( isset($_SESSION['customer_id']) )  {
	  // customer logged on so not a guest
    Href::redirect($Linker->build('index.php'));
  }

// if the customer is not logged on, carry on, must be a guest
  if ( !isset($_GET['pwa_id'], $_GET['products_id'])) {
    Href::redirect($Linker->build('product_info.php')->retain_query_except(['action']));
  }

  if (!($product instanceof Product) || !$product->get('status')) {
    Href::redirect($Linker->build('product_info.php', ['products_id' => (int)$_GET['products_id']]));
  }

  $pwa_id = Text::input($_GET['pwa_id']);

// check that pwa_id exists
  $customer_query = $db->query(sprintf(<<<'EOSQL'
SELECT customers_name, orders_id
  FROM orders
  WHERE reviews_key = '%s'
EOSQL
    , $db->escape($pwa_id)));

  if (!mysqli_num_rows($customer_query)) {
    Href::redirect($Linker->build('product_info.php')->retain_query_except(['action']));
  }

  $guest_customer = $customer_query->fetch_assoc();

// need to check that products_id relates to the pwa_id
  $orders_products_query = $db->query(sprintf(<<<'EOSQL'
SELECT products_id
  FROM orders_products
  WHERE orders_id = %d AND products_id = %d
 LIMIT 1
EOSQL
    , (int)$guest_customer['orders_id']), (int)$_GET['products_id']);

  if (!mysqli_num_rows($orders_products_query)) {
    Href::redirect($Linker->build('product_info.php')->retain_query_except(['action']));
  }

  $hooks->register_pipeline('write');
  require language::map_to_translation('modules/content/reviews/write.php');

  if (Form::validate_action_is('process')) {
    $rating = Text::input($_POST['rating']);
    $review = Text::input($_POST['review']);
    $nickname = Text::input($_POST['nickname']);

    if (ALLOW_ALL_REVIEWS == 'false') {
      if ($_POST['nickname'] != $guest_customer['customers_name']) {
        $nickname = sprintf(VERIFIED_BUYER, $nickname);
      }
    }

    $db->query(sprintf(<<<'EOSQL'
INSERT INTO reviews (products_id, customers_id, customers_name, customers_guest, reviews_rating, date_added)
  VALUES (%d, 0, '%s', '1', '%s', now())
EOSQL
      , (int)$_GET['products_id'], $db->escape($nickname), $db->escape($rating)));

    $insert_id = mysqli_insert_id($db);

    $db->query(sprintf(<<<'EOSQL'
INSERT INTO reviews_description (reviews_id, languages_id, reviews_text)
  VALUES (%d, %d, '%s')
EOSQL
      , (int)$insert_id, (int)$_SESSION['languages_id'], $db->escape($review)));

    $messageStack->add_session('product_action', sprintf(TEXT_REVIEW_RECEIVED, htmlspecialchars($guest_customer['customers_name'])), 'success');

    Href::redirect($Linker->build('product_info.php')->retain_query_except(['action']));
  }

  $customer = new class($guest_customer['customers_name']) {

    protected $name;

    public function __construct($name) {
      $this->name = $name;
    }

    public function get() {
      return $this->name;
    }

  };

  require $Template->map(__FILE__, 'ext');
  require 'includes/application_bottom.php';
