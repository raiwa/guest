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

// Language definitions used in checkout_success.php
const HOOK_PWA_TEXT_PRODUCTS = '<strong>Gekaufte Produkte:</strong>';
const HOOK_PWA_DOWNLOAD = '<br>Sie können Ihre Produkte zu einem späteren Zeitpunkt in Ihrem Konto auf der Seite "Bestellungen anzeigen" herunterladen, wenn Sie sich für ein permanentes Konto entscheiden und ein Passwort setzen.';

// Language definitions used in checkout_process.php for order confirmation mail
const HOOK_PWA_EMAIL_WARNING = 'Achtung: Diese Email-Adresse wurde uns von einem Besucher unseres Online-Shops übermittelt. Falls Sie nicht dieser Besucher waren, senden Sie bitte eine Mitteilung an:  ' . STORE_OWNER_EMAIL_ADDRESS . '. Danke für Ihre Bestellung und einen schönen Tag.';
const HOOK_PWA_EMAIL_DOWNLOAD = 'Wenn Sie Schwierigkeiten haben das gekaufte Produkt herunterzuladen, kontaktieren Sie uns bitte auf unserer <a class="btn btn-info" role="button" href="%s">Kontakt</a> Seite';
const HOOK_PWA_EMAIL_REVIEWS = 'Wir möchten Sie bitten eine Bewertung der gekauften Artikel zu schreiben';
