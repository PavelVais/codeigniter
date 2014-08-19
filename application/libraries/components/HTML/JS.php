<?php

namespace HTML;

/**
 * 
 */
class JS {

	private $name;
	private $fullName;
	private $fullPath;
	private $data;
	private $cacheName = null;
	private $ci;
	public $compileAdvMode;
	private $activate_cache;

	/**
	 * 
	 * @param boolean $minify
	 * null = NEKOMPILOVAT
	 * false = Kompilovat jednou, pokud je cache
	 * true = Kompilovat vzdy (vhodny pro debug)
	 * @param array $data
	 * @param string $name
	 * @param boolean $save_to_cache:
	 * TRUE : cache se ulozi do cache a nadale se nebude aktualizovavat
	 * FALSE : cache bude IGNOROVANA
	 * ale taky aby se ignorovala cache. 
	 */
	static function insert($minify = null, $data = array(), $name = null, $save_to_cache = null, $compile_adv_mode = false) {
		$ci = & get_instance();
		$JS = new JS( $data, $name, $ci );

		$JS->compileAdvMode = $compile_adv_mode;
		$JS->activate_cache = $save_to_cache == null ? $ci->config->item( 'activate_cache', 'js' ) : $save_to_cache;

		if ( !$JS->createTemplate() ) {
			return $JS->get( $minify == null ? $ci->config->item( 'always_minify', 'js' ) : $minify  );
		}
	}

	function __construct($data, $name, &$ci) {

		//$name = $name || $_ci_view;
		$this->ci = $ci;
		$this->data = $data;
		$this->name = $name == null ? 'view_' . strtolower( $this->ci->router->class . '_' . $this->ci->router->method ) : $name;
		$this->fullName = $this->name . '.php';
		$this->fullPath = APPPATH . 'views/js/' . $this->fullName;
		$this->ci->load->helper( 'file' );
		$this->ci->config->load( 'js', true );
	}

	/**
	 * Vytvori sablonu pro nove JS kody a ulozi do view/js
	 * @return boolean
	 */
	public function createTemplate() {
		if ( file_exists( $this->fullPath ) ) {
			return false;
		}

		$template = "<script>\n\n</script>";
		write_file( $this->fullPath, $template );
		return true;
	}

	public function get($minifyCode = false) {

		if ( !$this->activate_cache || file_exists( $this->getCachePath() ) === FALSE || \Cache::compareFiles( 'js/' . $this->fullName, $this->getCachePath() ) ) {
			$result = $this->removeScriptTag( $this->ci->load->view( 'js/' . $this->fullName, $this->data, true ) );

			if ( $minifyCode ) {
				$result = $this->codeCompile( $result );
			}

			try
			{
				$this->saveJSCode( $result );
			}
			catch (Exception $exc)
			{
				throw new $exc;
			}
		}
		\Head\Head2::addJS( $this->getCachePath(true) );
		/*if ( \Head\Head2::$state === 1) {
			
			return null;
		}
		else
			return $this->buildScriptTag( $this->getCachePath() );*/
	}

	/**
	 * Vytvori script tag s adresou js souboru
	 * @param string $path
	 * @return string
	 */
	private function buildScriptTag($path) {
		return Element::open( 'script' )->addAttribute( 'src', base_url( $path ) )->generate();
	}

	/**
	 * Ulozi soubor s javascriptem do js slozky (js/cache defaultne)
	 * @param type $script
	 * @throws \Exception
	 */
	public function saveJSCode($script) {
		if ( write_file( $this->getCachePath(), $script ) == FALSE ) {
			throw new \Exception( "Nelze zapsat JS soubor" );
		}
	}

	/**
	 * Ze scriptu odstrani script tagy
	 * @param string $script
	 * @return string
	 */
	private function removeScriptTag($script) {
		$script = rtrim( $script );
		$l = strlen( '<script>' );
		$a = substr( $script, 0, $l );

		if ( strtolower( $a ) == '<script>' ) {
			$script = substr( $script, $l );
		}
		$l = strlen( '</script>' );
		$a = substr( $script, -1 * $l );
		if ( strtolower( $a ) == '</script>' ) {
			$script = substr( $script, 0, - 1 * $l );
		}
		return $script;
	}

	public function codeCompile($script) {
		$JSShrink = new \Minify\JSShrink();

		$result = $JSShrink->compile( $script, $this->compileAdvMode );
		if ( strlen( $result ) == 1 ) {
			show_error( 'Javascript ' . $this->fullName . ' nemuže být zkompilovan!' );
		}
		return $result;
	}

	public function getCachePath($withoutJSFolder = false) {
		$name = $this->name;
		$data = $this->data;
		if ( is_null( $this->cacheName ) ) {
			$n = '';
			if ( !empty( $data ) ) {
				$n .= json_encode( $data );
			}

			$n .= $name;
			$this->cacheName = md5( $n );
		}
		return ($withoutJSFolder ? '' : 'js/'). 'cache/' . $this->cacheName . '.js';
	}

}
