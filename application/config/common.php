<?php

/*
  |--------------------------------------------------------------------------
  | Config file for MyslimNaTebe.cz
  |--------------------------------------------------------------------------
 */


/*
  |--------------------------------------------------------------------------
  | SECTION: Points
  |--------------------------------------------------------------------------
  |--------------------------------------------------------------------------
  | Request Points
  |--------------------------------------------------------------------------
  |
  | Kolik je potřeba získat bodů, aby byl účet považován za autorizovaný
  |
 */
$config['requested_points'] = 60;


/*
  |--------------------------------------------------------------------------
  | Points
  |--------------------------------------------------------------------------
  |
  | Odmena za jednotlive sluzby v bodech.
  |
 */
$config['points']['wish_points'] = 10;		//= Mysleni na prani
$config['points']['like_points'] = 20;		//= Lajkovani nasi fcb stranky
$config['points']['connect_points'] = 10;	//= Propojeni uctu s facebookem
$config['points']['phone_points'] = 30;	//= Propojeni uctu s mobilem


/*
  |--------------------------------------------------------------------------
  | Email to SMS upozorneni
  |--------------------------------------------------------------------------
  |
  | Tvary cisel, na ktere se odesila upozorneni.
  | Místo fyzickeho cisla je dan zastupce %cislo%
  |
 */
$config['smsnotification']['tmobile'] = '%cislo%@sms.t-mobile.cz';
$config['smsnotification']['vodafone'] = '%cislo%@vodafonemail.cz';
$config['smsnotification']['o2'] = '%cislo%@sms.cz.o2.com';

$config['sms_owner_email'] = "sms@myslimnatebe.cz";


/*
  |--------------------------------------------------------------------------
  | Email
  |--------------------------------------------------------------------------
  |
  | Komu vsemu se ma posilat email z kontaktniho formulare?
  |
 */
		  
$config['email_to'][] = "sdeleni@myslimnatebe.cz";
$config['email_to_secret'][] = "vaispavel@gmail.com";
//$config['email_to_secret'][] = "bartyzal@cesky-trh-prace.cz";
?>
