<?php

class MY_Router extends CI_Router {

	private $show_404 = false;
	private $recursive = false;

	function set_class($class) {
		$this->class = str_replace( '-', '_', $class );
	}

	function set_method($method) {
		$this->method = str_replace( '-', '_', $method );
	}

	function set_directory($dir) {
		$this->directory = $dir . '/';
	}

	function _validate_request($segments) {
		if ( count( $segments ) == 0 ) {
			return $segments;
		}
		// Does the requested controller exist in the root folder?
		if ( file_exists( APPPATH . 'controllers/' . str_replace( '-', '_', $segments[0] ) . '.php' ) ) {
			return $segments;
		}
		// Is the controller in a sub-folder?
		if ( is_dir( APPPATH . 'controllers/' . $segments[0] ) ) {
			// Set the directory and remove it from the segment array
			$this->set_directory( $segments[0] );
			$segments = array_slice( $segments, 1 );

			while ( count( $segments ) > 0 && is_dir( APPPATH . 'controllers/' . $this->directory . $segments[0] ) )
			{
				// Set the directory and remove it from the segment array
				$this->set_directory( $this->directory . $segments[0] );
				$segments = array_slice( $segments, 1 );
			}

			if ( count( $segments ) > 0 ) {
				// Does the requested controller exist in the sub-folder?
				if ( !file_exists( APPPATH . 'controllers/' . $this->fetch_directory() . str_replace( '-', '_', $segments[0] ) . '.php' ) ) {
					if ( !empty( $this->routes['404_override'] ) ) {
						$x = explode( '/', $this->routes['404_override'] );

						$this->set_directory( '' );
						$this->set_class( $x[0] );
						$this->set_method( isset( $x[1] ) ? $x[1] : 'index'  );

						return $x;
					}
					else {
						show_404($this->fetch_directory() . $segments[0]);
					}
				}
			}
			else {
				// Is the method being specified in the route?
				if ( strpos( $this->default_controller, '/' ) !== FALSE ) {
					$x = explode( '/', $this->default_controller );

					$this->set_class( $x[0] );
					$this->set_method( $x[1] );
				}
				else {
					$this->set_class( $this->default_controller );
					$this->set_method( 'index' );
				}

				// Does the default controller exist in the sub-folder?
				if ( !file_exists( APPPATH . 'controllers/' . $this->fetch_directory() . $this->default_controller . '.php' ) ) {
					$this->directory = '';
					return array();
				}
			}

			return $segments;
		}


		// If we've gotten this far it means that the URI does not correlate to a valid
		// controller class.  We will now see if there is an override
		if ( !empty( $this->routes['404_override'] ) ) {
			$x = explode( '/', $this->routes['404_override'] );

			$this->set_class( $x[0] );
			$this->set_method( isset( $x[1] ) ? $x[1] : 'index'  );

			return $x;
		}


		// Nothing else to do at this point but show a 404
		show_404($segments[0]);
	}

