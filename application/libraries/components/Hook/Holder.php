<?php

namespace Hook;

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 */
class Holder
{

	private $list;
	private $places;
	private $currentPlace;
	private $loaded;

	public function __construct()
	{
		$this->list = array();
		$this->places = array();
		$this->loaded = false;
		$this->currentPlace = Placer::PLACE_COMMON;
	}

	public function addHook(Placer $placer)
	{
		$this->list[] = $placer;
		return $this;
	}

	/**
	 * Nastavi misto, pro ktery ma vracet hooky
	 * @param string $place
	 * @return \Hook\Holder
	 */
	public function setPlace($place)
	{
		$this->currentPlace = strtolower( $place );
		return $this;
	}

	/**
	 * Vrati hook, pokud jiz zadny neni, vrati NULL
	 * @return Placer
	 */
	public function getHook()
	{
		$this->parsePlaces();

		return $this->next();
	}

	/**
	 * Vrati (a vyjme) dalsi prvek z listu
	 * @return null
	 */
	private function next()
	{
		if ( count( $this->places[$this->currentPlace] ) > 0 )
		{
			return array_shift( $this->places[$this->currentPlace] );
		}
		//unset( $this->places[$this->currentPlace] );
		return null;
	}

	/**
	 * Nahodne zamicha hooky v dane kategorii s tim, ze 
	 * prihlizi na jejich prioritu (nejvetsi priorita bude z nejvetsiho hlediska
	 * prvni
	 * @return boolean - vraci FALSE pri nenaleznuti hooku
	 */
	public function shuffleByPriority()
	{
		$this->parsePlaces();

		if ( $this->isPlaceExists() )
		{
			usort( $this->places[$this->currentPlace], function($a, $b)
			{
				$p1 = $a->getPriority();
				$p2 = $b->getPriority();
				return $p1 + rand( 0, 100 ) < $p2 + rand( 0, 100 );
			} );

			return true;
		}
		else
			return false;
	}

	/**
	 * Seradi hooky dle priority
	 * @return boolean
	 */
	public function orderByPriority()
	{
		$this->parsePlaces();

		if ( $this->isPlaceExists() )
		{
			usort( $this->places[$this->currentPlace], function($a, $b)
			{
				$p1 = $a->getPriority();
				$p2 = $b->getPriority();
				return $p1 < $p2;
			} );

			return true;
		}
		else
			return false;
	}

	/**
	 * Zamicha nahodne hooka. Nebere v potaz prioritu
	 * @return boolean
	 */
	public function shuffle()
	{
		$this->parsePlaces();

		if ( $this->isPlaceExists() )
		{
			shuffle( $this->places[$this->currentPlace] );
			return true;
		}
		else
			return false;
	}

	/**
	 * Rozdeli hook do jednotlivych mist.
	 * Mozne zavolat jen jednou
	 * @return \Hook\Holder
	 */
	private function parsePlaces()
	{
		if ( !$this->loaded )
		{
			foreach ( $this->list as $k => $placer )
			{
				$place = $placer->getPlace();
				if ( !$this->isPlaceExists($place) )
				{
					$this->places[$place] = array();
				}
				$this->places[$place][] = $placer;
				unset( $this->list[$k] );
			}
			$this->loaded = true;
		}
		return $this;
	}

	private function isPlaceExists($place = null)
	{
		$place = $place == null ? $this->currentPlace : $place;
		return isset( $this->places[$place] );
	}

}
