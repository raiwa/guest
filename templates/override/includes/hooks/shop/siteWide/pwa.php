<?php
/*
* $Id: pwa.php
* $Loc: /templates/override/includes/hooks/shop/siteWide/
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

class hook_shop_siteWide_pwa {

  public $version = '4.6.0.';

  public function listen_orderMail($parameters) {

    $products_review_links = HOOK_PWA_EMAIL_REVIEWS . "\n";
    $email_order = &$parameters['email'];
    $order = $parameters['order'];

    $link = Guarantor::ensure_global('Linker')->build('ext/modules/content/reviews/write.php');
    if (isset($_SESSION['customer_is_guest'])) {
      $email_order = str_replace(MODULE_NOTIFICATIONS_CHECKOUT_TEXT_INVOICE_URL . ' ' . Guarantor::ensure_global('Linker')->build('account_history_info.php', ['order_id' => $order->get_id()]) . "\n", '', $email_order);
      $email_order .= HOOK_PWA_EMAIL_WARNING . "\n\n" .
                      MODULE_NOTIFICATIONS_CHECKOUT_SEPARATOR . "\n";
      if ($order->content_type !== 'physical') {
        $email_order .= sprintf(HOOK_PWA_EMAIL_DOWNLOAD, Guarantor::ensure_global('Linker')->build('contact_us.php')) . "\n" .
                        MODULE_NOTIFICATIONS_CHECKOUT_SEPARATOR . "\n";
      }

      $reviews_key = Password::create_random(12);
      $GLOBALS['db']->query(sprintf(<<<'EOSQL'
UPDATE orders
  SET reviews_key = '%s'
  WHERE customers_id = %d
    AND orders_id = %d
EOSQL
      , $GLOBALS['db']->escape($reviews_key), (int)$_SESSION['customer_id'], (int)$order->get_id()));

      $link->set_parameter('pwa_id', $reviews_key);
    }

    if (MODULE_CONTENT_PWA_LOGIN_CHECKOUT_REGISTERED_REVIEW_LINKS === 'True') {
      foreach ($order->products as $p) {
        $products_review_links .= '<a href="' . $link->set_parameter('products_id', Product::build_prid($p['id'])) . '">' . $p['name'] . '</a>' . "\n";
      }
      $email_order .= $products_review_links . "\n" .
                      MODULE_NOTIFICATIONS_CHECKOUT_SEPARATOR . "\n";
    }

  }

  public function listen_insertOrder($parameters) {
    $orders = &$parameters['sql_data']['orders'];
    $orders['customers_guest'] = (int)($_SESSION['customer_is_guest'] ?? 0);
  }

  public function listen_injectAppTop() {
    global $db;

    // redirect login to create account pwa if set
    if ( Request::get_page() === 'login.php' && defined('MODULE_CONTENT_PWA_LOGIN_REDIRECT_TO_PWA_ACCOUNT') && MODULE_CONTENT_PWA_LOGIN_REDIRECT_TO_PWA_ACCOUNT === 'True' ) {
      Href::redirect(Guarantor::ensure_global('Linker')->build('checkout_guest.php'));
    }

    // check if guest account (e-mail) exists and remove if a new guest account is created
    if ( in_array(Request::get_page(), ['create_account.php', 'checkout_guest.php']) && Form::validate_action_is('process') )  {
      if (mysqli_num_rows($db->query("SELECT * FROM information_schema.columns WHERE table_schema='". DB_DATABASE . "' AND table_name='orders' AND column_name LIKE 'customers_guest'")) == 1 ) {
        $email_address = Text::prepare($_POST['email_address']);
        $check_guest_query = $db->query(sprintf(<<<'EOSQL'
SELECT customers_id
  FROM customers
  WHERE customers_email_address = '%s'
    AND customers_guest = '1'
EOSQL
        , $db->escape($email_address)));

        if ( mysqli_num_rows($check_guest_query) > 0 ) {
          $check_guest = $check_guest_query->fetch_assoc();
          $this->delete_guest_account($check_guest['customers_id']);
        }
      }
    }

// session unregister and delete account for guests on logoff
    if ( Request::get_page() === 'logoff.php' && isset($_SESSION['customer_is_guest']) ) {
      $this->delete_guest_account($_SESSION['customer_id']);
    }

// session unregister and delete account for guests
    if ( Request::get_page() === 'checkout_success.php' && isset($_SESSION['customer_is_guest']) ) {
      $_SESSION['navigation']->set_snapshot();

      if ( isset($_GET['action']) && ($_GET['action'] === 'update') ) {
        // redirect to set password if selected
        if ( isset($_POST['pwa_account']) && $_POST['pwa_account'] === 'true' ) {
          if ( !empty($_POST['notify']) && is_array($_POST['notify']) ) {
            $notify = array_unique($_POST['notify']);

            foreach ( $notify as $n ) {
              if ( is_numeric($n) && ($n > 0) ) {
                $check_query = $db->query(sprintf(<<<'EOSQL'
SELECT products_id
  FROM products_notifications
    WHERE products_id = '%s'
      AND customers_id = %d
    LIMIT 1
EOSQL
                , (int)$n, (int)$_SESSION['customer_id']));

                if ( !mysqli_num_rows($check_query) ) {
                  $db->query(sprintf(<<<'EOSQL'
INSERT INTO products_notifications (products_id, customers_id, date_added)
  VALUES ('%s', %s, NOW())
EOSQL
                , (int)$n, (int)$_SESSION['customer_id']));

                }
              }
            }
          }
          Href::redirect(Guarantor::ensure_global('Linker')->build('ext/modules/content/account/set_password.php'));
        } elseif (!isset($_POST['pwa_account']) || ($_POST['pwa_account'] !== 'true') ) {
          // delete guest account if selected
          $this->delete_guest_account($_SESSION['customer_id']);
        }
        if ( (!defined('MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_STATUS') || MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_STATUS != 'True') && DOWNLOAD_ENABLED != 'true' ) {
        // delete guest account
          $this->delete_guest_account($_SESSION['customer_id']);
        }
        Href::redirect(Guarantor::ensure_global('Linker')->build('index.php'));
      }

      $guest_account_script = '
<script>
$(document).ready(function() {
$("input:checkbox[name^=\'notify\']").hide().attr("disabled", true);
$(".cm-cs-product-notifications .card-header").html(\'' . HOOK_PWA_TEXT_PRODUCTS . '\');
$(".cm-cs-thank-you .border .list-group-item-action:nth-of-type(1)").addClass(\'d-none\');' . "\n";

      if ( defined('MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_STATUS') && MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_STATUS == 'True' ) {
      $guest_account_script .= '
$("#download-account").html(\'<p>' . HOOK_PWA_DOWNLOAD . '</p>\');' . "\n";
      } else {
      $guest_account_script .= '
$("#download-account").text(\'\');' . "\n";
      }
      $guest_account_script .= '
$("input:radio[name=\'pwa_account\']").click(function() {
  if($("input:radio[name=\'pwa_account\'][value=\'false\']").is(\':checked\')) {
    console.log(\'selected\');
    $("input:checkbox[name^=\'notify\']").hide().attr("disabled", true);
    $(".cm-cs-product-notifications .card-header").html(\'' . HOOK_PWA_TEXT_PRODUCTS . '\');
    } else {
    console.log(\'notselected\');
    $("input:checkbox[name^=\'notify\']").show().removeAttr("disabled", true);
    $(".cm-cs-product-notifications .card-header").html(\'' . MODULE_CONTENT_CHECKOUT_SUCCESS_PRODUCT_NOTIFICATIONS_TEXT_NOTIFY_PRODUCTS . '\');
  }
});
});
</script>
      ' . "\n";

      $GLOBALS['Template']->add_block($guest_account_script, 'footer_scripts');
    }

    if ( isset($_SESSION['customer_is_guest']) ) {
      if ( defined('MODULE_NAVBAR_ACCOUNT_STATUS') && MODULE_NAVBAR_ACCOUNT_STATUS === 'True' ) {
        $GLOBALS['Template']->add_block('<script>$(".nb-account .dropdown-toggle").addClass("disabled");</script>', 'footer_scripts');
      }
      if ( defined('MODULE_CONTENT_HEADER_BUTTONS_STATUS') && MODULE_CONTENT_HEADER_BUTTONS_STATUS === 'True' ) {
        $GLOBALS['Template']->add_block('<script>$(".cm-header-buttons .btn:nth-of-type(n+3)").addClass("disabled");</script>', 'footer_scripts');
      }
      if ( defined('MODULE_CONTENT_FOOTER_ACCOUNT_STATUS') && MODULE_CONTENT_FOOTER_ACCOUNT_STATUS === 'True' ) {
        $GLOBALS['Template']->add_block('<script>$(".cm-footer-account .nav-link").addClass("disabled");</script>', 'footer_scripts');
      }
    }

    // do things if a guest comes from checkout success
    if ( isset($_SESSION['customer_is_guest'], $_SESSION['navigation']->snapshot['page']) && $_SESSION['navigation']->snapshot['page'] === 'checkout_success.php' ) {
      if ( defined('MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_STATUS') && MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_STATUS === 'True' &&
        Request::get_page() === 'account.php' && strpos($GLOBALS['messageStack']->output('account'), 'alert-success') ) {
        // Unregister and remove guest from customers table if password is successfully set
        unset($_SESSION['customer_is_guest']);
        $db->query(sprintf(<<<'EOSQL'
UPDATE customers
  SET customers_guest = '0'
  WHERE customers_id = %d
EOSQL
          , (int)$_SESSION['customer_id']));
      } elseif ( Request::get_page() !== 'download.php' && basename(Request::get_page()) !== 'set_password.php' && !Text::is_prefixed_by(Request::get_page(), 'checkout') ) {
        // else delete guest account
        $this->delete_guest_account($_SESSION['customer_id']);
      }
    }

  }

  protected function delete_guest_account($customer_id) {
    global $db;

    unset($_SESSION['customer_is_guest']);
    $GLOBALS['hooks']->register_pipeline('reset');

    $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM customers
  WHERE customers_id = %d
    AND customers_guest = '1'
EOSQL
      , (int)$customer_id));

    $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM address_book
  WHERE customers_id = %d
EOSQL
      , (int)$customer_id));

    $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM customers_info
  WHERE customers_info_id = %d
EOSQL
      , (int)$customer_id));

    $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM customers_basket
  WHERE customers_id = %d
EOSQL
      , (int)$customer_id));

    $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM customers_basket_attributes
  WHERE customers_id = %d
EOSQL
      , (int)$customer_id));

    unset($_SESSION['customer_id']);
  }

}
