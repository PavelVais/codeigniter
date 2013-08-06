<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Rozsirena verze kalendare
 * @version 1.0
 * @author Pavel Vais
 */
class MY_Calendar
{
	/**
	 * Promenna nesouci v sobe zaznam o tom, jaky mesic
	 * se ma vykreslit.
	 * @var int 
	 */
	private $month;
	
	/**
	 * Urcuje, pro jaky rok se ma kalendar vykreslit
	 * @var int 
	 */
	private $year;
	
	/**
	 * Array objekt, ktery v sobe nese informace ke dnum,
	 * ke kterym uzivatel neco vlozil pres
	 * funkci insert_data($den,$data)
	 * @var array 
	 */
	private $cell_data;
	
	/**
	 * Interni nastaveni kalendare
	 * Obsahuje prvky:
	 * ['show_today']
	 * ['show_day_names']
	 * ['show_short_days']
	 * @var array 
	 */
	private $pref;
	
	/**
	 * Nese v sobe informace, jak ma pojmenovavat
	 * jednotlive prvky (jake css tridy k nim
	 * ma priradit)
	 * @var array
	 */
	private $class;

	public function __construct()
	{
		$this->class = array(
			 'week' => 'week',
			 'day' => 'day',
			 'month' => 'month',
			 'today' => 'today',
			 'unused' => 'unused',
			 'table' => 'calendar',
			 'old' => 'old',
			 'next' => 'next'
		);


		$this->pref['show_today'] = TRUE;
		$this->pref['show_day_names'] = TRUE;
		$this->pref['show_short_days'] = FALSE;

		$this->month = date( 'n' );
		$this->year = date( 'Y' );

		$this->CI = & get_instance();
	}

	/**
	 * Urci, zdali se ma vykreslit dnesni den (priradi se mu trida 'today'.
	 * Pokud se urci jiny mesic nebo rok, nez je momentalni, automaticky
	 * se tato funkce vypne. Ale pozor, manualne ji zas muzete zapnout.
	 * @param boolean $enable
	 * @return \MY_Calendar
	 */
	public function show_today($enable = TRUE)
	{
		$this->pref['show_today'] = $enable;
		return $this;
	}

	/**
	 * Urci, jestli se maji vypsat do thead popisky dni.
	 * Jejich popisek se nalezne v lang_common.php
	 * @param boolean $enable - TRUE - vypisou se, FALSE nevypisou se
	 * @param boolean $short - ma se vypsat kratka verze dni? (po / pondeli)
	 * @return \MY_Calendar
	 */
	public function show_day_names($enable = TRUE, $short = FALSE)
	{
		$this->pref['show_day_names'] = $enable;
		$this->pref['show_short_days'] = $short;
		return $this;
	}

	/**
	 * Vrati pole, ktere urci predchozi mesic (vcetne roku)
	 * @return array() ['year'] , ['month]
	 */
	public function get_previous_month()
	{
		return array(
			 'year' => $this->month == 1 ? $this->year - 1 : $this->year,
			 'month' => $this->month == 1 ? 12 : $this->month - 1
		);
	}

	/**
	 * Vrati pole, ktere urci nasledujici mesic (vcetne roku)
	 * @return array() ['year'] , ['month]
	 */
	public function get_next_month()
	{
		return array(
			 'year' => $this->month == 12 ? $this->year + 1 : $this->year,
			 'month' => $this->month == 12 ? 1 : $this->month + 1
		);
	}

