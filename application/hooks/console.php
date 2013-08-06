<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_URI $uri
 * @property CI_Output $output
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Menu $menu
 * @property Tank_auth $tank_auth //sprava prihlasenych
 * @property Template $template
 * @property Message $message
 * @property CI_Benchmark $b
 */
class console
{

	private $CI;
	private $panels = array();
	private $prefix = "ci-";

	public function __construct()
	{
		$this->CI = & get_instance();
	}

	public function init()
	{
		//= Vykresleni stranky
		$output = $this->CI->output->get_output();

		//= Do ajax dotazu prihodi informaci o dotazek databaze (paklize nejake jsou)
		if ( $this->CI->input->is_ajax_request() )
		{
			if ( $output == "" )
				return;

			$dbq = $this->CI->consolelogger->get_data_from_namespace( 'database' );

			if ( $dbq != null )
			{
				$this->CI->load->helper( 'text' );
				//= Priprava dbq dat

				foreach ( $dbq as &$q )
				{
					$q['query'] = highlight_code( str_replace( ' VALUES', "\nVALUES", $q['query'] ), ENT_QUOTES );
					$q['elapsed_time'] = number_format( $q['elapsed_time'] * 100, 4 ) . 's';
					$proc = '<span style="white-space: nowrap;">' . $q['parent_function'][0] . "<br>";
					$proc .= "from: <span style='font-size: 10px'>" . $q['parent_function'][1] . "</span></span>";
					$q['functions'] = $proc;
				}

				$json_output = array_merge( json_decode( $output, true ), array('db_queries' => $dbq) );

				echo json_encode( $json_output );
			}
			else
			{
				echo $output;
			}

			return;
		}


		// If the output data contains closing </body> and </html> tags
		// we will remove them and add them back after we insert the profile data
		if ( preg_match( "|</body>.*?</html>|is", $output ) )
		{
			$output = preg_replace( "|</body>.*?</html>|is", '', $output );
			$output .= $this->_build_panel();
			$output .= '</body></html>';
		}

		echo $output;
	}

	protected function _build_panel()
	{

		$this->_compile_runtime();
		$this->_compile_memory_usage();
		$this->_compile_post();
		$this->_compile_queries();
		$this->_compile_uri_string();
		$this->_compile_user_info();

		$data['prefix'] = $this->prefix;
		$data['panels'] = $this->panels;
		return $this->CI->load->view( "templates/comp/console/tmpl_console_panel", $data, TRUE );
	}

	/**
	 * @param type $panel_name
	 * @param type $label_data
	 * @param type $window_data
	 * @param type $image 
	 */
	protected function add_panel($panel_name, $label_data, $window_data = null, $image = false)
	{
		$data['prefix'] = $this->prefix;
		$data['name'] = $panel_name;
		$data['clickable'] = $window_data == null ? FALSE : TRUE;
		$data['img'] = $image;

		if ( is_array( $label_data ) )
		{
			$data['label'] = $label_data['label'];
			$data['heading'] = isset( $label_data['heading'] ) ? $label_data['heading'] : $label_data['label'];
			$data['title'] = isset( $label_data['title'] ) ? $label_data['title'] : $label_data['label'];
		}
		else
		{
			$data['label'] = $label_data;
			$data['heading'] = $label_data;
			$data['title'] = $label_data;
		}

		if ( $window_data != null && is_array( $window_data ) )
		{
			//= Transform to proper table data
			$data['window_data']['heading'] = isset( $window_data['heading'] ) ? $window_data['heading'] : null;

			$data['window_data']['rows'] = !is_array( $window_data['rows'][0] ) ? array($window_data['rows']) : $window_data['rows'];
			if ( !is_array( $data['window_data']['rows'][0] ) )
				$data['window_data']['rows'][0] = $data['window_data']['rows'];

			//= Prevede <th> tridu Builder na normalni string
			if ( $data['window_data']['heading'] != null )
			{
				$heading = new ConsoleHTMLBuilder( "tr", $data['window_data']['heading'] );
				$data['window_data']['heading'] = (string) $heading;
			}

			//= Prevede <tr> tridu Builder na normalni string
			foreach ( $data['window_data']['rows'] as &$tr )
			{
				$a = new ConsoleHTMLBuilder( "tr", $tr );
				$tr = (string) $a;
			}
		}
		else
		{
			$data['window_data'] = null;
		}

		$this->panels[] = $this->CI->load->view( "templates/comp/console/tmpl_console_panel_object", $data, TRUE );
	}

