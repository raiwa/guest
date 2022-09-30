<div class="col-sm-12 cm-cs-pwa-keep-account">
  <div class="card mb-2">
    <div class="card-header"><?= MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_PUBLIC_TITLE ?></div>
    <div class="card-body">
      <div class="form-check">
        <div class="col-sm-12 custom-control custom-radio">
          <?= new Tickable('pwa_account', ['value' => 'true', 'id' => 'cKeep', 'required' => null, 'class' => 'custom-control-input'], 'radio') ?>
          <label for="cKeep" class="custom-control-label text-muted"><?= MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_TEXT_SET_PASSWORD ?></label>
        </div>
        <div class="col-sm-12 custom-control custom-radio">
          <?= new Tickable('pwa_account', ['value' => 'false', 'id' => 'cDelete', 'class' => 'custom-control-input'], 'radio') ?>
          <label for="cDelete" class="custom-control-label text-muted"><?= MODULE_CONTENT_CHECKOUT_SUCCESS_PWA_KEEP_ACCOUNT_TEXT_DELETE_ACCOUNT ?></label>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
/*
  $Id$

  Purchase without Account for Phoenix
  Version 4.6.0 Phoenix
  by @raiwa
  info@oscaddons.com
  www.oscaddons.com

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/


