<?php

class Html
{

	private static $ci;

	final static function a()
	{
		
	}

	final static function img()
	{
		
	}

	final static function img_path($path)
	{
		
	}

	final static function microdata()
	{
		return new Html_Microdata();
	}

}

/**
 * @author Pavel Vais
 * 
 */
class Html_Microdata
{

	private $url = "http://schema.org/";

	const PERSON = "Person";
	const MOVIE = "Movie";
	const EVENT = "Event";
	const REVIEWS = "Reviews";
	const PRODUCTS = "Products";
	const BUSINESS = "Businesses and organizations";
	const RECIPES = "Recipes";
	const MUSIC = "Music";

	//echo $date->format('\P\TG\Hi\M');
	//date("c", strtotime($post[3]))


	public function open($type = self::EVENT)
	{
		return 'itemscope ' . Html_Builder::attribute( "itemtype",  $this->url.$type  );
	}

	public function item($name)
	{
		return $this->get_itemprop($name);
	}
	/**
	 * Vlozeni microformatu data.
	 * @param type $name - typ micro data.<br>
	 * U Review se urcuje <storng>dtreviewed</strong><br>
	 * U Eventu se urcuje <storng>startDate, endDate</strong><br>
	 * U Recipes se urcuje <strong>prepTime, cookTime, totalTime</strong>
	 * @param DateTime $time - new DateTime("string casu");
	 * @return String
	 */
	public function date($name, DateTime $time)
	{
		return $this->get_itemprop( $name ) . Html_Builder::attribute( "datetime", $time->format( 'c' ) );
	}

	/**
	 * Vraci odpovidajici itemprop pro microformat
	 * @param String $name
	 * @return String
	 */
	private function get_itemprop($name)
	{
		return Html_Builder::attribute('itemprop',$name);
	}

}

class Html_Builder
{
	static function attribute($name, $value = null)
	{
		return $name . ($value === null ? '' : '="' . $value . '"' );
	}
}