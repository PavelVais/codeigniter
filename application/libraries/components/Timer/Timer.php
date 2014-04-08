<?php
class Timer
{
	static $start;
	static $marks;
	static function start()
	{
		
	}
	
	static function stop()
	{
		
	}
	
	var $start;
	var $pause_time;
	private $marks;

	/*  start the timer  */

	function timer($start = 0)
	{
		$this->marks = array();
		if ( $start )
		{
			$this->start();
		}
	}

	/*  start the timer  */

	function start()
	{
		$this->start = $this->get_time();
		$this->pause_time = 0;
	}

	function mark($label = '')
	{
		$this->marks[] = array(
			 'label' => $label == '' ? count( $this->marks ) : $label,
			 'time' => $this->get()
		);
	}

	/*  pause the timer  */

	function pause()
	{
		$this->pause_time = $this->get_time();
	}

	/*  unpause the timer  */

	function unpause()
	{
		$this->start += ($this->get_time() - $this->pause_time);
		$this->pause_time = 0;
	}

	/*  get the current timer value  */

	function get($decimals = 8)
	{
		return round( ($this->get_time() - $this->start ), $decimals );
	}

	/*  format the time in seconds  */

	function get_time()
	{
		list($usec, $sec) = explode( ' ', microtime() );
		return ((float) $usec + (float) $sec);
	}

	function result()
	{
		FB::group( 'benchmark' );
		FB::info( 'marks:' );

		if ( empty( $this->marks ) )
		{
			FB::info( '	no marks.' );
		}

		$beforeTime = 0;
		foreach ( $this->marks as $mark )
		{
			FB::info( '		[' . $mark['label'] . '] => ' . $mark['time'] . ' sec. ' . "(diff: " . round( $mark['time'] - $beforeTime, 8 ) . " sec.)" );
			$beforeTime = $mark['time'];
		}
		FB::info( 'IN TOTAL: ' . $this->get() . ' sec' );
		FB::groupEnd();
	}

}