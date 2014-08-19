<?php

/*
  |--------------------------------------------------------------------------
  | Errors Config
  |--------------------------------------------------------------------------
 */

/*
  |--------------------------------------------------------------------------
  | Zakladní nastavení vypisování chyb
  |
  | 'errors2db' : Pokud je db k dispozici a existuje tabulka "errors",
  | Pote ukladá chybové hlášky do databáze
  |
  | 'db_omit_user_id' : Pakliže je uživatel přihlášen, tak je možné zakázat 
  | pro dané ID ukládání chyb do db.
  | Hodnoty: FALSE - žádné ID nebude akceptováno, 
  |		   integer:  - číslo userova ID
  |		   array: Více ID čísel
  |
  | 'show_detail_user_id' : Pakliže je určeno číslo (nebo pole čísel),
  | tak pro dané IDčka userů se místo produkčních chyb budou ukazovat detailní
  | dev chyby.
  |
  |--------------------------------------------------------------------------
 */
$config['errors_2_db'] = true;

$config['db_omit_user_id'] = 1;

$config['show_detail_user_id'] = 1;



