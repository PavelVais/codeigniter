<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

// To disable UhOh! simply change IN_PRODUCTION to TRUE.
if ( !defined( 'IN_PRODUCTION' ) ) {
	define( 'IN_PRODUCTION', ENVIRONMENT == 'production' );
}
/**
 * CodeIgniter UhOh!
 *
 * This is an extension on CI_Extensions that provides awesome error messages
 * with full backtraces and a view of the line with the error.  It is based
 * on Kohana v3 Error Handling.
 *
 * @package		CodeIgniter
 * @author		Dan Horrigan <http://dhorrigan.com>
 * @license		Apache License v2.0
 * @version		1.0
 */
/**
 * This file contains some functions originally from Kohana.  They have been modified
 * to work with CodeIgniter.  Here is the obligatory Kohana license info:
 *
 * @copyright  (c) 2008-2009 Kohana Team
 * @license	   http://kohanaphp.com/license
 */

/**
 * MY_Exceptions
 *
 * @subpackage	Exceptions
 */
class MY_Exceptions extends CI_Exceptions {

	/**
	 * Some nice names for the error types
	 */
	public static $php_errors = array(
	    E_ERROR => 'Fatal Error',
	    E_USER_ERROR => 'User Error',
	    E_PARSE => 'Parse Error',
	    E_WARNING => 'Warning',
	    E_USER_WARNING => 'User Warning',
	    E_STRICT => 'Strict',
	    E_NOTICE => 'Notice',
	    E_RECOVERABLE_ERROR => 'Recoverable Error',
	);

	/**
	 * The Shutdown errors to show (all others will be ignored).
	 */
	public static $shutdown_errors = array(E_PARSE, E_ERROR, E_USER_ERROR, E_COMPILE_ERROR);

	/**
	 * Construct
	 *
	 * Sets the error handlers.
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		parent::__construct();
		// If we are in production, then lets dump out now.
		//Set the Exception Handler
		set_exception_handler( array('MY_Exceptions', 'exception_handler') );

		// Set the Error Handler
		set_error_handler( array('MY_Exceptions', 'error_handler') );

		// Set the handler for shutdown to catch Parse errors
		register_shutdown_function( array('MY_Exceptions', 'shutdown_handler') );

		// This is a hack to set the default timezone if it isn't set. Not setting it causes issues.
		date_default_timezone_set( date_default_timezone_get() );
	}

	/**
	 * Debug Path
	 *
	 * This makes nicer looking paths for the error output.
	 *
	 * @access	public
	 * @param	string	$file
	 * @return	string
	 */
	public static function debug_path($file) {
		if ( strpos( $file, APPPATH ) === 0 ) {
			$file = 'APPPATH/' . substr( $file, strlen( APPPATH ) );
		}
		elseif ( strpos( $file, SYSDIR ) === 0 ) {
			$file = 'SYSDIR/' . substr( $file, strlen( SYSDIR ) );
		}
		elseif ( strpos( $file, FCPATH ) === 0 ) {
			$file = 'FCPATH/' . substr( $file, strlen( FCPATH ) );
		}

		return $file;
	}

	/**
	 * Error Handler
	 *
	 * Converts all errors into ErrorExceptions. This handler
	 * respects error_reporting settings.
	 *
	 * @access	public
	 * @throws	ErrorException
	 * @return	bool
	 */
	public static function error_handler($code, $error, $file = NULL, $line = NULL) {
		if ( error_reporting() & $code ) {
			// This error is not suppressed by current error reporting settings
			// Convert the error into an ErrorException
			self::exception_handler( new ErrorException( $error, $code, 0, $file, $line ) );
		}

		// Do not execute the PHP error handler
		return TRUE;
	}

