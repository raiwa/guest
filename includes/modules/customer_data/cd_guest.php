<?php
/*
 $Id$

  Purchase without Account for Phoenix
  Version 4.6.0 Phoenix
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

  class cd_guest extends abstract_customer_data_module {

    const CONFIG_KEY_BASE = 'MODULE_CUSTOMER_DATA_GUEST_';

    const PROVIDES = [ 'guest' ];
    const REQUIRES = [  ];

    protected function get_parameters() {
      return [
        static::CONFIG_KEY_BASE . 'STATUS' => [
          'title' => 'Enable Guest Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        static::CONFIG_KEY_BASE . 'PAGES' => [
          'title' => 'Pages',
          'value' => 'checkout_guest',
          'desc' => 'Only used on the guest checkout page',
          'set_func' => 'cd_guest::readonly(',
        ],
      ];
    }

    public function display_input(&$customer_details = null) {
    }

    public function process(&$customer_details) {
      if (Request::get_page() === 'checkout_guest.php')  {
        $customer_details['guest'] = '1';
      }

      return true;
    }

    public function build_db_values(&$db_tables, $customer_details, $table = 'both') {
      Guarantor::guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_guest'] = $customer_details['guest'];
    }

    public function build_db_aliases(&$db_tables, $table = 'both') {
      Guarantor::guarantee_subarray($db_tables, 'customers');
      $db_tables['customers']['customers_guest'] = 'guest';
    }

    public static function readonly($value) {
      return $value;
    }

  }
