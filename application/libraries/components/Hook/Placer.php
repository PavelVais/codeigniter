<?php

namespace Hook;

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 */
class Placer
{

	private $place;
	private $data;
	public $args;
	private $priority;

	const PLACE_COMMON = 'generic';

	static function init($place, $data)
	{
		$p = new Placer( $place, $data );
		return $p;
	}

	public function __construct($data,$place = self::PLACE_COMMON)
	{
		$this->setPlace( $place );
		$this->data = $data;
		$this->priority = 0;
	}

	public function __toString()
	{
		return $this->proceed();
	}

	public function getPriority()
	{
		return $this->priority;
	}

	public function setPriority($priority)
	{
		$this->priority = $priority;
		return $this;
	}

	public function getPlace()
	{
		return $this->place;
	}

	public function getData()
	{
		return $this->data;
	}

	public function setArgs($args)
	{
		$this->args = $args;
		return $this;
	}

	public function setPlace($place)
	{
		$this->place = strtolower( $place == '' ? self::PLACE_COMMON : $place  );
		return $this;
	}

	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}

	public function proceed()
	{
		if ( is_callable( $this->data ) )
		{
			return call_user_func( $this->data, $this->args );
		}
		else
		{
			return $this->data;
		}
	}

}