	public static function error2database(Exception $e) {
		$string_color = '#A35E34';
		$ci = & get_instance();

		if ( !class_exists( 'CI_DB' ) || $ci->db === '' ) {
			return false;
		}

		if ( !$ci->db->table_exists( 'errors' ) ) {
			return false;
		}

		$data = array(
		    'class' => get_class( $e ),
		    'message' => $e->getMessage(),
		    'file' => $e->getFile(),
		    'line' => $e->getLine()
		);


		$detail = '';
		$deep = 9;
		$from = 2; //odkud se veme detail tracu (prvni byvaji vypisy erroru
		$index = 0;
		$trace = $e->getTrace();
		if ( isset( $trace[2] ) ) {
			$data['class'] = $trace[2]['class'];
		}
		foreach ( $trace as $r )
		{
			if ( !isset( $r['file'] ) )
				$r['file'] = '';
			if ( !isset( $r['line'] ) )
				$r['line'] = '';
			if ( !isset( $r['class'] ) )
				$r['class'] = '';

			if ( $deep + $from === $index++ ) {
				break;
			}
			if ( $index < $from ) {
				continue;
			}

			$detail .= "<strong style='text-decoration: underline;'>[" . ($index - $from + 1) . "] => " . '<span title="file: ' . $r['file'] . '">' . $r['class'] . ' (' . $r['line'] . ')</span></strong> 	»';
			$args = $r['args'];
			$d = '';



			if ( is_array( $args ) && !empty( $args ) ) {
				foreach ( $args as $arg )
				{
					if ( is_array( $arg ) ) {
						if ( empty( $arg ) ) {
							$d .= ', array()';
							continue;
						}
						$d .= ', array(<br>';
						foreach ( $arg as $k => $ar )
						{
							$d .= '&nbsp;&nbsp;&nbsp;[' . $k . '] => ' . (is_string( $ar ) ? '<span style="color: ' . $string_color . '">\'' . $ar . '\</span>' : 'array') . '<br>';
						}
						$d .= ')<br>';
					}
					else {
						$d .= ', ' . (string) '<span style="color: ' . $string_color . '">\'' . $arg . '\'</span>';
					}
				}
				$d = substr( $d, 2 );
			}

			$detail .= ' <strong>' . $r['function'] . '</strong>(' . $d . ')<br>';
		}

		$data['detail'] = $detail;

		$ci->load->library( 'user_agent' );
		$data['user_agent'] = $ci->agent->agent_string();
		$data['ip'] = $ci->input->ip_address();
		if ( class_exists( 'User' ) ) {
			if ( User::is_logged_in() ) {
				$data['user_name'] = User::get_username();
			}
			$data['user_id'] = User::get_id();
		}
		$data['ip'] = $ci->input->ip_address();
		$data['date'] = date( "Y-m-d H:i:s" );
		$data['url'] = current_url();



		$ci->db->insert( 'errors', $data );
	}

	private static function user_can($ci, $name) {
		if ( !is_loaded( 'User' ) )
			return true;

		$users = $ci->config->item( $name );
		if ( $users === false )
			return true;

		if ( !is_array( $users ) )
			$users = (array) $users;

		if ( !User::is_logged_in() )
			return true;
		return !in_array( User::get_id(), $users );
	}

