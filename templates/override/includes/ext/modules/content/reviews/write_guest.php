<?php
/*
  $Id$

  Purchase without Account for Phoenix
  Version 4.6.0. Phoenix
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

  $breadcrumb->add(NAVBAR_TITLE, Guarantor::ensure_global('Linker')->build()->retain_query_except());

  require $Template->map('template_top.php', 'component');
?>

<div class="row">
  <h1 class="display-4 col-sm-8"><?= $product->get('products_name') ?></h1>
  <h2 class="display-4 col-sm-4 text-left text-sm-right"><?= $product->hype_price() ?></h2>
</div>

<?= new Form('review', $GLOBALS['Linker']->build('ext/modules/content/reviews/write_guest.php', ['action' => 'process', 'products_id' => (int)$_GET['products_id'], 'pwa_id' => $_GET['pwa_id']])) ?>

  <div class="alert alert-warning" role="alert">
    <?= sprintf(TEXT_REVIEW_WRITING, htmlspecialchars($guest_customer['customers_name']), $product->get('products_name')) ?>
  </div>

  <div class="form-group row">
    <label for="inputNickName" class="col-form-label col-sm-3 text-left text-sm-right"><?= SUB_TITLE_FROM ?></label>
    <div class="col-sm-9">
      <?=
       (new Input('nickname', htmlspecialchars($guest_customer['customers_name']), ['id' => 'inputNickName', 'placeholder' => SUB_TITLE_REVIEW_NICKNAME]))->require(),
       FORM_REQUIRED_INPUT
?>
    </div>
  </div>

  <div class="form-group row">
    <label for="inputReview" class="col-form-label col-sm-3 text-left text-sm-right"><?= SUB_TITLE_REVIEW ?></label>
    <div class="col-sm-9">

<?= (new Textarea('review', ['cols' => '60', 'rows' => '15', 'id' => 'inputReview', 'placeholder' => SUB_TITLE_REVIEW_TEXT]))->require(),
      FORM_REQUIRED_INPUT
?>
    </div>
  </div>

  <div class="form-group row align-items-center">
    <label class="col-form-label col-sm-3 text-left text-sm-right"><?= SUB_TITLE_RATING ?></label>
    <div class="col-sm-9">
      <div class="rating d-flex justify-content-end flex-row-reverse align-items-baseline">
        <?= sprintf(TEXT_GOOD, 5) ?>
        <input type="radio" id="r5" name="rating" required aria-required="true" value="5"><label title="<?= sprintf(TEXT_RATED, sprintf(TEXT_GOOD, 5)) ?>" for="r5">&nbsp;</label>
        <input type="radio" id="r4" name="rating" value="4"><label title="<?= sprintf(TEXT_RATED, 4) ?>" for="r4">&nbsp;</label>
        <input type="radio" id="r3" name="rating" value="3"><label title="<?= sprintf(TEXT_RATED, 3) ?>" for="r3">&nbsp;</label>
        <input type="radio" id="r2" name="rating" value="2"><label title="<?= sprintf(TEXT_RATED, 2) ?>" for="r2">&nbsp;</label>
        <input type="radio" id="r1" name="rating" checked value="1"><label title="<?= sprintf(TEXT_RATED, sprintf(TEXT_BAD, 1)) ?>" for="r1">&nbsp;</label>
      </div>
    </div>
  </div>

  <?= $hooks->cat('injectFormDisplay') ?>

  <div class="buttonSet">
    <div class="text-right"><?= new Button(IMAGE_BUTTON_ADD_REVIEW, 'fas fa-pen', 'btn-success btn-lg btn-block') ?></div>
    <p><?= new Button(IMAGE_BUTTON_BACK, 'fas fa-angle-left', 'btn-light mt-2', [], $product->get('link')) ?></p>
  </div>

  <hr>

  <div class="row">
    <div class="col-sm-8"><?= $product->get('products_description') ?></div>
    <div class="col-sm-4"><?= new Image('images/' . $product->get('image'), ['alt' => htmlspecialchars($product->get('products_name'))]) ?></div>
  </div>

</form>

<?php
  require $Template->map('template_bottom.php', 'component');
?>
