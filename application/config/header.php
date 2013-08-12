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
$config['header']['keywords'] = '';
$config['header']['favicon'] = 'favicon.ico';
$config['header']['description'] = '';
$config['header']['encode'] = 'utf-8'; //= Default
$config['header']['language'] = 'en';  //= Default
$config['header']['doctype'] = 'html5';  //= viz codeigniter guide ('xhtml1-strict' napr.)

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
  | 'cover' muze obsahovat argument 'all' nebo se nemusi vubec uvadet,
  | pokud chceme dany objekt aplikovat na jakekoli strance
  |--------------------------------------------------------------------------
  | TYTO PRAVIDLA SE VZTAHUJI KE VSEM MOZNYM PRVKUM
  | ('meta','view','css','js','string')
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
//$config['header']['js'][] = 'http://cdnjs.cloudflare.com/ajax/libs/gsap/1.9.7/TweenMax.min.js';
$config['header']['js'][] = 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js';
$config['header']['js'][] = 'plugins/jquery.confirm.js';
$config['header']['js'][] = 'ci.js';

/*
  |--------------------------------------------------------------------------
  | VIEWS:
  | Diky views, muzete volat cele kusy kodu, ktere jsou ulozeny ve VIEW slozce.
  | Hlavni argumentem musi byt cesta k VIEW souboru.
  |--------------------------------------------------------------------------
 */
//= HTML5 for fuckin IE
$config['header']['view'][] = 'comp/header/html5';

//= GA
$config['header']['view'][] = 'comp/header/google_analytics';

/*
  |--------------------------------------------------------------------------
  | STRINGS:
  | Diky stringum muzete do hlavicky postovat presne zneni kodu, ktere sem
  | napisete. 
  | 
  |--------------------------------------------------------------------------
 */

// $config['header']['string'][] = 'string example';
?>
