<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Menu $menu
 * @property Tank_auth $tank_auth //sprava prihlasenych
 * @property Template $template
 * @property Message $message
 * @property MY_Lang $lang
 * @property GoogleAnalytics $googleanalytics
 */
class Start extends My_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->helper( 'text' );
	}

	/**
	 *
	 */
	public function index()
	{
		$data = array();
		$this->load->view( 'view_start', $data );
	}

	public function database()
	{
		$this->load->database();
		$TM = new TestModel;

		$TM->get_all();
		$this->lang->view( 'homepage/view_maintenance' );
		//FB::info($TM->get_all(),'return');
	}

	public function pruser()
	{
		$pocet_dni = 120;					// Pro kolik dni se budou vysledky generovat
		$pocet_lidi_na_segment = 60;	// Pocet lidi v dany cas (hodne orientacni cislo)
		$odData = '01-02-2014';			// dd-mm-yyyy datum od kdy se zacne generovat
		$odchylka = 7; // odchylka od lidi+- (v %) , k teto odchylce se pripocitava kladna odchylka pro ejdnotlive dny

		/**
		 * Odchylka podle dnu, ktera se pouze pripocitava k lidi na segment
		 * tzn v nedeli bude lidi o 29% vice (až! 29%) nez v jiny den
		 */
		$odchylka_kladna = array(
			 1 => 5, //po
			 2 => 7, //ut
			 3 => 7, //st
			 4 => 6, //ct
			 5 => 16, //pa
			 6 => 22, //so
			 7 => 29, //ne
		);
		/**
		 * Sance, ze v danou dobu klikne user na reklamu
		 */
		$rozlozeni = array(
			 'noc_po' => 0.02,
			 'noc' => 0.005,
			 'rano' => 0.05,
			 'poledne' => 0.08,
			 'odpoledne' => 0.12,
			 'vecer' => 0.18,
			 'noc_pred' => 0.03,
		);
		
		/**
		 * Rozmezi ejdnotlivych segmentu
		 */
		$casy = array(
			 'noc_po' => array(0, 2),
			 'noc' => array(3, 5),
			 'rano' => array(6, 10),
			 'poledne' => array(11, 13),
			 'odpoledne' => array(14, 16),
			 'vecer' => array(17, 21),
			 'noc_pred' => array(22, 23)
		);
		$output = array();
		$l = count( $rozlozeni );
		mt_srand( rand(0,80000000) );
		$aktdenVTydnu = date( 'N', strtotime( $odData ) );
		$temp = $aktdenVTydnu;
		$b = array();
		echo '<table>';
		for ( $index = $aktdenVTydnu; $index < $pocet_dni + $aktdenVTydnu; $index++ )
		{
			foreach ( $rozlozeni as $k => $r )
			{
				$max = $pocet_lidi_na_segment / 100 * rand( 100 - $odchylka, 100 + $odchylka + $odchylka_kladna[$aktdenVTydnu] );
				for ( $index1 = 0; $index1 < $max; $index1++ )
				{
					$random = mt_rand( 0, 1000 ) / 1000;
					if ( $r > $random )
					{
						if ( !isset( $output[$index][$k] ) )
							$output[$index][$k] = 1;
						else
							$output[$index][$k] ++;

						$a = $this->generujDatum( $k, $casy, $index - $temp, $odData );
						//$b[date('G',strtotime($a))][] = $a;
						echo $a;
					}
				}
			}
			$aktdenVTydnu = ($index % 7 == 0) ? 1 : $aktdenVTydnu + 1;
		}
		echo '</table>';
		
		/*for ($index2 = 1; $index2 < 24; $index2++) {
		dump(count($b[$index2]),$index2);
}
		dump($b);
		//dump($output);*/
	}

	private function generujDatum($aktsegment, $casy, $aktDenPoradi, $odData)
	{
		$sekundy = $aktDenPoradi * 24 * 60 * 60;
		$rozmezi = $casy[$aktsegment];
		$r = (mt_rand( $rozmezi[0], $rozmezi[1] )) * 60 * 60; //hodinove rozmezi v sekundach
		$r = $r + rand( 0, 60 ) * 60 + rand(0,60);
		$sekundy = $sekundy + $r;
		$den = date( 'Y-m-d H:i:s', strtotime( $odData . ' + ' . $sekundy . ' seconds' ) );
		//return $den;
		return "<tr><td>" . $den . " </td><td>" . (1) . "</td></tr>";
	}

}

/* End of file homepage.php */
/* Location: ./application/controllers/homepage.php */