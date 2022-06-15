<?php
/*
  $Id$

  Purchase without Account for Phoenix
  Version 4.4.0 Phoenix
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

  class hook_shop_write_zz_redirect {

    public function listen_injectSiteEnd() {
      if (isset($_GET['pwa_id'])) {
        return <<<'EOSQL'
<style>
  .rating > input { display: none; }
  .rating > label:before { content: "\f005"; margin: 2px; font-size: 1.5em; font-family: "Font Awesome 5 Free"; display: inline-block; }
  .rating > label { color: #ccc; }
  .rating > input:checked ~ label { color: #ffca08; font-weight: 900; }
  .rating > input:hover ~ label { color: #ffca08; cursor: pointer; font-weight: 900; }
</style>
EOSQL;
      }
    }

    public function listen_loginRequiredStart() {
      if (!isset($_GET['pwa_id'])) {
        Login::require();
        return;
      }

// check that pwa_id exists
      $customer_query = $GLOBALS['db']->query(sprintf(<<<'EOSQL'
SELECT customers_name, orders_id
  FROM orders
  WHERE reviews_key = '%s'
EOSQL
        , $GLOBALS['db']->escape(Text::input($_GET['pwa_id']))));

      if (!mysqli_num_rows($customer_query)) {
        Href::redirect($GLOBALS['Linker']->build('product_info.php')->retain_query_except(['action']));
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
        Href::redirect($Linker->build('product_info.php')->retain_query_except(['action', 'pwa_id']));
      }

      $GLOBALS['customer'] = new class($guest_customer['customers_name']) {

        protected $name;

        public function __construct($name) {
          $this->name = $name;
        }

        public function get_id() {
          return 0;
        }

        public function get() {
          return $this->name;
        }

      };
    }

  }
