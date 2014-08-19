<?php

/*
  |--------------------------------------------------------------------------
  | Header nastaveni
  | Paklize je tady vyplneno, pouzije se jako defaultne u kazdeho sestrojeni
  | stranek.
  | Muze se premazat pres 'addParams($data)' popr. pres jednotlive
  | settery.
  |--------------------------------------------------------------------------
 */


/*
  |--------------------------------------------------------------------------
  | Paklize je stranka vicejazycna, musi se i hlavicka nastavovat dle jazyka.
  | Pokud je 'use_lang_file' nastaveno na TRUE, tak description, keywords,
  | language a title-postfix namisto sringu bere odkaz na jazyk.
  | (napr:
  | title-postfix = "Moje Stranka" => title-postfix = "header.titlepostfix" )
  |--------------------------------------------------------------------------
 */
$config['header']['use_lang_file'] = false;
$config['header']['lang_file'] = 'common';


/*
  |--------------------------------------------------------------------------
  | Pokud je 'environment_option' TRUE, nastavuje se chovani
  | dle ENVIRONMENT globalni promenne (v index.php).
  | Pokud je promenna nastavena na development rezim, soubory se
  | neslucuji a neminifikuji.
  | Pokud je nastavena produkcni rezim, vsechny CSS a JS soubory se automaticky
  | sloučí a minifikují.
  | Pokud se něnkterý soubor změní, automaticky se cache překompiluje.
  |--------------------------------------------------------------------------
 */
$config['header']['environment_option'] = true;

/*
  |--------------------------------------------------------------------------
  | Zakladni nastaveni hlavicky u kazde stranky (ktera dane informace generuje
  | pomoci $this->header->generate('TITULEK STRANKY');
  |--------------------------------------------------------------------------
 */
$config['header']['author'] = 'Pavel Vais';
$config['header']['keywords'] = '';
$config['header']['favicon'] = 'favicon.ico';
$config['header']['description'] = '';
$config['header']['encode'] = 'utf-8'; //= Default
$config['header']['language'] = 'en';  //= Default
$config['header']['doctype'] = '<!DOCTYPE html>';  //= viz codeigniter guide ('xhtml1-strict' napr.)

/**
 * Spojka, ktera se prida v pripade, ze je uveden title-psotfix
 * a je uvedeny i titulek.
 * Pokud neni uveden titulek, jako title se da POUZE title-postfix 
 */
$config['header']['title-union'] = ' - ';
$config['header']['title-postfix'] = 'My CI site';  //= Text, který se přidá automaticky za titulek

/**
 * Predpona pro cache CSS a JS
 */
$config['header']['cache-css-prefix'] = 'cacheminifed_';
$config['header']['cache-js-prefix'] = 'cache/cacheminifed_';


/*
  |--------------------------------------------------------------------------
  | Meta znacky:
  |
  | $config['header']['meta'][0] = array(
  |	'property' => 'og:title',	//bud property nebo name
  |	'content' => 'blahblah',
  |	 //= pro jake URL se tento objekt aplikuje? ('all' nebo nic = pro kazdou)
  |	'cover' => array(array('rozcestnik', 1), 'test'),
  |	//= Vyjimka pro jake URL se dany objekt neaplikuje
  |	'except' => 'rozcestnik/test/aa', // Vyjimka z URL adres aplikovanych v coveru
  | );
  |
  | cover i except muze obsahovat array adresy, pokud je pole v poli,
  | tak jako druhy argument udavame, pro jaky segment URL se dana url ma
  | aplikovat, prikl.:
  | 'cover' => array(array('test',2),'rozcestnik/foo')
  | zde udavame, ze url musi obsahovat BUD segment 'test' na pozici 2
  | nebo kdekoli mit url adresu 'rozcestnik/foo
  | priklad prikladu:
  | 1) www.bbb.cz/rozcestnik/boo/test/foo => objekt se NEZOBRAZI
  | 2) www.bbb.cz/rozcestnik/test/boo		 => objekt se ZOBRAZI (viz 'test')
  | 3) www.bbb.cz/rozcestnik/foo/test		 => objekt se ZOBRAZI (viz 'rzc/foo')
  | 4) www.bbb.cz/boo/eee/rozcestnik/foo	=> objekt se ZOBRAZI
  |
  | dalsi priklad:
  | 'cover' => 'foo/bar',
  | 'except' => 'foo/bar/aaa'
  | priklady prikladu:
  | www.aaa.cz/foo/bar => ZOBRAZI se
  | www.aaa.cz/foo/bar/aaa => NEZOBRAZI se
  |
  | 'cover' se nemusi vubec uvadet, pote se objekt vypise na jakekoli strance
  |--------------------------------------------------------------------------
  | TYTO PRAVIDLA SE VZTAHUJI KE VSEM MOZNYM PRVKUM
  | ('meta','view','css','js','string')
  |--------------------------------------------------------------------------
 */
$config['header']['meta'][] = array(
    'name' => 'viewport',
    'content' => 'width=device-width, initial-scale=1.0, maximum-scale=1',
);
$config['header']['meta'][] = array(
    'property' => 'og:site_name',
    'content' => 'YOUR SITE NAME',
);