	/**
	 * Vraci vygenerovany kalendar.
	 * @return string
	 */
	public function generate()
	{
		$first_day = mktime( 0, 0, 0, $this->month, 1, $this->year );
		$days_in_month = date( 't', $first_day );
		$first_day = date( 'w', $first_day );
		$current_day = date( 'd' );




		$html = function($element, $class)
				  {
					  return "<$element" . ($class != "" ? " class='$class'>" : ">");
				  };
		$output = $html( "table", $this->class["table"] );


		//= Generovani TH prvku (nazvy tydnu)
		if ( $this->pref['show_day_names'] )
		{
			$output .= "<thead><tr>";
			for ( $index = 1; $index < 8; $index++ )
			{
				$output .= "<th>" . $this->CI->lang->line( "calendar_day_" . ($this->pref['show_short_days'] ? $index . "_short" : $index) ) . "</th>" . PHP_EOL;
			}
			$output .= "</tr></thead>" . PHP_EOL;
		}

		$output .= $html( "tbody", $this->class['month'] ) . $html( "tr", $this->class['week'] ) . PHP_EOL;
		for ( $day = 1; $day < $days_in_month + $first_day; $day++ )
		{
			$d = $day - ($first_day - 1);


			if ( $day < $first_day )
				$output .= $html( "td", $this->class['day'] . " " . $this->class['unused'] . " " . $this->class['old'] ) . "</td>";
			else
			{
				//= Urceni zdali se ma oznacit aktualni (dnesni) den
				if ( $this->pref['show_today'] && $current_day == $d )
					$class = $this->class['today'] . " " . $this->class['day'];
				else
					$class = $this->class['day'];

				$day_cell = "<div><span class='day_number'>$d</span>";
				if ( isset( $this->cell_data[$d] ) )
				{
					$day_cell .= "<span class='inner'>" . implode( PHP_EOL, $this->cell_data[$d] ) . "</span>";
				}

				$output .= $html( "td", $class ) . $day_cell . "</div></td>";
			}

			if ( $day % 7 == 0 && $d < $days_in_month )
			{
				//konec tydne
				$output .="</tr>" . PHP_EOL . $html( "tr", $this->class['week'] );
			}
		}


		//= dopocitani zbylych dnu
		while ( ($day - 1) % 7 != 0 )
		{
			$day++;
			$output .= $html( "td", $this->class['day'] . " " . $this->class['unused'] . " " . $this->class['next'] ) . "</td>";
		}

		$output .= "</tr></tbody></table>" . PHP_EOL;

		return $output;
	}

	/**
	 * Vrati datum, ktery urceno pro vykresleni.
	 * Vohnde kdyz je potreba nekam externe vykreslit o jaky se 
	 * jedna mesic , rok
	 * @param type $as_string - Mesic se vrati v nazvu, ne v cisle
	 * @return array() ['year'] , ['month']
	 */
	public function get_actual_month($as_string = TRUE)
	{
		return array(
			 'year' => $this->year,
			 'month' => $as_string ? $this->CI->lang->line( "calendar_month_" . $this->month ) : $this->month
		);
	}

	/**
	 * Zmeni datum pro vykresleni kalendare.
	 * @param int $year - pro jaky rok se ma kalendar vypsat?
	 * @param int $month_number - pro jaky mesic se ma vypsat.
	 * @return \MY_Calendar
	 */
	public function set_actual_month($year, $month_number)
	{

		$this->year = $year;
		$this->month = $month_number;
		//= Pokud se nejedna o aktualni mesic, nemuze se vyznacit aktualni den
		if ( date( "n/Y" ) != $this->month . "/" . $this->year )
			  $this->show_today( FALSE );

		return $this;
	}

	/**
	 * Vlozi html prvky (text, odkazy..) do bunky odkazujici
	 * na dany den
	 * @param int $day_number - den v mesici, kam se maji data vlozit
	 * @param String/array(String) $data - text, ktery se ma vlozit
	 * @return \Calendar
	 */
	public function insert_data($day_number, $data)
	{
		if ( !is_array( $data ) )
		{
			$data = array($data);
		}

		foreach ( $data as $row )
		{
			$this->cell_data[$day_number][] = $row;
		}

		return $this;
	}

	/**
	 * Nastavi tridu pro cely radek (tr - tyden)
	 * @param String $class_name
	 * @return \MY_Calendar
	 */
	public function set_week_class($class_name)
	{
		$this->class['week'] = $class_name;
		return $this;
	}

	/**
	 * Nastavi tridu pro bunku (td - den)
	 * @param String $class_name
	 * @return \MY_Calendar
	 */
	public function set_day_class($class_name)
	{
		$this->class['day'] = $class_name;
		return $this;
	}

	/**
	 * Nastavi tridu pro cely tbody
	 * @param String $class_name
	 * @return \MY_Calendar
	 */
	public function set_month_class($class_name)
	{
		$this->class['month'] = $class_name;
		return $this;
	}

}