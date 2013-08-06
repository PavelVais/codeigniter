<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Trida menu slouzi ke generovani menu polozek. vsechno nastaveni se
 * provadi v config/menu.php
 * Podporuje automaticke urcovani aktualni polozky, sobmenu polozky.
 * Zakomponovana podpora jazykovych souboru.
 * @author Pavel Vais
 * @version 1.1 
 * @changelog
 * 1.1 
 * - pridan class atribut next (aplikuje se po current strance)
 * - pridano vice nastaveni k jazykove slozce
 * -- pridana funkce set_language()
 * - pridany popisky k vetsine promennym
 * - pridana podpora "tagu" pro lepsi identifikaci aktualni stranky
 * - pridana podpora modul segmentu dle ktereho se urci aktualni stranka
 * 
 */
class Menu
{

	/**
	 * Aktualni stranka
	 * @var String
	 */
	private $currentPage = null;

	/**
	 * Balicek obsahujici cele menu
	 * @var array 
	 */
	private $menu_entry = array();

	/**
	 * Balicek obsahujici cele submenu
	 * @var array 
	 */
	private $submenu_entry = array();

	/**
	 * Balicek s jazykovym nastavenim
	 * @var array 
	 */
	private $lang_settings = array();

	/**
	 * Urcuje, zdali se ma aktualni stranka automaticky
	 * rozpoznat.
	 * @var type 
	 */
	private $auto_page = FALSE;

	/**
	 * Zjisti aktualni prvek, podle ktereho pozna,
	 * jaka stranka se ma oznacit jako aktualni
	 * @var String ("name" nebo "url") 
	 * @todo zbavit se tohodle prvku
	 */
	private $currentPageAuto = null;

	/**
	 * Nastaveni obalovaciho wrapperu
	 * @var String 
	 */
	private $html_wrapper = 'menu';

	/**
	 *
	 * @var type 
	 */
	private $currentPageBy = 'url';

	/**
	 * Pokud zde neco je napsane, vypise se v menu jen ty polozky,
	 * ktere maji danou skupinu
	 * @var String
	 */
	private $group;

	/**
	 * Urcuje, kde bude hledat obrazky
	 */

	const IMAGE_FOLDER = "images/";
	/**
	 * Urci prvni pozici pri pridani dodatecne polozky 
	 */
	const FIRST = 'first';

	/**
	 * Urci posledni pozici pri pridani dodatecne polozky
	 * (tato moznsot je defaultni)
	 */
	const LAST = 'last';

	/**
	 * Ma menu oznacit aktualni stranku?
	 * @var Boolean 
	 */
	private $showCurrentPage = TRUE;

	/**
	 * Konstruktor
	 * @param type $currentPage
	 */
	function __construct($currentPage = null)
	{
		$this->ci = & get_instance();

		$this->ci->load->config( 'menu' );
		$this->menu_entry = $this->ci->config->item( 'menu' );
		$this->submenu_entry = $this->ci->config->item( 'submenu' );

		$this->lang_settings = array(
			 "use_lang_file" => $this->ci->config->item( 'menu_use_lang_file' ),
			 "file_name" => $this->ci->config->item( 'menu_lang_file' ),
			 "default_language" => $this->ci->config->item( 'menu_default_language' ),
		);

		$this->auto_page = $this->ci->config->item( 'menu_auto_recognize_actual_page' );
		$this->currentPageBy = strtolower( $this->ci->config->item( 'menu_selected_page_by' ) );
		$this->html_wrapper = $this->ci->config->item( 'html_wrapper' );

		$this->checkOption( $this->lang_settings, array(TRUE, FALSE), 'Menu setting: menu_use_lang_file musí obsahovat pouze hodnoty TRUE nebo FALSE.' );
		$this->checkOption( $this->auto_page, array(TRUE, FALSE), 'Menu setting: menu_auto_recognize_acual_page obsahovat pouze hodnoty TRUE nebo FALSE.' );
		$this->checkOption( $this->currentPageBy, array('url', 'name'), 'Menu setting: menu_selected_page_by obsahovat pouze hodnoty "URL" nebo "NAME".' );
	}

	//= TODO ..vymazat posledni parametr
	public function addLink($url, $name, $last)
	{
		$this->menu_entry[] = array(
			 'url' => $url,
			 'name' => $name,
			 'additional' => $last
		);
	}

