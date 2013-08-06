<?php
define( 'IMAGE', 'images/' );
define( 'CSS', 'css/' );
define( 'JS', 'js/' );
/**
 * Linker Helper
 *
 * Vraci spravnou url adresu, paklize ma webove prostredi vice modulu / domen
 *
 * @access        public
 * @param        mixed    variables to be output
 */
function linker($type,$argument)
{
	return site_url('app_' . DOMAIN .'/'. $type . $argument);
}
