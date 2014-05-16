<?php

namespace Benchmark;
/**
 * Simple benchmark timer.
 * @author Pavel Vais <vaispavel@gmail.com>
 * @version 1.0
 */
class Timer
{

	static $timers = array();

	static function start($benchmarkName = '')
	{
		gc_collect_cycles();
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
		$timer->addMark( $label, self::getTime(),memory_get_usage() );
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
			\FB::info( "	$index. " . $mark['diff'] . ' sec. (' . $mark['percent'] . '%) memory usage: '.$mark['mem_diff'].' KB | label: "' . $mark['label'] . '" | total time: ' . $mark['overall'] . ' sec. ' );
			$index++;
		}
		\FB::info( '==========================================' );
		\FB::info( 'IN TOTAL: ' . $result['total'] . ' sec | memory: '.$result['totalmem'].' KB' );
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
	private $firstMemory;
	private $name;

	public function __construct($time, $label = '')
	{
		$this->name = $label;
		$this->firstTime = $time;
		$this->firstMemory = memory_get_usage();
	}

	public function addMark($label, $time,$memory_usage)
	{
		$this->marks[] = array(
			 'label' => $label == '' ? count( $this->marks ) + 1 : $label,
			 'time' => $time,
			 'memory' => $memory_usage
		);
		return $this;
	}

	public function get($time)
	{
		$overall = $time - $this->firstTime;
		$overallmem = memory_get_usage() - $this->firstMemory;
		$lastTime = $this->firstTime;
		$lastMemory = $this->firstMemory;
		foreach ( $this->marks as &$mark )
		{
			$mark['time'] = $this->round( $mark['time']);
			$mark['overall'] = $this->round( $mark['time'] -  $this->firstTime );
			$mark['diff'] = $this->round( $mark['time'] - $lastTime );
			$mark['percent'] = $this->round( 100 * $mark['diff'] / $overall, 2 );
			$mark['mem_diff'] = $this->round( ($mark['memory'] - $lastMemory) / 1024,4 );
			$lastTime = $mark['time'];
			$lastMemory = $mark['memory'];
		}
		return array(
			 'marks' => $this->marks,
			 'total' => $this->round($overall,8),
			 'totalmem' => $this->round($overallmem / 1024,4),
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
