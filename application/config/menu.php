<?php

/*
  |--------------------------------------------------------------------------
  | Menu detaily
  | [x], kde x je cislo = urcuje poradi v menu
  | ['name'] = index odkazujici na aktualni jazyk
  | ['url'] = odkaz dane polozky
  | [x][x] = urcuje submenu pro danou polozku
  |--------------------------------------------------------------------------
 */

/*
  |--------------------------------------------------------------------------
  | Pokud "use_lang_file" je false, Polozka "name" v jednotlivych menu polozkach
  | se bere jako nazev, ne jako odkaz do lang souboru.
  |  pokud je "use_lang_file false, nelze pouzit multijazycny system!
  |
  | "menu_lang_file" - urcuje nazev lang souboru. (vyuziva se pouze v pripade,
  | ze se menu_use_lang_file je TRUE
  | POZOR: nazev souboru piste bez koncovky "_lang" ! (CI pravidla)
  |
  | "menu_default_language" - zakladni jazykova znacka.
  |--------------------------------------------------------------------------
 */
$config['menu_use_lang_file'] = TRUE;
$config['menu_lang_file'] = "menu";
$config['menu_default_language'] = "czech";

/*
  |--------------------------------------------------------------------------
  | HTML wrapper, diky kteremu se vygeneruje cele menu
  |--------------------------------------------------------------------------
 */
$config['html_wrapper']['tag'] = array('<nav id="hlavni">','</nav>');
$config['html_wrapper']['entries'] = array('<ul>','</ul>');
$config['html_wrapper']['entry'] = array('<li>','</li>');

/*
  |--------------------------------------------------------------------------
  | Pokud je TRUE, tak <img> prvek vlozi do <a> prvku.
  | v druhem pripade bude struktura vypadat takto:
  | <li>
  |	<img>
  |	<a>
  | </li>
  |--------------------------------------------------------------------------
 */
$config['insert_image_into_link'] = FALSE;

/*
  |--------------------------------------------------------------------------
  | Toto nastaveni urci, podle ktereho parametru (NAME, URL) se pozna,
  | ze je dana stranka aktualni
  |--------------------------------------------------------------------------
 */
$config['menu_selected_page_by'] = 'URL';

/*
  |--------------------------------------------------------------------------
  | Zkusi automaticky podle url rozpoznat, jaka stranka je aktualni.
  | Pokud je natvrdo stranka zadana, toto nastaveni nema zadny vliv.
  |--------------------------------------------------------------------------
 */
$config['menu_auto_recognize_actual_page'] = TRUE;

/*
  |--------------------------------------------------------------------------
  | Zpresni automatickeho zjistovani url adresy pro oznaceni aktualni
  | stranky.
  | Nastavte zde stejny controller, ktery mate v config/routes.php
  |--------------------------------------------------------------------------
 */
$config['default_controller'] = 'homepage';

/*
  |--------------------------------------------------------------------------
  | Pri modulu url adresa nabyva o jeden prvek navic.
  | Zde muzeme urcit, pro jakou skupinu menu polozek pouzit segment url adresy
  |
  | DEFAULT je vzdy prvni segment.
  |--------------------------------------------------------------------------
 */
$config['menu_modul_url_segment']['administrace-hlavni'] = 2;


/*
  |--------------------------------------------------------------------------
  | Menu
  | generuji se dle poradi, jake je zde urcene
  | argumenty:
  |	'name' => nazev odkazujici na lang / popripade rovnou nazev polozky
  |	'url' => odkaz. Pokud neni urcen, odkaz se stane neklikatelny
  |	'group' => urci skupinu, ktera se pak vygeneruje, paklize se skupina
  |		urci natvrdo pres $this->menu->setGroup('nazev skupiny');
  |	'img' => nazev obrazku ktery se prida pred text
  |	'show_text => (TRUE (default) / FALSE) - ma se text zobrazit?
  |   'tag' => upresnuje nazev pro identifikaci aktualni stranky. Vstahuje se na
  |		nej "menu_use_lang_file" pravidlo
  |
  | polozka do menu se muze pridat pres $this->menu->addEntry(array()...,$arg);
  |	kde $arg muze nebyvat hodnot Menu::LAST (polozka se zaradi na konec),
  |	Menu::FIRST (polozka se zaradi na zacatek) a nebo cislo, ktere znaci index
  |	kam se polozkazaradi
  |
  |--------------------------------------------------------------------------
 */

