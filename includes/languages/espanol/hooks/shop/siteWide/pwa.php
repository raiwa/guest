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
const HOOK_PWA_TEXT_PRODUCTS = '<strong>Producto(s) comprado(s:</strong>';
const HOOK_PWA_DOWNLOAD = '<br>Puede descargar sus productos más adelante en su Cuenta en la página "Ver pedidos", si opta por una cuenta permanente configurando su contraseña.';

// Language definitions used in checkout_process.php for order confirmation mail
const HOOK_PWA_EMAIL_WARNING = 'NOTA: Esta dirección de correo electrónico ha sido enviado por un visitante de nuestra tienda online. Si no fuera este visitante, por favor, envíe un mensaje a: ' . STORE_OWNER_EMAIL_ADDRESS . '. Gracias por su compra y tenga un buen día.';
const HOOK_PWA_EMAIL_DOWNLOAD = 'Si tiene alguna dificultad para descargar el producto comprado, por favor, contáctenos en nuestra página de, <a class="btn btn-info" role="button" href="%s">Contacto</a>.';
const HOOK_PWA_EMAIL_REVIEWS = 'Nos gustaría pedirle escribir una valoración de los productos que ha comprado';