$config['header']['meta'][] = array(
    'property' => 'og:type',
    'content' => 'YOUR SITE TYPE'
	   // more on https://developers.facebook.com/docs/reference/opengraph
);

$config['header']['meta'][] = array(
    'name' => 'robots',
    'content' => 'all,index,follow'
);
$config['header']['meta'][] = array(
    'name' => 'googlebot',
    'content' => 'index,follow,snippet,archive'
);

/* $config['header']['link'][] = array(
  'url' => 'favicon.ico',
  'rel' => 'shortcut icon',
  'type' => 'image/ico',
  ); */


/*
  |--------------------------------------------------------------------------
  | CSS
  |
  | $config['header']['css'][] = array(
  |	'url' => 'main',
  |   'language' => 'cs',		// Omezeni pro jazyk
  |   'compress' => true,		// [CSS, JS] => minifikace souboru
  |   'version' => true,			// [CSS, JS] => verze cache souboru
  |   'name => 'nazevCacheSouboru',// [CSS, JS] => nazev cache souboru
  |	'deferred' => TRUE,			// [JS] - ma se vypsat az na konec stranky?
  |	'detection' => "isMobile" , "isTablet", "isiOS", "isAndroidOS", "isComputer"
  |					=> pri negaci staci pred to dat "!"
  |  'localhost' => true		// Podminka, vypise se pouze pri localhostu,
  |							// pri false se vypise pouze pokud NENI
 * 							// pristup z localhostu
  | );
  | K CSS objektu se pristupuje stejne jako k jinym objektum.
  | Plati zde take pravidla 'cover' a 'except'
  |
  |--------------------------------------------------------------------------
 */

$config['header']['css'][] = array(
    'url' => array('bootstrap.min.css', 'font-awesome.min.css','bootstrap-update.css'),
    'except' => 'administrace'
);
$config['header']['css'][] = array(
    'url' => 'mystyle.css',
    'except' => 'administrace'
);
$config['header']['css'][] = array(
    'url' => array(
	   'administration/bootstrap.min.css',
	   'bootstrap-update.css',
	   'font-awesome.min.css',
	   'administration/plugins/timeline/timeline.css',
	   'administration/plugins/morris/morris-0.4.3.min.css',
	   'administration/select2/select2.css',
	   'administration/summernote.css',
	   'administration/admin.css',
    ),
    'cover' => array('administrace', 'dev')
);
$config['header']['css'][] = array(
    'url' => array(
	   'administration/login.css',
    ),
    'cover' => 'administrace/login',
);

/**
  | Compressed CSS EXAMPLE
  |--------------------------
  | $config['header']['css'][] = array(
  |	 'url' => array('bootstrap.css',"mystyle.css"),
  |	 'cover' => 'all',
  |	 'compress' => true,
  |	 'name' => 'mystyle',
  |	 'version' => '1.0'
  | );
 */
/*
  |--------------------------------------------------------------------------
  | JavaScript
  | Stejne vlastnosti jako u CSS
  |--------------------------------------------------------------------------
 */
$config['header']['js'][] = array(
    'url' => 'http://code.jquery.com/jquery-1.10.2.min.js',
    'deferred' => true,
    'localhost' => false
);
$config['header']['js'][] = array(
    'url' => 'jquery-1.11.1.min.js',
    'deferred' => true,
    'localhost' => true
);

$config['header']['js'][] = array(
    'url' => 'plugins/jquery.confirm.js',
    'deferred' => true,
    'except' => 'administrace'
);

$config['header']['js'][] = array(
    'url' => array(
	   'administration/bootstrap.min.js',
	   'administration/plugins/metisMenu/jquery.metisMenu.js',
	   'administration/plugins/morris/raphael-2.1.0.min.js',
	   'administration/plugins/morris/morris.js',
	   'administration/summernote.min.js',
	   'administration/select2/select2.min.js',
	   'administration/select2/select2_locale_cs.js',
	   'administration/admin.js'
    ),
    'deferred' => true,
    'cover' => array('dev', 'administrace')
);
$config['header']['js'][] = array(
    'url' => array(
	   'respond.min.js'
    )
);

$config['header']['js'][] = array(
    'url' => array(
	   'formValidation.js',
    ),
    'deferred' => true
);


/*
  |--------------------------------------------------------------------------
  | VIEWS:
  | Diky views, muzete volat cele kusy kodu, ktere jsou ulozeny ve VIEW slozce.
  | Hlavni argumentem musi byt cesta k VIEW souboru.
  |--------------------------------------------------------------------------
 */
//= HTML5 a Respond.js for fuckin IE
$config['header']['view'][] = 'comp/header/html5';
$config['header']['view'][] = 'comp/header/retina';

//= GA
$config['header']['view'][] = 'comp/header/google_analytics';

/*
  |--------------------------------------------------------------------------
  | STRINGS:
  | String je jednoduchy hook, ktery vlozi do hlavicky presne to,
  | co je v nem napsano. Idealni pro netradicni meta tagy, na ktere 
  | nejde aplikovat standartni funkce
  |--------------------------------------------------------------------------
 */

// $config['header']['string'][] = 'string example';