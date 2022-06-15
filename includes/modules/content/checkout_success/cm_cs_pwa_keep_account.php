<?php
/*
  $Id$

  Purchase without Account for Phoenix
  Version 4.5.0 Phoenix
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

  class cm_cs_pwa_keep_account extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_';

    public function __construct() {
      parent::__construct(__FILE__);

      if (!file_exists(DIR_FS_CATALOG . 'templates/' . TEMPLATE_SELECTION . '/includes/hooks/shop/siteWide/pwa.php')) {
        $this->enabled = false;
        $this->description = '<div class="alert alert-danger" role="alert">' .
                                 MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_HOOK_WARNING .
                             '</div>' .
                             $this->description;
      }

      if (!defined('MODULE_CONTENT_PWA_LOGIN_STATUS') || defined('MODULE_CONTENT_PWA_LOGIN_STATUS') && MODULE_CONTENT_PWA_LOGIN_STATUS != 'True' ) {
        $this->description = '<div class="alert alert-danger" role="alert">' .
                                 MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_PWA_LOGIN_WARNING .
                             '  <a href="modules_content.php?module=cm_pwa_login&action=install">' . MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_INSTALL_NOW . '</a>
                             </div>' .
                             $this->description;
      }

    }

    public function execute() {

      $content_width = (int)MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_CONTENT_WIDTH;

      if (isset($_SESSION['customer_is_guest'])) {

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_VERSION' => [
          'title' => 'Current Version',
          'value' => '4.5.0. Phoenix',
          'desc' => 'Version info. It is read only',
          'set_func' => 'cm_cs_pwa_keep_account::readonly(',
        ],
        'MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_STATUS' => [
          'title' => 'Enable PWA Keep Account Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        'MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "Config::select_one(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '100',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

    public static function readonly($value) {
      return $value;
    }
  }
