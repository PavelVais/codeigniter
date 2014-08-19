<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| JS INLINE section
| -------------------------------------------------------------------------
| Hodnota TRUE vlastnost zapina (povoluje)
| Hodnota FALSE vlastnost zakazuje
|
*/

/**
 * Pokud neni jinak, automaticky dany JS kod minifikuje
 */
$config['always_minify'] = true;

/**
 * Pokud neni jinak, JS soubor (minifikovany, i normalni) se ulozi po prvnim
 * vygenerovani do cache a dale se jiz nebude ukladat (TRUE), a nebo
 * se bude stale generovat a nebude se ukladat do cache (FALSE)
 * FALSE hodnota je vhodna do development modu
 */
$config['activate_cache'] = true;



/* End of file js.php */
/* Location: ./application/config/js.php */