	/**
	 * Exception Handler
	 *
	 * Displays the error message, source of the exception, and the stack trace of the error.
	 *
	 * @access	public
	 * @param	object	 exception object
	 * @return	boolean
	 */
	public static function exception_handler(Exception $e) {

		if ( $e->getMessage() == 'The page you requested was not found.' ) {

			if ( !class_exists( 'CI_Controller' ) ) {
				require BASEPATH . 'core/Controller.php';
			}
			if ( !class_exists( 'Common' ) ) {
				//require(BASEPATH.'core/Common.php');
			}
			$exception = new CI_Exceptions();
			echo $exception->show_error( 'This page is missing.', 'Sorry, but page you are looking for is not exists.', ! IN_PRODUCTION ? 'error_404' : 'error_404_production', 400 );
			return;
		}
		else
			
		$ci = & get_instance();
		if ( $ci->config->item( 'errors_2_db' ) && self::user_can( $ci, 'db_omit_user_id' ) ) {
			self::error2database( $e );
		}

		if ( IN_PRODUCTION ) {
			try
			{
				// Start an output buffer
				ob_start();

				// This will include the custom error file.
				require FCPATH . '/application/errors/error_php_production.php';

				// Display the contents of the output buffer
				echo ob_get_clean();

				exit( 1 );
			}
			catch (Exception $e)
			{
				// Clean the output buffer if one exists
				ob_get_level() and ob_clean();
				// Display the exception text
				// Exit with an error status
				exit( 1 );
			}
		}
		else {

			try
			{
				// Get the exception information
				$type = get_class( $e );
				$code = $e->getCode();
				$message = $e->getMessage();
				$file = $e->getFile();
				$line = $e->getLine();

				if ( $code == E_STRICT || $code == E_WARNING )
					return true;

				// Create a text version of the exception
				$error = self::exception_text( $e );

				// Log the error message
				log_message( 'error', $error, TRUE );

				// Get the exception backtrace
				$trace = $e->getTrace();

				if ( $e instanceof ErrorException ) {
					if ( isset( self::$php_errors[$code] ) ) {
						// Use the human-readable error name
						$code = self::$php_errors[$code];
					}

					if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
						// Workaround for a bug in ErrorException::getTrace() that exists in
						// all PHP 5.2 versions. @see http://bugs.php.net/bug.php?id=45895
						for ( $i = count( $trace ) - 1; $i > 0; --$i )
						{
							if ( isset( $trace[$i - 1]['args'] ) ) {
								// Re-position the args
								$trace[$i]['args'] = $trace[$i - 1]['args'];

								// Remove the args
								unset( $trace[$i - 1]['args'] );
							}
						}
					}
				}
				// Start an output buffer
				ob_start();

				// This will include the custom error file.
				require FCPATH . '/application/errors/error_php_custom.php';

				// Display the contents of the output buffer
				echo ob_get_clean();

				return TRUE;
			}
			catch (Exception $e)
			{
				// Clean the output buffer if one exists
				ob_get_level() and ob_clean();

				// Display the exception text
				echo self::exception_text( $e ), "\n";

				// Exit with an error status
				exit( 1 );
			}
		}
	}

	/**
	 * Shutdown Handler
	 *
	 * Catches errors that are not caught by the error handler, such as E_PARSE.
	 *
	 * @access	public
	 * @return	void
	 */
	public static function shutdown_handler() {
		$error = error_get_last();
		if ( $error = error_get_last() AND in_array( $error['type'], self::$shutdown_errors ) ) {
			// Clean the output buffer
			ob_get_level() and ob_clean();

			// Fake an exception for nice debugging
			self::exception_handler( new ErrorException( $error['message'], $error['type'], 0, $error['file'], $error['line'] ) );

			// Shutdown now to avoid a "death loop"
			exit( 1 );
		}
	}

	/**
	 * Exception Text
	 *
	 * Makes a nicer looking, 1 line extension.
	 *
	 * @access	public
	 * @param	object	Exception
	 * @return	string
	 */
	public static function exception_text(Exception $e) {
		return sprintf( '%s [ %s ]: %s ~ %s [ %d ]', get_class( $e ), $e->getCode(), strip_tags( $e->getMessage() ), $e->getFile(), $e->getLine() );
	}

	/**
	 * Debug Source
	 *
	 * Returns an HTML string, highlighting a specific line of a file, with some
	 * number of lines padded above and below.
	 *
	 * @access	public
	 * @param	string	 file to open
	 * @param	integer	 line number to highlight
	 * @param	integer	 number of padding lines
	 * @return	string	 source of file
	 * @return	FALSE	 file is unreadable
	 */
	public static function debug_source($file, $line_number, $padding = 5) {
		if ( !$file OR !is_readable( $file ) ) {
			// Continuing will cause errors
			return FALSE;
		}

		// Open the file and set the line position
		$file = fopen( $file, 'r' );
		$line = 0;

		// Set the reading range
		$range = array('start' => $line_number - $padding, 'end' => $line_number + $padding);

		// Set the zero-padding amount for line numbers
		$format = '% ' . strlen( $range['end'] ) . 'd';

		$source = '';
		while ( ($row = fgets( $file )) !== FALSE )
		{
			// Increment the line number
			if ( ++$line > $range['end'] )
				break;

			if ( $line >= $range['start'] ) {
				// Make the row safe for output
				$row = htmlspecialchars( $row, ENT_NOQUOTES );

				// Trim whitespace and sanitize the row
				$row = '<span class="number">' . sprintf( $format, $line ) . '</span> ' . $row;

				if ( $line === $line_number ) {
					// Apply highlighting to this row
					$row = '<span class="line highlight">' . $row . '</span>';
				}
				else {
					$row = '<span class="line">' . $row . '</span>';
				}

				// Add to the captured source
				$source .= $row;
			}
		}

		// Close the file
		fclose( $file );

		return '<pre class="source"><code>' . $source . '</code></pre>';
	}

	/**
	 * Trace
	 *
	 * Returns an array of HTML strings that represent each step in the backtrace.
	 *
	 * @access	public
	 * @param	string	path to debug
	 * @return	string
	 */
	public static function trace(array $trace = NULL) {
		if ( $trace === NULL ) {
			// Start a new trace
			$trace = debug_backtrace();
		}

		// Non-standard function calls
		$statements = array('include', 'include_once', 'require', 'require_once');

		$output = array();
		foreach ( $trace as $step )
		{
			if ( !isset( $step['function'] ) ) {
				// Invalid trace step
				continue;
			}

			if ( isset( $step['file'] ) AND isset( $step['line'] ) ) {
				// Include the source of this step
				$source = self::debug_source( $step['file'], $step['line'] );
			}

			if ( isset( $step['file'] ) ) {
				$file = $step['file'];

				if ( isset( $step['line'] ) ) {
					$line = $step['line'];
				}
			}

			// function()
			$function = $step['function'];

			if ( in_array( $step['function'], $statements ) ) {
				if ( empty( $step['args'] ) ) {
					// No arguments
					$args = array();
				}
				else {
					// Sanitize the file path
					$args = array($step['args'][0]);
				}
			}
			elseif ( isset( $step['args'] ) ) {
				if ( strpos( $step['function'], '{closure}' ) !== FALSE ) {
					// Introspection on closures in a stack trace is impossible
					$params = NULL;
				}
				else {
					if ( isset( $step['class'] ) ) {
						if ( method_exists( $step['class'], $step['function'] ) ) {
							$reflection = new ReflectionMethod( $step['class'], $step['function'] );
						}
						else {
							$reflection = new ReflectionMethod( $step['class'], '__call' );
						}
					}
					else {
						$reflection = new ReflectionFunction( $step['function'] );
					}

					// Get the function parameters
					$params = $reflection->getParameters();
				}

				$args = array();

				foreach ( $step['args'] as $i => $arg )
				{
					if ( isset( $params[$i] ) ) {
						// Assign the argument by the parameter name
						$args[$params[$i]->name] = $arg;
					}
					else {
						// Assign the argument by number
						$args[$i] = $arg;
					}
				}
			}

			if ( isset( $step['class'] ) ) {
				// Class->method() or Class::method()
				$function = $step['class'] . $step['type'] . $step['function'];
			}

			$output[] = array(
			    'function' => $function,
			    'args' => isset( $args ) ? $args : NULL,
			    'file' => isset( $file ) ? $file : NULL,
			    'line' => isset( $line ) ? $line : NULL,
			    'source' => isset( $source ) ? $source : NULL,
			);

			unset( $function, $args, $file, $line, $source );
		}

		return $output;
	}

	/**
	 * General Error Page
	 *
	 * This function takes an error message as input
	 * (either as a string or an array) and displays
	 * it using the specified template.
	 *
	 * @access	private
	 * @param	string	the heading
	 * @param	string	the message
	 * @param	string	the template name
	 * @return	string
	 */
	function show_error($heading, $message, $template = 'error_general', $status_code = 500) {
		// If we are in production, then lets dump out now.
		if ( IN_PRODUCTION ) {
			return parent::show_error( $heading, $message, $template, $status_code );
		}

		if ( !headers_sent() ) {
			set_status_header( $status_code );
		}
		$trace = debug_backtrace();
		$file = NULL;
		$line = NULL;

		$is_from_app = FALSE;
		if ( isset( $trace[1]['file'] ) AND strpos( $trace[1]['file'], APPPATH ) === 0 ) {
			$is_from_app = !self::is_extension( $trace[1]['file'] );
		}

		// If the application called show_error, don't output a backtrace, just the error
		if ( $is_from_app ) {
			$message = '<p>' . implode( '</p><p>', (!is_array( $message )) ? array($message) : $message  ) . '</p>';

			if ( ob_get_level() > $this->ob_level + 1 ) {
				ob_end_flush();
			}
			ob_start();
			include(APPPATH . 'errors/' . $template . EXT);
			$buffer = ob_get_contents();
			ob_end_clean();
			return $buffer;
		}

		$message = implode( ' / ', (!is_array( $message )) ? array($message) : $message  );

		// If the system called show_error, so lets find the actual file and line in application/ that caused it.
		foreach ( $trace as $call )
		{
			if ( isset( $call['file'] ) AND strpos( $call['file'], APPPATH ) === 0 AND !self::is_extension( $call['file'] ) ) {
				$file = $call['file'];
				$line = $call['line'];
				break;
			}
		}
		unset( $trace );

		self::exception_handler( new ErrorException( $message, E_ERROR, 0, $file, $line ) );
		return;
	}

	/**
	 * Is Extension
	 *
	 * This checks to see if the file path is to a core extension.
	 *
	 * @access	private
	 * @param	string	$file
	 * @return	bool
	 */
	private static function is_extension($file) {
		foreach ( array('libraries/', 'core/') as $folder )
		{
			if ( strpos( $file, APPPATH . $folder . config_item( 'subclass_prefix' ) ) === 0 ) {
				return TRUE;
			}
		}
		return FALSE;
	}

}