	/**
	 * Panel pro zobrazeni informaci ohledne uctu
	 * Pokud uzivatel neni prihlasen, vraci to hlasku, ze neni prihlasen.
	 * V druhem pripade se vrati detailni popis uctu.
	 * POZOR! CI spoleha, ze se nacita take role uctu 
	 */
	protected function _compile_user_info()
	{

		$data['title'] = "You'ren't logged in";
		$data['label'] = "";
		$data['img'] = "user_off.png";
		$window = null;



		if ( User::is_logged_in() )
		{
			$this->CI->load->library( "roles" );
			$data['title'] = "You're logged in";
			$data['label'] = User::get_username();
			$data['heading'] = "user info: " . $data['label'] . ' - (' . User::get_id().")";
			$data['img'] = "user.png";
			$th = array();
			$th[] = new ConsoleHTMLBuilder( 'th', "user id" );
			$th[] = new ConsoleHTMLBuilder( 'th', "user name" );
			$th[] = new ConsoleHTMLBuilder( 'th', "user role" );
			$window['heading'] = $th;

			$td = array();
			$td[] = new ConsoleHTMLBuilder( 'td', User::get_id() );
			$td[] = new ConsoleHTMLBuilder( 'td', User::get_username() );
			$td[] = new ConsoleHTMLBuilder( 'td', User::get_role() );

			$window['rows'] = $td;
		}

		$this->add_panel( "userinfo", $data, $window, $data['img'] );
	}

	/**
	 * Panel pro zobrazeni rychlosti stranek 
	 */
	protected function _compile_runtime()
	{
		$this->CI->benchmark->mark( "total_execution_time_end" );
		$time = $this->CI->benchmark->elapsed_time( "total_execution_time_start", "total_execution_time_end" );
		$label = number_format( $time * 1000, 2 ) . " ms";
		$data['label'] = number_format( $time * 1000, 2 ) . " ms";
		$data['title'] = "loading time: " . number_format( $time * 1000, 2 ) . " ms";
		$this->add_panel( "runtime", $data, NULL, "time.png" );
	}

	/**
	 * Compile Queries
	 *
	 * @return string
	 */
	protected function _compile_queries()
	{
		$dbs = array();
		$output = null;

		// Let's determine which databases are currently connected to
		foreach ( get_object_vars( $this->CI ) as $CI_object )
		{
			if ( is_object( $CI_object ) && is_subclass_of( get_class( $CI_object ), 'CI_DB' ) )
			{
				$dbs[] = $CI_object;
			}
		}

		if ( count( $dbs ) == 0 )
		{
			return;
		}

		$data['label'] = "0 queries";
		$data['title'] = "database wasn't used";

		// Load the text helper so we can highlight the SQL
		$this->CI->load->helper( 'text' );
		$dbq = $this->CI->consolelogger->get_data_from_namespace( 'database' );
		if ( $dbq == null )
			$data['window'] = null;
		else
		{
			foreach ( $dbq as $q )
			{
				$query = highlight_code( $q['query'], ENT_QUOTES );
				$time = number_format( $q['elapsed_time'] * 100, 4 ) . 's';
				$proc = '<span style="white-space: nowrap;">' . $q['parent_function'][0] . "<br>";
				$proc .= "from: <span style='font-size: 10px'>" . $q['parent_function'][1] . "</span></span>";

				//= Generovani jednoho radku do tabulky
				$td = array();
				$td[] = new ConsoleHTMLBuilder( "td", $proc ); //bunka procedur
				$td[] = new ConsoleHTMLBuilder( "td", $query ); //query bunka
				$td[] = new ConsoleHTMLBuilder( "td", $time ); //casova bunka

				$td_rows = new ConsoleHTMLBuilder( "td", $q['rows'] );

				//= Generovani rows bunky -> moznost rozkliknout
				if ( $q['result'] != null )
				{
					$td_rows->add_attribute( "class", "more-info" );
					$td_rows_mi = new ConsoleHTMLBuilder( "div" );
					$container = "<tr><th>result</th></tr>";
					$container .= "<tr><td><pre>" . highlight_code( print_r( $q['result'], true ), ENT_QUOTES ) . "</pre></td></tr>";
					$td_rows_mic = new ConsoleHTMLBuilder( "table", $container );


					$td_rows_mi->set_inner_html( $td_rows_mic )->add_attribute( 'class', $this->prefix . "more-info-container" );
					$td_rows->add_inner_html( $td_rows_mi );
				}

				$td[] = $td_rows;

				$data['window']['rows'][] = $td;
			}

			$data['label'] = count( $dbq ) . " queries";
			$data['title'] = count( $dbq ) . " queries was executed";

			$data['window']['heading'] = array("procedure", "sql query", "execution time", "rows");
			$th = array();
			$th[] = new ConsoleHTMLBuilder( "th", "procedure" );
			$th[] = new ConsoleHTMLBuilder( "th", "sql query" );
			$th[] = new ConsoleHTMLBuilder( "th", "execution time" );
			$th[] = new ConsoleHTMLBuilder( "th", "rows" );

			$data['window']['heading'] = $th;


			$table = new ConsoleHTMLBuilder();
			$table->add_inner_html( $th );
		}

		$this->add_panel( "queries", $data, $data['window'], "database.png" );
	}