	public function setCurrentPage($page)
	{
		$this->currentPage = $page;
	}

	/**
	 * Vypne, nebo zapne vypsani aktualni stranky (defaultne je zapnuto)
	 * @param boolean $boolean 
	 * @deprecated
	 */
	public function ShowCurrentPage($boolean)
	{
		$this->showCurrentPage = $boolean;
	}

	/**
	 * Vygeneruje menu
	 * @param String $currentPage - urci jaka stranka ma byt aktualni
	 */
	public function generate($currentPage = null)
	{
		if ( $this->lang_settings['use_lang_file'] )
			$this->ci->lang->load( $this->lang_settings['file_name'], $this->lang_settings['default_language'] );

		//= Zjisteni aktualni stranky
		if ( $this->auto_page == TRUE )
		{
			$segment = $this->ci->config->item( 'menu_modul_url_segment' );
			$segment = isset( $segment[$this->group] ) ? $segment[$this->group] : 1;

			if ( $this->ci->uri->segment( $segment ) == FALSE && $segment == 1 )
			{
				$this->currentPageAuto = $this->ci->config->item( 'default_controller' );
			}
			else
			{
				$this->currentPageAuto = $this->ci->uri->segment( $segment );
			}
		}


		if ( $currentPage != null )
			$this->setCurrentPage( $currentPage );

		$iterator = array(0, $this->countMenuEntries());

		echo $this->html_wrapper['tag'][0] . "\n";
		echo "\t" . $this->html_wrapper['entries'][0] . "\n";

		$insert_next_class = FALSE; //Po current class vlozi "next" class

		foreach ( $this->menu_entry AS &$menu_entry )
		{

			if ( ($this->group == null && isset( $menu_entry['group'] )) ||
					  (isset( $menu_entry['group'] ) && ($this->group != $menu_entry['group']) ) ||
					  (!isset( $menu_entry['group'] ) && $this->group != null ) )
			{
				continue;
			}
			$iterator[0]++;
			$menu_entry['outher_class'] = '';

			if ( $insert_next_class )
			{
				$insert_next_class = FALSE;
				$menu_entry['outher_class'] .= ' next';
			}

			if ( $this->currentPage != null )
			{

				if ( $menu_entry['name'] == $this->currentPage )
				{
					$menu_entry['outher_class'] .= ' selected';
				}
			}
			else
			if ( $this->currentPage == null && $this->auto_page == TRUE )
			{
				if ( isset( $menu_entry[$this->currentPageBy] ) && $menu_entry[$this->currentPageBy] == $this->currentPageAuto ||
						  ( isset( $menu_entry['tag'] ) && ($this->lang_settings['use_lang_file'] ? $this->ci->lang->line( $menu_entry['tag'] ) : $menu_entry['tag']) == $this->currentPageAuto ) )
				{
					$menu_entry['outher_class'] .= 'selected';
					$insert_next_class = TRUE;
				}
			}

			if ( $iterator[0] == $iterator[1] )
			{
				$menu_entry['outher_class'] .= ' last';
			}
			else if ( $iterator[0] == 1 )
			{
				$menu_entry['outher_class'] .= ' first';
			}

			echo "\t\t" . $this->buildEntry( $menu_entry );

			unset( $menu_entry );
		}

		echo "\t" . $this->html_wrapper['entries'][1] . "\n";
		echo ($this->html_wrapper['tag'][1] . "\n");
	}

	/**
	 * Prida polozku menu ve tvaru <br>
	 * $config['menu'][] = array(
	 * 		'name' => 'Název položky',
	 * 		'url' => 'odkaz na položku'
	 * 	);
	 * @param Array $obj
	 * @param int $position - Menu::LAST, Menu::FIRST, index
	 */
	public function addEntry($obj, $position = self::LAST)
	{
		if ( $position == self::LAST )
		{
			$this->menu_entry[] = $obj;
		}
		else if ( $position == self::FIRST )
		{
			$position = 0;
		}

		array_splice( $this->menu_entry, $position, 0, array($obj) );

		return $this;
	}

	public function set_language($language_name)
	{
		$this->lang_settings['default_language'] = $language_name;
		return $this;
	}

	/**
	 * Vyselektuje do menu pouze ty odkazy, ktere spadaji do
	 * urcene skupiny
	 * @param String $name 
	 */
	public function setGroup($name)
	{
		$this->group = $name;
	}

