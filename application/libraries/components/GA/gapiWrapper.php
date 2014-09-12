<?php

/**
 * 
 */
class gapiWrapper
{

	/** @var Cache */
	private $cache;

	/** @var gapi */
	private $gapi;
	private $range;
	private $filter;
	private $dimension;
	private $metric;
	private $reportID;
	private $sort;
	
	private $login;
	private $password;

	public function __construct($reportID,$login,$password)
	{
		$this->cache = new Cache;
		$this->setRange(date('Y-m-d',strtotime("-1 month")), date('Y-m-d'));
		$this->sort('date');
		$this->reportID = $reportID;
		$this->login = $login;
		$this->password = $password;
	}

	/**
	 * format: YYYY-MM-DD 
	 * @param type $from
	 * @param type $to
	 */
	public function setRange($from, $to)
	{
		$this->range = array($from, $to);
		return $this;
	}

	public function filter($filter)
	{

		$this->filter = $filter;
	}

	public function dimension($dimension)
	{
		if ( !is_array( $dimension ) )
		{
			$dimension = array($dimension);
		}
		$this->dimension = $dimension;
		return $this;
	}

	public function metric($metric)
	{
		if ( !is_array( $metric ) )
		{
			$metric = array($metric);
		}
		$this->metric = $metric;
		return $this;
	}
	
	public function sort($sort)
	{
		if ( !is_array( $sort ) )
		{
			$sort = array($sort);
		}
		$this->sort = $sort;
		return $this;
	}

	public function get()
	{
		if ( ($this->gapi = $this->cache->get( $this->getCachePath() )) == FALSE )
		{
			$this->gapi = new gapi( $this->login, $this->password );
			$this->gapi->requestReportData( $this->reportID, $this->dimension, $this->metric, $this->sort, $this->filter, $this->range[0], $this->range[1] );
			$this->saveCache( $this->gapi );
		}
		return $this->gapi;
	}

	public function saveCache($result)
	{
		$this->cache->write( $result, $this->getCachePath(), 60 * 60 * 24 );
	}

	private function getCachePath()
	{
		$path = 'GA/';
		return $path . md5( $this->reportID . implode( '', $this->metric ) . implode( '', $this->dimension ) . $this->filter . $this->range[0] . $this->range[1] );
	}

}
