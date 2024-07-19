<?php
/*
* $Id: checkout_guest.php
* $Loc: /templates/override/includes/pages/
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

  $breadcrumb->add(NAVBAR_TITLE, $Linker->build('checkout_guest.php'));

  require $Template->map('template_top.php', 'component');
?>

<h1 class="display-4"><?= HEADING_TITLE ?></h1>

<?php
  if ($messageStack->size($message_stack_area) > 0) {
    echo $messageStack->output($message_stack_area);
  }
?>

<div class="alert alert-warning" role="alert">
  <div class="row">
    <div class="col-sm-9"><?= sprintf(TEXT_ORIGIN_LOGIN, $Linker->build('login.php')->retain_query_except()) ?></div>
    <div class="col-sm-3 text-left text-sm-right"><span class="text-danger"><?= FORM_REQUIRED_INFORMATION ?></span></div>
  </div>
</div>

<?php
  echo (new Form('checkout_guest', $Linker->build(), 'post', [], true))->hide('action', 'process');

  echo '<div class="row">';

  while ($customer_data_group = $customer_data_group_query->fetch_assoc()) {
    $modules = $grouped_modules[$customer_data_group['customer_data_groups_id']] ?? [];
    $modules = array_filter($modules, function ($v) use ($page_fields) {
      return count(array_intersect(get_class($v)::PROVIDES, $page_fields)) > 0;
    });

    if ([] === $modules) {
      continue;
    }
    ?>

    <div class="<?= $customer_data_group['customer_data_groups_width'] ?>">

      <h4><?= $customer_data_group['customer_data_groups_name'] ?></h4>

      <?php
      foreach ($modules as $module) {
        $module->display_input($customer_details);
      }
      ?>
      
    </div>
    
    <?php
  }

  echo '</div>';

  echo $hooks->cat('injectFormDisplay');
?>

  <div class="buttonSet">
    <div class="text-right"><?= new Button(IMAGE_BUTTON_CONTINUE, 'fas fa-user', 'btn-success btn-block btn-lg') ?></div>
  </div>

</form>

<?php
  require $Template->map('template_bottom.php', 'component');
?>
