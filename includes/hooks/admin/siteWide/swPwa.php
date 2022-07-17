<?php
/*
  Purchase without Account for Phoenix
  Version 4.5.4 Phoenix
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

class hook_admin_siteWide_swPwa {

  public $version = '4.5.3';

  function listen_statusUpdateEmail($data) {
    global $check_status, $notify_comments, $orders_status_array, $status, $comments;

    if (  Request::get_page() === 'orders.php' && isset($_POST['add_reviews']) && ($_POST['notify'] === 'on') ) {
      $link = Guarantor::ensure_global('Admin')->catalog('ext/modules/content/reviews/write.php');
      if ($check_status['customers_guest'] == '1') {
        $link->set_parameter('pwa_id', $check_status['reviews_key']);
      }

      $order = new order($data['orders_id']);

      $products_review_links = HOOK_SWPWA_REVIEWS . ':' . "\n";
      foreach ($order->products as $product) {
        $products_review_links .= '<a href="' . $link->set_parameter('products_id', Product::build_prid($product['id'])) . '">' . $product['name'] . '</a>' . "\n";
      }
    }

    $link = $check_status['customers_guest'] == '1' ? '' : sprintf(MODULE_NOTIFICATIONS_UPDATE_ORDER_TEXT_INVOICE_URL, $GLOBALS['Admin']->catalog('account_history_info.php', ['order_id' => $data['orders_id']])) . "\n";
    $email = STORE_NAME . "\n" . MODULE_NOTIFICATIONS_UPDATE_ORDER_SEPARATOR . "\n" .
             sprintf(MODULE_NOTIFICATIONS_UPDATE_ORDER_TEXT_ORDER_NUMBER .  "\n", $data['orders_id']) .
             $link .
             sprintf(MODULE_NOTIFICATIONS_UPDATE_ORDER_TEXT_DATE_ORDERED .  "\n", Date::expound($data['date_purchased'])) . "\n\n" .
             $products_review_links . "\n" .
             $data['notify_comments'] .
             sprintf(MODULE_NOTIFICATIONS_UPDATE_ORDER_TEXT_STATUS_UPDATE, $data['status_name']);

    ob_get_clean();
    ob_start();

    return $email;

  }

  function listen_preAction() {
    global $db;

    $delete_action = ($_GET['action'] ?? '');

    if ( Request::get_page() == 'customers.php' && 'delete_guests' === $delete_action) {

      $delete_guests_query = $db->query(<<<'EOSQL'
SELECT GROUP_CONCAT(c.customers_id)
  FROM customers c, customers_info ci
  WHERE c.customers_guest = '1'
    AND c.customers_id = ci.customers_info_id
    AND ci.customers_info_date_account_created <= curdate() - interval 2 day
EOSQL
      );

      $delete_guests = $delete_guests_query->fetch_assoc();
      $guests_to_delete = $delete_guests['GROUP_CONCAT(c.customers_id)'];
      $num_guests = count(explode(',', $guests_to_delete));

      if ( $num_guests > 0 ) {

        $GLOBALS['messageStack']->add_session(sprintf(HOOK_SWPWA_TEXT_MESSAGE_SUCCESS, $num_guests), 'success');

        $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM customers
  WHERE customers_id IN (%s)
    AND customers_guest = '1'
EOSQL
        , $guests_to_delete));

        $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM address_book
  WHERE customers_id IN (%s)
EOSQL
        , $guests_to_delete));

        $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM customers_info
  WHERE customers_info_id IN (%s)
EOSQL
        , $guests_to_delete));

        $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM customers_basket
  WHERE customers_id IN (%s)
EOSQL
      , $guests_to_delete));

        $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM customers_basket_attributes
  WHERE customers_id IN (%s)
EOSQL
        , $guests_to_delete));

        $maxlifetime = 259200; // 72 hours
        $db->query(sprintf(<<<'EOSQL'
DELETE
  FROM sessions
  WHERE value LIKE '%%customer_is_guest%%'
    AND expiry < '%s'
EOSQL
        , (time() - $maxlifetime)));

        Href::redirect(Guarantor::ensure_global('Admin')->link('customers.php'));
      }
    }
  }

  function listen_constructPaginator( $param ) {
    global $table_definition;

    if ( in_array(Request::get_page(), ['orders.php', 'customers.php', 'reviews.php'], true)
      && isset($table_definition['columns']))
    {
      $index = array_search(TABLE_HEADING_ACTION, array_column($table_definition['columns'], 'name'));
      if (!is_int($index)) {
        $index = -1;
      }

      array_splice($table_definition['columns'], $index, 0, [[
        'name' => HOOK_SWPWA_GUEST,
        'class' => 'text-center',
        'function' => function (&$row) {
          return (($row['customers_guest']) == 1 ? '<i class="fas fa-check"></i>' : '');
        },
      ]]);

    }

    if ( Request::get_page() === 'customers.php' ) {
      $guests_query = $GLOBALS['db']->query(<<<'EOSQL'
SELECT COUNT(*) AS total
  FROM customers c, customers_info ci
  WHERE c.customers_guest = '1'
    AND c.customers_id = ci.customers_info_id
    AND ci.customers_info_date_account_created <= curdate() - interval 2 day
EOSQL
      );

      $GLOBALS['guests'] = $guests_query->fetch_assoc();

      if ( $GLOBALS['guests']['total'] > 0 ) {
        $GLOBALS['admin_hooks']->set('customersListButtons', 'delete_guests', function () {
          return '<p class="mt-3">' . sprintf(HOOK_SWPWA_TEXT_DELETE_GUESTS, $GLOBALS['guests']['total']) . new Button(HOOK_SWPWA_BUTTON_DELETE_GUESTS, 'fas fa-trash', 'btn-danger', [], Guarantor::ensure_global('Admin')->link('customers.php', ['action' => 'delete_guests'])) . '</p>';
        });
      }

    }

  }

  function listen_injectSiteEnd() {
    global $action;

    $output = null;

    if ( Request::get_page() === 'orders.php' && $action === 'edit' ) {

        $guest_review = '<div class="form-group row align-items-center">';
        $guest_review .= '<div class="col-form-label col-sm-3 text-left text-sm-right">' . HOOK_SWPWA_REVIEWS_STATUS . '</div>';
        $guest_review .= '<div class="col-sm-9 pl-5 custom-control custom-switch">';
        $guest_review .= (new Tickable('add_reviews', [
          'value' => 'on',
          'class' => 'custom-control-input',
          'id' => 'oAddReviews',
        ], 'checkbox'))->tick();
        $guest_review .= '<label for="oAddReviews" class="custom-control-label text-muted"><small>' . HOOK_SWPWA_REVIEWS_STATUS_TEXT . '</small></label>';
        $guest_review .= '</div>';
        $guest_review .= '</div>';

        $output = <<<EOD
<script>
$(function() {
$('#oNotifyComments').closest('div').parent().closest('div').after('{$guest_review}');
});
</script>
EOD;

    }

    return $output;

  }

  function listen_accountEditPages($parameters) {
    $parameters['pages'][] = 'checkout_guest';
  }

}
