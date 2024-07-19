<?php
/*
* $Id: cm_pwa_login.php
* $Loc: /includes/modules/content/login/
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

  class cm_pwa_login extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PWA_LOGIN_';

    public function __construct() {
      parent::__construct(__FILE__);

      if (is_object($_SESSION['cart'] ?? null)) {
        $cart_content = $_SESSION['cart']->get_content_type();
        $cart_count = $_SESSION['cart']->count_contents();
        if ( $cart_count < 1 || (MODULE_CONTENT_PWA_LOGIN_VIRTUAL !== 'True' && $cart_content !== 'physical') ) {
          $this->enabled = false;
        }
      }

      if (!file_exists(DIR_FS_CATALOG . 'templates/' . TEMPLATE_SELECTION . '/includes/hooks/shop/siteWide/pwa.php')) {
        $this->description = '<div class="alert alert-danger" role="alert">' .
                                 MODULE_CONTENT_PWA_LOGIN_HOOK_MODULE_WARNING .
                             '</div>' .
                             $this->description;
        $this->enabled = false;
      }

    }

    public function execute() {
      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        $this->config_key_base . 'VERSION' => [
          'title' => 'Current Version',
          'value' => '4.6.4. Phoenix',
          'desc' => 'Version info. It is read only',
          'set_func' => 'cm_pwa_login::readonly(',
        ],
        $this->config_key_base . 'STATUS' => [
          'title' => 'Enable PWA Login Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        $this->config_key_base . 'CONTENT_WIDTH' => [
          'title' => 'Content Container',
          'value' => 'col-sm-6',
          'desc' => 'What container should the content be shown in? (col-*-12 = full width, col-*-6 = half width).',
        ],
        $this->config_key_base . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '1500',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        $this->config_key_base . 'REMOVE_DATA' => [
          'title' => 'Uninstall Removes Database columns',
          'value' => 'False',
          'desc' => 'Do you want to remove the pwa guest flag column when uninstall the module? (Guest orders will not be deleted, but will lose their guest order flags)',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        $this->config_key_base . 'VIRTUAL' => [
          'title' => 'Allow guest checkout for virtual products',
          'value' => 'False',
          'desc' => 'Do you wish to allow guest checkout for orders containing virtual-downloadable products.<br>Also applies for mixed orders.',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        $this->config_key_base . 'PAYMENT_MODULES' => [
          'title' => 'Exclude Payment Modules for Virtual Guest Orders',
          'value' => '',
          'desc' => 'The payment modules to exclude for guests and orders including virtual (downloadable) products.',
          'set_func' => 'cm_pwa_login::cm_pwa_login_edit_payment_modules(',
          'use_func' => 'abstract_module::list_exploded',
        ],
        $this->config_key_base . 'CHECKOUT_GUEST_REVIEW_LINKS' => [
          'title' => 'Add Review Links to Guest Order Mail',
          'value' => 'True',
          'desc' => 'Do you wish to add a list with all products linked to the write review pages to the order confirmation Mail for guests?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        $this->config_key_base . 'CHECKOUT_REGISTERED_REVIEW_LINKS' => [
          'title' => 'Add Review Links to Standard Order Mail',
          'value' => 'True',
          'desc' => 'Do you wish to add a list with all products linked to the write review pages to the order confirmation Mail for registered customers?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        $this->config_key_base . 'REDIRECT_TO_PWA_ACCOUNT' => [
          'title' => 'Redirect to Create Account PWA',
          'value' => 'False',
          'desc' => 'Do you wish to redirect the login page to Create Account PWA? Use this if you wish to offer Guest Checkout as stand alone Option.',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
      ];
    }


    public function install($parameter_key = null) {
      parent::install($parameter_key);
      global $db;

      if (mysqli_num_rows($db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='orders' AND COLUMN_NAME LIKE 'customers_guest'")) != 1 ) {
        $db->query("ALTER TABLE orders ADD customers_guest INT(1) NOT NULL DEFAULT '0'");
      }
      if (mysqli_num_rows($db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='orders' AND COLUMN_NAME LIKE 'reviews_key'")) != 1 ) {
        $db->query("ALTER TABLE orders ADD reviews_key VARCHAR(12) NOT NULL DEFAULT '0'");
      }
      if (mysqli_num_rows($db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='customers' AND COLUMN_NAME LIKE 'customers_guest'")) != 1 ) {
        $db->query("ALTER TABLE customers ADD customers_guest INT(1) NOT NULL DEFAULT '0'");
      }
      if (mysqli_num_rows($db->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='". DB_DATABASE . "' AND TABLE_NAME='reviews' AND COLUMN_NAME LIKE 'customers_guest'")) != 1 ) {
        $db->query("ALTER TABLE reviews ADD customers_guest INT(1) NOT NULL DEFAULT '0'");
      }

      $db->query("DELETE FROM hooks WHERE hooks_group = 'checkout_confirmation' AND hooks_class = 'cd_matc'");
    }

    public function remove($parameter_key = null) {
      parent::remove($parameter_key);

      if ( defined('MODULE_CONTENT_PWA_LOGIN_REMOVE_DATA') && MODULE_CONTENT_PWA_LOGIN_REMOVE_DATA === 'True' ) {
        global $db;

        $db->query("ALTER TABLE customers DROP customers_guest");
        $db->query("ALTER TABLE orders DROP customers_guest");
        $db->query("ALTER TABLE orders DROP reviews_key");
        $db->query("ALTER TABLE reviews DROP customers_guest");
      }
    }

    public static function readonly($value) {
      return $value;
    }

    public static function cm_pwa_login_edit_payment_modules($values, $key) {
      $installed_modules = explode(';', MODULE_PAYMENT_INSTALLED);
      return Config::select_multiple($installed_modules, $values, $key);
    }

}
