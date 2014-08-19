<?php

/**
 * Hook, ktery se stara o anotace. Jedna se o microformaty
 * @property CI_Router $router Description
 */
class Annotation
{

	private $CI;
	private $annotations;
	private $class;
	private $method;
	private $hooks = array();

	public function __construct()
	{
		$this->CI = & get_instance();
		$this->addHook( 'ajax-only', 'checkAjaxOnly' )
				  ->addHook( 'logged', 'checkLogged' )
				  ->addHook( 'role', 'checkRole' )
				  ->addHook( 'role-allowed', 'checkRoleMethod' );
	}

	/**
	 * Prikaz: @ajax-only
	 * Zabranuje probehnuti funkce, pokud neni ajaxove volani
	 * @return boolean
	 */
	public function checkAjaxOnly()
	{
		if ( !$this->CI->input->is_ajax_request() )
			show_404();
		else
			return true;
	}

	/**
	 * Prikaz: @logged(redirect,negation)
	 * @param type $redirect - pokud neni lognutej, KAM se redirectne?
	 * [default] = homepage
	 * @param type $not - negace? tzn pokud je lognutej, tak se provede redirect
	 * @return boolean
	 */
	public function checkLogged($redirect = null, $not = false)
	{
		if ( User::is_logged_in() == $not )
			redirect( $redirect == null ? '' : $redirect  );
	}

	/**
	 * Prikaz: @role(nazev_role,redirect)
	 * @param type $role
	 * @param type $redirect - pokud neni lognutej, KAM se redirectne?
	 * [default] = homepage
	 */
	public function checkRole($role = 'administration', $redirect = null)
	{
		if ( strtolower( User::get_role() ) == strtolower( $role ) )
			redirect( $redirect == null ? '' : $redirect  );
	}

	public function checkRoleMethod($type, $method, $redirect = null)
	{
		$RM = new Roles();
		if ( !$RM->allowed( $type, $method ) )
			redirect( $redirect == null ? '' : $redirect  );
	}

	//*********************************************************
	//* SYSTEMOVA CAST
	//*********************************************************

	public function check()
	{
		if (!isset($this->CI->router))
			return;
		
		$this->class = $this->CI->router->fetch_class();
		$this->method = $this->CI->router->fetch_method();

		if ( !$this->parseAnnotation() )
			return;
		$this->checkAnnotations();
		//return $annotations[1];
	}

	private function parseAnnotation()
	{
		if ( !method_exists( $this->class, $this->method ) )
			return false;
		$r = new ReflectionClass( $this->class );
		$d = new ReflectionMethod( $this->class, $this->method );

		$doc = $d->getDocComment();

		preg_match_all( '#@(.*?)\n#s', $doc, $annotations );

		if ( empty( $annotations[0] ) )
		//= Neni zapotrebi dal cokoli delat, funkce nema anotace
			return false;

		$this->annotations = $annotations[1];

		foreach ( $this->annotations as &$annotation )
		{
			if ( strpos( $annotation, '(' ) === FALSE )
			{
				$annotation = array(
					 'name' => $annotation,
					 'arg' => null
				);
			}
			else
			{
				preg_match( '#(.*)\((.*)\)#s', $annotation, $result );
				$annotation = array(
					 'name' => $result[1],
					 'arg' => $result[2]
				);
			}
		}
		//FB::info( $this->annotations, 'anotace: ' );
		return true;
	}

	private function checkAnnotations()
	{
		foreach ( $this->hooks as $hook )
		{
			foreach ( $this->annotations as $index => $annotation )
			{
				if ( $hook['name'] == strtolower( $annotation['name'] ) )
				{

					if ( $annotation['arg'] != null )
						$this->callFunction( $hook, $annotation );
					else
						$this->{$hook['function']}();
					unset( $this->annotations[$index] );
				}
			}
		}
	}

	private function callFunction($hook, $annotation)
	{
		if ( $annotation['arg'] != false )
		{
			$annotation['arg'] = explode( ',', $annotation['arg'] );
			foreach ( $annotation['arg'] as &$ann )
			{
				$a = strtoupper( $ann );
				if ( $a == 'TRUE' )
					$ann = true;
				elseif ( $a == 'FALSE' )
					$ann = false;
				elseif ( is_numeric( $ann ) == true )
					$ann = (int) $ann;
			}
		}
		call_user_func_array( array($this, $hook['function']), $annotation['arg'] );
	}

	private function addHook($name, $functionName)
	{
		$this->hooks[] = array(
			 'name' => $name,
			 'function' => $functionName
		);
		return $this;
	}

}

?>