	/**
	 * Edited by Pavel Vais: podpora rekurzivniho vyhledavani: 
	 * je mozne vyuzit vice route pravidel
	 * Set the route mapping
	 *
	 * This function determines what should be served based on the URI request,
	 * as well as any "routes" that have been set in the routing config file.
	 *
	 * @access	private
	 * @return	void
	 */
	function _set_routing() {
		require_once APPPATH . '/libraries/components/FirePHPCore/FirePHP.php';
		require_once APPPATH . '/libraries/components/FirePHPCore/fb.php';
		// Are query strings enabled in the config file?  Normally CI doesn't utilize query strings
		// since URI segments are more search-engine friendly, but they can optionally be used.
		// If this feature is enabled, we will gather the directory/class/method a little differently
		$segments = array();
		if ( $this->config->item( 'enable_query_strings' ) === TRUE AND isset( $_GET[$this->config->item( 'controller_trigger' )] ) ) {
			if ( isset( $_GET[$this->config->item( 'directory_trigger' )] ) ) {
				$this->set_directory( trim( $this->uri->_filter_uri( $_GET[$this->config->item( 'directory_trigger' )] ) ) );
				$segments[] = $this->fetch_directory();
			}

			if ( isset( $_GET[$this->config->item( 'controller_trigger' )] ) ) {
				$this->set_class( trim( $this->uri->_filter_uri( $_GET[$this->config->item( 'controller_trigger' )] ) ) );
				$segments[] = $this->fetch_class();
			}

			if ( isset( $_GET[$this->config->item( 'function_trigger' )] ) ) {
				$this->set_method( trim( $this->uri->_filter_uri( $_GET[$this->config->item( 'function_trigger' )] ) ) );
				$segments[] = $this->fetch_method();
			}
		}

		// Load the routes.php file.
		if ( defined( 'ENVIRONMENT' ) AND is_file( APPPATH . 'config/' . ENVIRONMENT . '/routes.php' ) ) {
			include(APPPATH . 'config/' . ENVIRONMENT . '/routes.php');
		}
		elseif ( is_file( APPPATH . 'config/routes.php' ) ) {
			include(APPPATH . 'config/routes.php');
		}

		$this->routes = (!isset( $route ) OR !is_array( $route )) ? array() : $route;
		unset( $route );

		// Set the default controller so we can display it in the event
		// the URI doesn't correlated to a valid controller.
		$this->default_controller = (!isset( $this->routes['default_controller'] ) OR $this->routes['default_controller'] == '') ? FALSE : strtolower( $this->routes['default_controller'] );

		// Were there any query string segments?  If so, we'll validate them and bail out since we're done.
		if ( count( $segments ) > 0 ) {
			return $this->_validate_request( $segments );
		}

		// Fetch the complete URI string
		$this->uri->_fetch_uri_string();

		// Is there a URI string? If not, the default controller specified in the "routes" file will be shown.
		if ( $this->uri->uri_string == '' ) {
			return $this->_set_default_controller();
		}

		// Do we need to remove the URL suffix?
		$this->uri->_remove_url_suffix();

		// Compile the segments into an array
		$this->uri->_explode_segments();

		
		// Parse any custom routing that may exist
		$this->uri->osegments  = $this->uri->segments;
		while ( $this->_parse_routes())
		{
			$this->_parse_routes();
		}

		// Re-index the segment array so that it starts with 1 rather than 0
		$this->uri->_reindex_segments();
	}

	/**
	 *  Parse Routes
	 *
	 * This function matches any routes that may exist in
	 * the config/routes.php file against the URI to
	 * determine if the class/method need to be remapped.
	 *
	 * @access	private
	 * @return	void
	 */
	function _parse_routes() {
		// Turn the segment array into a URI string
		$uri = implode( '/', $this->uri->segments );
		// Is there a literal match?  If so we're done
		if ( isset( $this->routes[$uri] ) ) {
			return $this->_set_request( explode( '/', $this->routes[$uri] ) );
		}

		// Loop through the route array looking for wild-cards
		foreach ( $this->routes as $key => $val )
		{
			// Convert wild-cards to RegEx
			$key = str_replace( ':any', '.+', str_replace( ':num', '[0-9]+', $key ) );

			if ( $key[0] == '!' ) {
				$key = substr( $key, 1 );
				$this->recursive = true;
			}

			// Does the RegEx match?
			if ( preg_match( '#^' . $key . '$#', $uri ) ) {
				// Do we have a back-reference?
				if ( strpos( $val, '$' ) !== FALSE AND strpos( $key, '(' ) !== FALSE ) {
					$val = preg_replace( '#^' . $key . '$#', $val, $uri );
				}

				if ( $this->recursive ) {
					$this->uri->segments = explode( '/', $val );
					return true;
				}
				$this->_set_request( explode( '/', $val ) );
				return false;
			}
		}

		// If we got this far it means we didn't encounter a
		// matching route so we'll set the site default route
		$this->_set_request( $this->uri->segments );
		return false;
	}

}
