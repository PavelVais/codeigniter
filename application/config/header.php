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
  | Zakladni nastaveni hlavicky u kazde stranky (ktera dane informace generuje
  | pomoci $this->header->generate('TITULEK STRANKY');
  |--------------------------------------------------------------------------
 */
$config['header']['author'] = 'Pavel Vais';
$config['header']['keywords'] = 'confession, anonymous, sharing, confess, hashtag';
$config['header']['favicon'] = 'favicon.ico';
$config['header']['description'] = 'Make an anonymous confession! The most interesting confessions will be made public on.';
$config['header']['encode'] = 'utf-8'; //= Default
$config['header']['language'] = 'en';  //= Default
$config['header']['doctype'] = 'html5';  //= viz codeigniter guide ('xhtml1-strict' napr.)

/**
 * Spojka, ktera se prida v pripade, ze je uveden title-psotfix
 * a je uvedeny i titulek.
 * Pokud neni uveden titulek, jako title se da POUZE title-postfix 
 */
$config['header']['title-union'] = ' - ';
$config['header']['title-postfix'] = 'MakeConfession.com';  //= Text, který se přidá automaticky za titulek



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
  | 'cover' muze obsahovat argument 'all' nebo se nemusi vubec uvadet,
  | pokud chceme dany objekt aplikovat na jakekoli strance
  |--------------------------------------------------------------------------
  | TYTO PRAVIDLA SE VZTAHUJI KE VSEM MOZNYM PRVKUM
  | ('meta','links','css','js')
  |--------------------------------------------------------------------------
 */
$config['header']['meta'][] = array(
    'name' => 'copyright',
    'content' => '(c) 2013 pavadesign.cz - Pavel Vais',
);


$config['header']['meta'][] = array(
    'name' => 'viewport',
    'content' => 'width=device-width, initial-scale=1.0',
);
/*
  $config['header']['meta'][] = array(
  'property' => 'og:mark',
  'content' => 'blahblah',
  //'restriction' => 'fcb'
  ); */
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
  | );
  | K CSS objektu se pristupuje stejne jako k jinym objektum.
  | Plati zde take pravidla 'cover' a 'except' popripade muzeme tyto omezeni
  | odkazat na objekt 'restriction'
  |
  |--------------------------------------------------------------------------
 */

//$config['header']['css'][] = 'http://fonts.googleapis.com/css?family=Open+Sans:300,400,600';
$config['header']['css'][] = array(
    'url' => 'bootstrap.css',
    'cover' => 'all',
);
$config['header']['css'][] = array(
    'url' => 'mystyle.css',
    'cover' => 'all',
	 'except' => 'administrace'
);
$config['header']['css'][] = array(
    'url' => 'administration.css',
    'cover' => 'administrace',
);
//$config['header']['css'][] = array("jquery.smartwizard.css","jquery.ui.timepicker.css");


/* $config['header']['caching'][] = array(
  'name' => 'mnt_common',
  'compress' => true,
  'version' => "1.0.0",
  'css' => array(
  'css/jquery.confirm.css',
  'css/jquery-ui',
  "css/jquery.smartwizard.css",
  "css/jquery-ui-1.10.0.custom.min.css",
  "css/style.css"
  )
  ); */
/*
  $config['header']['css'][] = array(
  'url' => 'canvas',
  'cover' => 'canvas'
  ); */


/*
  |--------------------------------------------------------------------------
  | JavaScript
  | Stejne vlastnosti jako u CSS
  |--------------------------------------------------------------------------
 */
//$config['header']['js'][] = 'http://cdnjs.cloudflare.com/ajax/libs/gsap/1.9.7/TweenMax.min.js';
$config['header']['js'][] = 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js';
$config['header']['js'][] = 'plugins/jquery.confirm.js';

//$config['header']['js'][] = 'plugins/jquery.superscrollorama.js';
//$config['header']['js'][] = 'plugins/jquery.masonry.min.js';
$config['header']['js'][] = 'ci.js';
$config['header']['js'][] = 'plugins/jquery.charCounter.js';

//$config['header']['js'][] = 'plugins/time/jquery.ui.core.min.js';


//

/*
  $config['header']['caching'][] = array(
  'name' => 'mnt_common',
  'compress' => true,
  'version' => "1.0.0",
  'js' => array(
  'js/plugins/jquery.actual.min.js',
  'js/plugins/jquery.smartWizard-2.0.min.js',
  'js/plugins/jquery.superscrollorama.js',
  'js/plugins/jquery.autocomplete.js',
  'js/plugins/jquery.masonry.min.js',
  'js/ci.js'
  ),

  ); */

/*
  $config['header']['js'][] = 'plugins/time/jquery.ui.position.min.js';
  $config['header']['js'][] = 'plugins/time/jquery.ui.widget.min.js';
  $config['header']['js'][] = 'plugins/time/jquery.ui.tabs.min.js';
  $config['header']['js'][] = 'plugins/jquery.ui.timepicker2.js'; */

/*
  |--------------------------------------------------------------------------
  | Hooky:
  | Diky hookum, muzete volat cele kusy kodu, kdekoli na strance.
  | pole 'name' urcuje nazev hooku (a nazev souboru v view/comp/header/hooks)
  | pole 'func_name' neni povinne, urcuje nazev funkce (viz nize)
  | pole 'cover' , 'except popr. 'restriction' jsou stejne jako jinde
  | pole 'included' znaci, jestli bude hook automaticky vypsan
  |                 v hlavicce. (Defaultně je TRUE)
  | pole 'arguments' muze vlozit argument (popr. array argumenty)
  |                  ktere pak vlozi do view souboru
  |                  ($arg1 az argX kde X je pocet argumentu + nazev hooku)
  | prikaz $this->header->callHeaderHook($nazevHooku); vypise dany hook
  |        kdekoli na strance
  |--------------------------------------------------------------------------
 */
//= Animovane menu
$config['header']['hooks'][] = array(
    'name' => 'html5',
);

$config['header']['hooks'][] = array(
    'name' => 'google_analytics',
);

/*
  |--------------------------------------------------------------------------
  | Cachovani:
  |
  |
  |--------------------------------------------------------------------------
 */
/*
  $config['header']['caching'][] = array(
  'name' => 'mnt_common',
  'compress' => true,
  'version' => "1.0.0",
  'js' => array(
  'js/plugins/jquery.actual.min.js',
  'js/plugins/jquery.smartWizard-2.0.min.js',
  'js/plugins/jquery.superscrollorama.js',
  'js/plugins/jquery.autocomplete.js',
  'js/plugins/jquery.masonry.min.js',
  'js/ci.js'
  ),

  ) */



/*
  |--------------------------------------------------------------------------
  | Restrikce:
  | Pokud vice objektu pouziva stejny 'cover' a 'except', muze se vytvorit
  | restrikce, na kterou pote budou objekty odkazovat pres
  | pole 'restriction'
  |
  | priklad:
  | $config['header']['restriction'][] = array(
  |	'name' => 'google',
  |	'cover' => 'rozcestnik/test',
  |	'except' => 'rozcestnik/test/bb'
  | );
  | pote napriklad objekt hook:
  | $config['header']['hooks'][] = array(
  |	'name' => 'google_analytics',
  |	'func_name' => 'ga',
  |	'restriction' => 'google'
  | );
  | Od te doby ma tento objekt stejny cover a except jako ma tato restrikce
  |--------------------------------------------------------------------------
 */
?>
