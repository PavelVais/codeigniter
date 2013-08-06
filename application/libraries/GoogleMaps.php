<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * Trida GoogleMaps umoznuje jednoduse pridat na stranku mapy od firmy Google
 * nezapomenout otevrit body pomoci <body onload="initialize()"> !!!!
 * @version 1.0
 * @author Pavel Vais
 */
class GoogleMaps {
	
	/**
	 * Mozne argumenty pro typ mapy:
	 * ROADMAP - displays the normal, default 2D tiles of Google Maps.
	 * SATELLITE - displays photographic tiles.
	 * HYBRID - displays a mix of photographic tiles and a tile layer for prominent features (roads, city names).
	 * TERRAIN - displays physical relief tiles for displaying elevation and water features (mountains, rivers, etc.).
	 * @var string
	 */
	private $mapType = "ROADMAP";
	
	/**
	 * Pro jaky ID element na html strance se bude vstahovat dana mapa
	 * default: map_canvas
	 * @var string
	 */
	private $htmlID = "goelocation";
		
	/**
	 * Zoom mapy (0 - 18?) 
	 * @var int
	 */
	private $zoom = 15;
	
	/**
	 * Souradnice vycentrovani mapy
	 * @var array - [0] = Latitude, [1] = Longitude
	 */
	private $coord = array();
	
	/**
	 * Pole znacek v mapach
	 * @var GoogleMapsMark
	 */
	private $markers = array();
	
	/**
	 * Slozka ve ktere jsou ostatni komponenty googlemaps knihovny (view soubory)
	 * @var string
	 */
	private $viewFolder = "comp/googlemaps/";
	
	/**
	 * Vypise javascript pro mapu.
	 * Vkladejte do <head> casti
	 */
	public function printMap()
	{
		$data['zoom'] = $this->zoom;
		$data['coord'] = $this->coord;
		$data['id'] = $this->htmlID;
		$data['map'] = $this->mapType;
		
			//= Kontrola typu mapy
		switch ($data['map'])
		{
			case 'ROADMAP':
			case 'SATELLITE':
			case 'HYBRID':
			case 'TERRAIN':
			break;
			default:
				$data['map'] = "ROADMAP";
			break;
		}
		
		$CI =& get_instance();
		$CI->load->view($this->viewFolder.'js',$data);
		
		if (count($this->markers) > 0)
		{
			foreach ($this->markers AS $mark)
			{
				$CI->load->view($this->viewFolder.'mark',$mark->getAttributes());
			}
		}
		
		echo "};\n</script>\n";
	} 
	
	/**
	 * Zadani souradnic.
	 * @param real $lat
	 * @param real $long
	 */
	public function setCoords($lat,$long)
	{
		$this->coord = array($lat,$long);
	}
	
	/**
	 * Zadani zoomu
	 * @param int $number (0-18)
	 */
	public function zoom($number)
	{
		$this->zoom = $number;
	}
	
	/**
	 * Zmeni typ mapy na neco jineho
	 * Mozne argumenty pro typ mapy:
	 * ROADMAP - displays the normal, default 2D tiles of Google Maps.<br/>
	 * SATELLITE - displays photographic tiles.<br/>
	 * HYBRID - displays a mix of photographic tiles and a tile layer for prominent features (roads, city names).<br/>
	 * TERRAIN - displays physical relief tiles for displaying elevation and water features (mountains, rivers, etc.).<br/>
	 * @param string $mapType
	 */
	public function mapType($mapType)
	{
		$this->mapType = $mapType;
	}
	
	/**
	 * Prida znacku do mapy
	 * @param string $name
	 * @param string $title
	 * @param real array $coord
	 */
	public function addMarker($name,$title,$coord,$description = null)
	{
		$this->markers[] = new GoogleMapsMark($name, $title, $coord, $description);
	}
} 
/**
 *
 * Trida manipulujici se znackami v GoogleMaps
 * @author Pavel Vais
 * @version 1.0
 *
 */
class GoogleMapsMark {
	/**
	 * Slozka ve ktere jsou ostatni komponenty googlemaps knihovny (view soubory)
	 * @var string
	 */
	private $viewFolder = "googlemaps/"; 
	
	/**
	 * Souradnice vycentrovani mapy
	 * @var array - [0] = Latitude, [1] = Longitude
	 */
	private $coord = array();
	
	/**
	 * Nazev znacky 
	 * @var string
	 */
	private $name;
	
	/**
	 * Kratky popisek znacky
	 * @var string
	 */
	private $title;
	
	/**
	 * Dlouhy popisek (umoznuje html tagy)
	 * Pokud description neni urcen, znacka nebude klikatelna a neukaze se zadny popis (krom 'title')
	 * @var string
	 */
	private $description = null;
	
	/**
	 * 
	 * Konstruktor trid GoogleMapsMark
	 * @param string $name
	 * @param string $title
	 * @param real array $coord
	 */
	function __construct($name,$title,$coord, $description = null)
	{
		$this->coord = $coord;
		$this->name = $name;
		$this->title = $title;
		
		if (!is_null($description)) $this->description = $description;
	}

	/**
	 * Vrati vsechny informace o znacce
	 */
	public function getAttributes()
	{
		return array('name' => $this->name, 'title' => $this->title, 'coord' => $this->coord, 'description' => $this->description);
	}
}