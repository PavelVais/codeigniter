<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Website details
|
| These details are used in emails sent by authentication library.
|--------------------------------------------------------------------------
*/

$config['secure_authorization'] = TRUE;


$config['login_column'] = "username";

$config['check_unique_username'] = TRUE;
$config['check_unique_email'] = TRUE;
$config['email_activation'] = TRUE;


/*
  | -------------------------------------------------------------------
  |  URCENI ROLI
  | -------------------------------------------------------------------
 *  Role k uctu muze byt v samostatne tabulce, nebo je primo u uctu
 * Pokud je primo u uctu, nechte kolonku prazdnou, v druhem pripade
 * napiste v jake tabulce role je
 */
$config['roleTable'] = 'roles';

/*
|--------------------------------------------------------------------------
| Facebook authorization
|
| These details are used in emails sent by authentication library.
|--------------------------------------------------------------------------
*/
$config['fcb_secred_app_id'] = "58a59b538e911ba9ce348de9d72c461c";

/*
|--------------------------------------------------------------------------
| Security settings
|
| The library uses PasswordHash library for operating with hashed passwords.
| 'phpass_hash_portable' = Can passwords be dumped and exported to another server. If set to FALSE then you won't be able to use this database on another server.
| 'phpass_hash_strength' = Password hash strength.
|--------------------------------------------------------------------------
*/
$config['phpass_hash_portable'] = FALSE;
$config['phpass_hash_strength'] = 8;

/*
|--------------------------------------------------------------------------
| Auto login settings
|
| 'autologin_cookie_name' = Auto login cookie name.
| 'autologin_cookie_life' = Auto login cookie life before expired. Default is 2 months (60*60*24*31*2).
|--------------------------------------------------------------------------
*/
$config['autologin_cookie_name'] = 'autologin';
$config['autologin_cookie_life'] = 60*60*24*31*2;