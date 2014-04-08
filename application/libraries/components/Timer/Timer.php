<?php

namespace Benchmark;

class Timer
{

	static $timers = array();

	static function start($benchmarkName = '')
	{
		self::$timers[] = new TimeHolder( self::getTime(), $benchmarkName );
	}

	static function stop($benchmarkName = '')
	{
		$timer = & self::getTimer( $benchmarkName );
		$result = $timer->get( self::getTime() );
		self::FBPrint( $result );
	}

	static function mark($label, $benchmarkName = '')
	{
		$timer = & self::getTimer( $benchmarkName );
		$timer->addMark( $label, self::getTime() );
	}

	private static function FBPrint($result)
	{
		\FB::group( 'benchmark ' . $result['name'] );
		\FB::info( 'marks:' );

		if ( empty( $result['marks'] ) )
		{
			\FB::info( "	no marks." );
		}
		$index = 1;
		foreach ( $result['marks'] as $mark )
		{
			\FB::info( "	$index. " . $mark['diff'] . ' sec. (' . $mark['percent'] . '%) label: "' . $mark['label'] . '" | total time: ' . $mark['overall'] . ' sec.' );
			$index++;
		}
		\FB::info( '==========================================' );
		\FB::info( 'IN TOTAL: ' . $result['total'] . ' sec' );
		\FB::groupEnd();
	}

	static function reset()
	{
		static $timers = array();
	}

	/**
	 * @param string $name
	 * @return TimeHolder
	 */
	private static function getTimer($name)
	{
		if ( $name == '' )
			return (self::$timers[0]);

		foreach ( self::$timers as &$timer )
		{
			if ( (string) $timer == $name )
				return $timer;
		}
	}

	private static function getTime()
	{
		list($usec, $sec) = explode( ' ', microtime() );
		return ((float) $usec + (float) $sec);
	}

}

class TimeHolder
{

	private $marks = array();
	private $firstTime;
	private $name;

	public function __construct($time, $label = '')
	{
		$this->name = $label;
		$this->firstTime = $time;
	}

	public function addMark($label, $time)
	{
		$this->marks[] = array(
			 'label' => $label == '' ? count( $this->marks ) + 1 : $label,
			 'time' => $time
		);
		return $this;
	}

	public function get($time)
	{
		$overall = $time - $this->firstTime;
		$lastTime = $this->firstTime;

		foreach ( $this->marks as &$mark )
		{
			$mark['time'] = $this->round( $mark['time']);
			$mark['overall'] = $this->round( $mark['time'] -  $this->firstTime );
			$mark['diff'] = $this->round( $mark['time'] - $lastTime );
			$mark['percent'] = $this->round( 100 * $mark['diff'] / $overall, 2 );
			$lastTime = $mark['time'];
		}
		return array(
			 'marks' => $this->marks,
			 'total' => $this->round($overall,8),
			 'firstTime' => $this->firstTime,
			 'endTime' => $time,
			 'name' => $this->name
		);
	}

	public function round($number, $decimal = 8)
	{
		if ( $decimal > 5 )
			return sprintf( '%f', round( $number, $decimal ) );
		else
			return round( $number, $decimal );
	}

	public function __toString()
	{
		return $this->name;
	}

}
