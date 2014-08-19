<?php


function loadController()
{
	global $URI;
	if ( $URI->segment( 1 ) == 'api' )
	{
		require_once(APPPATH . 'libraries/customControllers/API_Controller.php');
	}
}