	/**
	 * Compile $_POST Data
	 *
	 * @return string
	 */
	protected function _compile_post()
	{
		$output = array();

		if ( count( $_POST ) == 0 )
		{
			return;
		}
		else
		{
			foreach ( $_POST as $key => $val )
			{
				if ( !is_numeric( $key ) )
				{
					$key = "'" . $key . "'";
				}

				if ( is_array( $val ) )
				{
					$output['&#36;_POST[' . $key . ']'] = '<pre>' . htmlspecialchars( stripslashes( print_r( $val, TRUE ) ) ) . '</pre>';
				}
				else
				{
					$output['&#36;_POST[' . $key . ']'] = htmlspecialchars( stripslashes( $val ) );
				}
			}
		}
		$this->add_panel( "postinfo", count( $_POST ) . " posts", $output, "post.png" );
	}

	/**
	 * Show query string
	 *
	 * @return string
	 */
	protected function _compile_uri_string()
	{
		if ( $this->CI->uri->uri_string == '' )
		{
			return;
		}

		$data['label'] = "";
		$data['heading'] = "Uri strings";
		$data['title'] = "no uri";

		$output = array();
		$index = 2;
		$th[] = new ConsoleHTMLBuilder( "th", "pozice" );
		$th[] = new ConsoleHTMLBuilder( "th", "segment" );
		$output['heading'] = $th;

		foreach ( $this->CI->uri->segment_array() as $segment )
		{
			$td = array();
			$td[] = new ConsoleHTMLBuilder( "td", $index );
			$td[] = new ConsoleHTMLBuilder( "td", $segment );
			$output['rows'][] = $td;
			$index++;
		}

		$data['label'] = "";


		$this->add_panel( "uriinfo", $data, $output, "url.png" );
	}

	/**
	 * Compile memory usage
	 *
	 * Display total used memory
	 *
	 * @return string
	 */
	protected function _compile_memory_usage()
	{
		if ( function_exists( 'memory_get_usage' ) && ($usage = memory_get_usage()) != '' )
			$this->add_panel( "memoryusage", $this->get_file_size( $usage ), null, "memory.png" );
		else
			return;
	}

	/**
	 * Show the controller and function that were called
	 *
	 * @return string
	 */
	protected function _compile_controller_info()
	{
		$output = $this->CI->router->fetch_class() . "/" . $this->CI->router->fetch_method();

		return $output;
	}

	public static function get_file_size($size, $retstring = null)
	{
		// adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
		$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		if ( $retstring === null )
		{
			$retstring = '%01.2f %s';
		}

		$lastsizestring = end( $sizes );

		foreach ( $sizes as $sizestring )
		{
			if ( $size < 1024 )
			{
				break;
			}
			if ( $sizestring != $lastsizestring )
			{
				$size /= 1024;
			}
		}
		if ( $sizestring == $sizes[0] )
		{
			$retstring = '%01d %s';
		} // Bytes aren't normally fractional
		return sprintf( $retstring, $size, $sizestring );
	}

}

/**
 * @author Pavel Vais 
 */
class ConsoleHTMLBuilder
{

	public $element;
	public $attributes;
	private $inner_html;

	public function __construct($element = null, $inner_html = null)
	{
		$this->attributes = array();
		$this->inner_html = "";

		if ( $element != null )
			$this->set_element( $element )->set_inner_html( $inner_html );
	}

	public function __toString()
	{
		$string = "<" . $this->element;

		foreach ( $this->attributes as $attr => $val )
		{
			$string .= " $attr=\"$val\"";
		}

		$string .= ">" . $this->inner_html . "</" . $this->element . ">";

		return $string;
	}

	public function add_attribute($name, $value)
	{
		$this->attributes[$name] = $value;
		return $this;
	}

	public function set_inner_html($inner_html)
	{
		$this->inner_html = "";
		$this->add_inner_html( $inner_html );
		return $this;
	}

	public function add_inner_html($inner_html)
	{
		if ( !is_array( $inner_html ) )
			$inner_html = array($inner_html);

		foreach ( $inner_html as $text )
		{
			if ( $text instanceof ConsoleHTMLBuilder )
			{
				$this->inner_html .= (string) $text;
			}
			else
				$this->inner_html .= $text;
		}
		return $this;
	}

	public function set_element($element)
	{
		$this->element = $element;
		return $this;
	}

}

?>
