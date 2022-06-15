<div class="col-sm-<?= $content_width ?> cm-pwa_login ">
  <p class="alert alert-info" role="alert"><?= MODULE_CONTENT_PWA_LOGIN_HEADING ?></p>
    <p>
<?=
  MODULE_CONTENT_PWA_LOGIN_TEXT_1,
  ( defined('MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_STATUS') && MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_STATUS !== 'True' )
  ? MODULE_CONTENT_PWA_LOGIN_TEXT_3
  : MODULE_CONTENT_PWA_LOGIN_TEXT_2
?>
    </p>
    <p class="text-right"><?= new Button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', 'btn-primary btn-block', [], Guarantor::ensure_global('Linker')->build('checkout_guest.php')) ?></p>
</div>
