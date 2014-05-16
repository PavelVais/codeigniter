<?php

namespace api;

/**
 * Description of Authenticator
 *
 * @author Pavel Vais
 */
class Authenticator
{

	public function __construct($login,$passToken)
	{
		
		$expectedToken = 'hjeopjeojreop';
		
	}
	
	public function compareTokens($firstToken,$secondToken)
	{
		return $firstToken == $secondToken;
	}
	
	public function parsePassToken($passToken)
	{
		
	}

}
