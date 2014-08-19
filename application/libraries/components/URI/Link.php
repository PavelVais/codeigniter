<?php
	namespace URI;

/**
 * Description of URI
 *
 * @author Pavel Vais
 * 
 * @examples
 * URI::build('administration/article/add',array(12,8,'overwrite')); 
 * => http://.......administration/article/add/12/8/overwrite
 * URI::anchor('administration/article/add',array(12,8,'overwrite'),send); TODO?
 * => <a href="http...administration/article/add/...." >send</a>
 * URI::URL(!)
 * => stejny modul, v jakym se prave nachazime (administrace atp...)
 * URI::URL(:www.example.com)
 * => prefix ':' vzdy vede na externi url
 */
class Link
{
	static function URL($path,$arguments = null)
	{
		$url = new URL($path,$arguments);
		return $url->get();
	}
	
	
	
	
}