$config['menu'][] = array(
	 'name' => 'Úvod',
	 'url' => 'homepage'
);
$config['menu'][] = array(
	 'name' => 'Kontakt',
	 'url' => 'kontakt'
);
$config['menu'][] = array(
	 'name' => 'Tým',
	 'url' => "tym"
);

//= Administrace
$config['menu'][] = array(
	 'name' => 'menu_administration_dashboard',
	 'tag' => 'mmenu_administration_dashboard_tag',
	 'url' => 'administrace',
	 'img' => 'adm/menu/dashboard.png',
	 'group' => 'administrace-hlavni'
);
$config['menu'][] = array(
	 'name' => 'menu_administration_accounts',
	 'tag' => 'menu_administration_accounts_tag',
	 'url' => 'administrace/uzivatele',
	 'img' => 'adm/menu/ucty.png',
	 'group' => 'administrace-hlavni'
);
$config['menu'][] = array(
	 'name' => 'menu_administration_wishes',
	 'tag' => 'menu_administration_wishes_tag',
	 'url' => 'administrace/prani',
	 'img' => 'adm/menu/prani.png',
	 'group' => 'administrace-hlavni'
);
$config['menu'][] = array(
	 'name' => 'menu_administration_settings',
	 'url' => 'administrace/nastaveni',
	 'img' => 'adm/menu/nastaveni.png',
	 'group' => 'administrace-hlavni'
);
$config['menu'][] = array(
	 'name' => 'menu_administration_logs',
	 'url' => 'administrace/logy',
	 'img' => 'adm/menu/logy.png',
	 'group' => 'administrace-hlavni'
);
$config['menu'][] = array(
	 'name' => 'menu_administration_database',
	 'url' => 'administrace/databaze',
	 'img' => 'adm/menu/databaze.png',
	 'group' => 'administrace-hlavni'
);
/*
  |--------------------------------------------------------------------------
  | Submenu
  | $config['submenu'][name polozka menu ke ktere se ma submenu zaradit]
  |
  |--------------------------------------------------------------------------
 */

/*$config['submenu']['menu_o_praze'][] = array(
	 'name'	=> 'submenu_o_praze_pamatky',
	 'url'	=> 'prague/monuments',
	 'img'	=> 'monumentsIco.png'
);
$config['submenu']['menu_o_praze'][] = array(
	 'name'	=> 'submenu_o_praze_kultura',
	 'url'	=> 'prague/culture',
	 'img'	=> 'divadloIco.png'
);
$config['submenu']['menu_o_praze'][] = array(
	 'name'	=> 'submenu_o_praze_restaurace',
	 'url'	=> 'prague/restaurants',
	 'img'	=> 'restaurantIco.png'
);
$config['submenu']['menu_o_praze'][] = array(
	 'name'	=> 'submenu_o_praze_priroda',
	 'url'	=> 'prague/nature',
	 'img'	=> 'natureIco.png'
);
//= Admin sekce
$config['submenu']['menu_admin_apartmany'][] = array(
	 'name'	=> 'submenu_admin_apartmany_budova_add',
	 'url'	=> 'prague/nature'
);
$config['submenu']['menu_admin_apartmany'][] = array(
	 'name'	=> 'submenu_admin_apartmany_add',
	 'url'	=> 'prague/nature'
);
$config['submenu']['menu_admin_apartmany'][] = array(
	 'name'	=> 'submenu_admin_apartmany_edit',
	 'url'	=> 'prague/nature'
);*/