	/**
	 * Z objektu vygeneruje html prvek - vcetne submenu
	 * @param Array $obj
	 * @param array $iterator
	 * @return String 
	 */
	private function buildEntry($obj)
	{
		$label = $this->lang_settings['use_lang_file'] ? $this->ci->lang->line( $obj['name'] ) : $obj['name'];
		$img = '';

		if ( isset( $obj['img'] ) )
		{
			$img = '<img src="' . site_url( self::IMAGE_FOLDER . $obj['img'] ) . '" ';

			if ( isset( $obj['show_text'] ) )
			{
				$img .= $obj['show_text'] ? '/>' . $label : 'title="' . $label . '" />';
			}
			else
			{
				$img .= " />";
			}
		}

		if ( isset( $obj['url'] ) )
		{
			$a = !$this->ci->config->item( 'insert_image_into_link' ) ? $img : '';

			if ( isset( $obj['class'] ) )
			{
				$a .= $this->htmlPreparator( '<a href="' . site_url( $obj['url'] ) . '">', array('class', $obj['class']) );

				if ( $this->ci->config->item( 'insert_image_into_link' ) )
					$a .= ($img == '' ? $label : $img);
				else
					$a .= $label;
			}
			else
			{
				$a .= '<a href="' . site_url( $obj['url'] ) . '">';
				if ( $this->ci->config->item( 'insert_image_into_link' ) )
					$a .= ($img == '' ? $label : $img);
				else
					$a .= $label;
			}
			$a .= "</a>";
		}
		else
		{
			$a = ($img == '' ? $label : $img);
		}

		//= Existuje pro danou polozku submenu?
		if ( $this->existSubmenu( $obj['name'] ) )
		{

			$obj['outher_class'] = isset( $obj['outher_class'] ) ? $obj['outher_class'] . ' submenu' : 'submenu';
			$sub = $this->htmlPreparator( $this->html_wrapper['entry'][0], array('class' => $obj['outher_class']) ) . $a;
			$sub .= "\n" . $this->html_wrapper['entries'][0];

			//$iterator = array(0, count( $this->submenu_entry[$obj['name']] ));
			foreach ( $this->submenu_entry[$obj['name']] as $submenu_entry )
			{
				//$iterator[0]++;
				$sub .= $this->buildEntry( $submenu_entry );
			}
			return $sub . $this->html_wrapper['entries'][1] . $this->html_wrapper['entry'][1] . "\n";
		}
		return (isset( $obj['outher_class'] ) ? $this->htmlPreparator( $this->html_wrapper['entry'][0], array('class' => $obj['outher_class']) ) : $this->html_wrapper['entry'][0]) . $a . $this->html_wrapper['entry'][1] . "\n";
	}

	/**
	 * Existuje pro dany nazev menu submenu?
	 * @param String $entry_name
	 * @return boolean 
	 */
	private function existSubmenu($entry_name)
	{
		return isset( $this->submenu_entry[$entry_name] );
	}

	/**
	 * Prida do tagu atribut
	 * @param String $tag
	 * @param Array $attr = array('class' => 'xxxx')
	 */
	private function htmlPreparator($tag, $attr)
	{
		if ( substr( $tag, -1 ) == '>' )
		{
			$result = substr( $tag, 0, -1 );

			foreach ( $attr as $k => $v )
			{
				if ( $v != '' )
					$result .= ' ' . $k . '="' . $v . '"';
			}

			return $result . '>';
		}

		show_error( 'Menu class: htmlWrapper - tag neni ukoncen!' );
	}

	/**
	 * Kontroluje, jeslti je jsou zadane spravne parametry, jinak se ukaze error_msg zprava.
	 * @param whatever $option
	 * @param array $arguments - povolene argumeny, ktere muze $option mit
	 * @param type $error_msg
	 * @return boolean true / nebo error hlasku
	 */
	private function checkOption($option, array $arguments, $error_msg)
	{
		foreach ( $arguments as $value )
		{
			if ( $option == $value )
			{
				return true;
			}
		}

		show_error( $error_msg );
	}

	/**
	 * Spocita, kolik polozek se vypise (bere v potaz i skupiny) 
	 */
	public function countMenuEntries()
	{
		$i = 0;
		foreach ( $this->menu_entry as $m )
		{
			$g = !isset( $m['group'] ) ? null : $m['group'];

			if ( $g == $this->group )
				$i++;
		}
		return $i;
	}

}
