<?php

namespace Minify;

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class JSShrink
{

	/**
	 * some basic setup
	 *
	 */
	public function compile($script, $advMode = false)
	{
		$ch = curl_init( 'http://closure-compiler.appspot.com/compile' );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );

		if ( $advMode )
		{
			curl_setopt( $ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&externs_url=http://closure-compiler.googlecode.com/svn/trunk/contrib/externs/jquery-1.8.js&output_format=text&compilation_level=ADVANCED_OPTIMIZATIONS&js_code=' . urlencode( $script ) );
		}
		else
		{
			curl_setopt( $ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=SIMPLE_OPTIMIZATIONS&js_code=' . urlencode( $script ) );
		}
		$output = curl_exec( $ch );
		curl_close( $ch );

		return $output;
	}
	
	public function combine_files($files_paths)
	{
		
	}

}
