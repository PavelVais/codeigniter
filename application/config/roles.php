<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| ROLES
| -------------------------------------------------------------------
| Toto nastaveni upresnuje chovani jednotlivych roli prirazenych
| k jednotlivym uctum.
|
| -------------------------------------------------------------------
| Instrukce
| -------------------------------------------------------------------
| Nejdrive mame seznam skupin (roli), ke kterym se vstahuji pravidla.
| Jedntolive role muzou od sebe dedit.
|
| Pote se urcuji pravidla, podle kterych se rozhoduje, zdali dana role
| muze vykonat metodu, ktera je v pravidle urcena.
|
*/

/*
| -------------------------------------------------------------------
|  DEFINICE ROLI
| -------------------------------------------------------------------
| Seznam roli, kde klic je vzdy nazev role a hodnota je argument,
| dle ktereho se muze chovani role rozsirovat.
|
| Možné argumenty:
|	zadny argument = role od nikoho nededi, je samovolna.
|	"all" = muze vse, kde neni vyslovene zakazano ze to dana role
|		nesmi vykonat (pomoci prikazu not:: viz nize)
|  "from::<nazev_role>" = role dedi od role, ktera je zde urcena.
|		Muze dochazet i k vicenasobne dedicnosti
|	"others" = teto roli pripadaji vsichni, kteri nespadaji do jine role.
|		Tato role MUSI byt nastavena.
| 
|
*/

$config['roles']['roles'] = array(
	 'administrator'	=> 'all',					//= muze vse
	 'moderator'		=> 'from::verified',		//= dedi od verified
	 'verified'		=> 'from::registered',		//= dedi od registerd
	 'registered'		=> 'from::unregistered',		//= dedi od unregisterd
	 'unregistered'	=> 'others'						//= role pro neregistrovane
);

/*
| -------------------------------------------------------------------
|  DEFINICE PRAVIDEL
| -------------------------------------------------------------------
| Pravidla:
|	Kazde pravidlo se deli na samotne sekce, ke kterym se prirazuje,
|  Kdo muze dane pravidlo splnovat a kdo ne.
|		Pokud chcete vyslovne nektere skupine danou moznost zakazat
|		je mozne napsat string "not::<nazev_role>"
|		
|		Pokud chcete metodu povolit POUZE konkretni skupine,
|		je mozne napsat string "only::<nazev_role>".
|		Tato podminka se vstahuje ke konkretni roli, nededi se!
|
|		Pokud chcete danou vec umoznit absolutne vsem, staci
|		napsat string "all"
|
|		* U negace neplati zakon dedicnosti!
|			Takze pokud je not::registered a moderator od neho dedi,
|			Danou metodu bude mit povolenou.
|
| Příklad:
|	Hlasovat v ankete mohou vsichni krom moderatoru a administratoru,
|	editovat anketu muze jen administrator a reklamu vidi pouze neregistrovany
|
| $config['roles']['rules']['wishes'] = array(
|		'poll' => array("all","not::moderator","not::administrator"),
|		'edit' => array("administrator"),
|		'ads' =´> array('only::unregistered')
|
*/
//= Nastaveni pristupu do administrace
$config['roles']['rules']['administration'] = array(
	 'access' => 'moderator',
);
//= Nastaveni prani
$config['roles']['rules']['wishes'] = array(
	 'post' => 'registered',
	 'edit' => 'administrator',
	 'delete' => 'administrator',
	 'hide'	=> 'moderator',
);

//= Nastaveni komentaru
$config['roles']['rules']['comments'] = array(
	 'delete' => 'moderator'
);

//= Nastaveni databaze
$config['roles']['rules']['db'] = array(
	 'backup' => 'administrator',
	 'renew' => 'administrator'
);

//= Nastaveni profilu
$config['roles']['rules']['users'] = array(
	 'add' => 'all',
	 'edit' => 'registered',
	 'delete' => array('all','not::registered','not::moderator'),
	 'ban'	=> 'moderator'
);

//= Nastaveni moznosti prirazeni jednotlivych roli dle aktualnich roli
$config['roles']['rules']['role_assign'] = array(
	 'administrator' => 'administrator',
	 'moderator' => 'administrator',
	 'verified' => 'moderator',
	 'registered' => 'moderator',
	 'unregistered'	=> 'moderator'
);




/* End of file roles.php */
/* Location: ./application/config/roles.php */