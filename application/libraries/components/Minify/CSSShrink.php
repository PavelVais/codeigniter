<?php
namespace Minify;


defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class CSSShrink
{


	/**
	 * some basic setup
	 *
	 */
	private function compile($jsscript)
	{
		$ch = curl_init( 'http://closure-compiler.appspot.com/compile' );

		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=ADVANCED_OPTIMIZATIONS&js_code=' . urlencode( $script ) );
		$output = curl_exec( $ch );
		curl_close( $ch );

		return $output;
	}

	/**
	 * @param $css
	 */
	public function css($css)
	{
		$this->css_array = $css;
	}

	/**
	 * @param $js
	 */
	public function js($js,$isContent = false)
	{
		$this->js_array = $js;
		$this->is_content = $isContent;
	}

	/**
	 * scan CSS direcctory and look for changes
	 *
	 * @param $type
	 */
	public function scan_files($type)
	{
		switch ($type)
		{
			case 'css':
				$files_array = $this->css_array;
				$directory = $this->css_dir;
				$out_file = $this->css_file;
				break;
		}

		// if multiple files
		if ( is_array( $files_array ) )
		{
			foreach ( $files_array as $file )
			{
				$filename = $directory . '/' . $file;

				if ( file_exists( $filename ) )
				{
					if ( filemtime( $filename ) > $this->_lmod[$type] )
					{
						$this->_concat_files( $files_array, $directory, $out_file );
					}
				}
				else
				{
					die( "File {$filename} is missing" );
				}
			}
		}
	}

	/**
	 * add merge files
	 *
	 * @param string $file_array input file array
	 * @param        $directory
	 * @param string $out_file   output file
	 *
	 * @internal param string $filename file name
	 */
	private function _concat_files($file_array, $directory, $out_file)
	{


		if ( $fh = fopen( $out_file, 'w' ) )
		{
			foreach ( $file_array as $file_name )
			{

				$file_name = $directory . '/' . $file_name;
				$handle = fopen( $file_name, 'r' );
				$contents = fread( $handle, filesize( $file_name ) );
				fclose( $handle );

				fwrite( $fh, $contents );
			}
			fclose( $fh );
		}
		else
		{
			die( "Can't write to {$out_file}" );
		}

		if ( $this->compress )
		{
			$handle = fopen( $out_file, 'r' );
			$contents = fread( $handle, filesize( $file_name ) );
			fclose( $handle );

			$handle = fopen( $out_file, 'w' );
			//fwrite($handle, $this->_process($contents));
			fwrite( $handle, $this->_new_process( $contents ) );
			fclose( $handle );
		}
	}

	/**
	 * grab js files into one file
	 */
	public function join_js()
	{
		$js = $this->js_array;
		
		if ($this->is_content)
		{
			$this->_merge_js( $this->js_file,true );
		}
		
		if ( file_exists( $this->js_file ) )
		{
			$x = filemtime( $this->js_file );
		}
		else
		{
			$x = 0;
		}

		$flag = FALSE; // flag to check if any of the file was changed to rebuild all the set of files
		if ( is_array( $js ) )
		{
			foreach ( $js as $j )
			{
				$filename = $this->js_dir . '/' . $j;
				if ( file_exists( $filename ) && filemtime( $filename ) > $x )
				{
					$flag = TRUE;
					break;
				}
			}
			if ( !$flag )
				return; // nothing was changed
			@unlink( $this->js_file );
			foreach ( $js as $j )
			{
				$filename = $this->js_dir . '/' . $j;
				if ( file_exists( $filename ) )
				{
					$this->_merge_js( $filename );
				}
			}
		}
		else
		{
			$filename = $this->css_dir . "/" . $js;
			if ( file_exists( $filename ) && filemtime( $filename ) > $x )
			{
				@unlink( $this->js_file );
				$this->_merge_js( $filename );
			}
		}
	}

	/**
	 * deploy and minify CSS
	 *
	 * @return mixed
	 */
	public function deploy_css()
	{

		$this->scan_files( 'css' );

		$this->ci->load->helper( 'html' );

		return link_tag( $this->css_file );
	}

	/**
	 * @param bool $refresh
	 *
	 * @return string
	 */
	public function deploy_js($refresh = FALSE)
	{
		if ( $refresh )
		{
			$this->join_js();
		}

		return "<script type=\"text/javascript\" src=\"" . base_url() . '/' . $this->js_file . "\"></script>";
	}

	/**
	 * join all js files into one big file
	 *
	 * @param string $filename name of source file
	 * @param string $isContent je ten file uz kod nebo odkaz na soubor?
	 */
	private function _merge_js($filename, $isContent = false)
	{
		if ( $isContent )
		{
			$handle = fopen( $filename, "r" );
			$contents = fread( $handle, filesize( $filename ) );
			fclose( $handle );
		}
		else
		{
			$contents = $filename;
		}

		if ( $this->compress )
		{
			$contents = $this->_compress_js( $contents );
		}

		$fh = fopen( $this->js_file, 'a' );
		fwrite( $fh, $contents );
		fclose( $fh );
	}

	/**
	 * @param $script
	 *
	 * @return mixed
	 */
	private function _compress_js($script)
	{
		
	}

	private function _new_process($data)
	{
		include_once('cssmin-v3.0.1.php');
		return CssMin::minify( $data );
	}

	/**
	 * @package  Minify
	 * @authohor Stephen Clay <steve@mrclay.org>
	 * @author   http://code.google.com/u/1stvamp/ (Issue 64 patch)
	 */
	private function _process($css)
	{
		$css = str_replace( "\r\n", "\n", $css );

		// preserve empty comment after '>'
		// http://www.webdevout.net/css-hacks#in_css-selectors
		$css = preg_replace( '@>/\\*\\s*\\*/@', '>/*keep*/', $css );

		// preserve empty comment between property and value
		// http://css-discuss.incutio.com/?page=BoxModelHack
		$css = preg_replace( '@/\\*\\s*\\*/\\s*:@', '/*keep*/:', $css );
		$css = preg_replace( '@:\\s*/\\*\\s*\\*/@', ':/*keep*/', $css );

		// apply callback to all valid comments (and strip out surrounding ws
		$css = preg_replace_callback( '@\\s*/\\*([\\s\\S]*?)\\*/\\s*@'
				  , array($this, '_commentCB'), $css );

		// remove ws around { } and last semicolon in declaration block
		$css = preg_replace( '/\\s*{\\s*/', '{', $css );
		$css = preg_replace( '/;?\\s*}\\s*/', '}', $css );

		// remove ws surrounding semicolons
		$css = preg_replace( '/\\s*;\\s*/', ';', $css );

		// remove ws around urls
		$css = preg_replace( '/
                url\\(      # url(
                \\s*
                ([^\\)]+?)  # 1 = the URL (really just a bunch of non right parenthesis)
                \\s*
                \\)         # )
            /x', 'url($1)', $css );

		// remove ws between rules and colons
		$css = preg_replace( '/
                \\s*
                ([{;])              # 1 = beginning of block or rule separator
                \\s*
                ([\\*_]?[\\w\\-]+)  # 2 = property (and maybe IE filter)
                \\s*
                :
                \\s*
                (\\b|[#\'"-])        # 3 = first character of a value
            /x', '$1$2:$3', $css );

		// remove ws in selectors
		$css = preg_replace_callback( '/
                (?:              # non-capture
                    \\s*
                    [^~>+,\\s]+  # selector part
                    \\s*
                    [,>+~]       # combinators
                )+
                \\s*
                [^~>+,\\s]+      # selector part
                {                # open declaration block
            /x'
				  , array($this, '_selectorsCB'), $css );

		// minimize hex colors
		$css = preg_replace( '/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i'
				  , '$1#$2$3$4$5', $css );

		// remove spaces between font families
		$css = preg_replace_callback( '/font-family:([^;}]+)([;}])/'
				  , array($this, '_fontFamilyCB'), $css );

		$css = preg_replace( '/@import\\s+url/', '@import url', $css );

		// replace any ws involving newlines with a single newline
		$css = preg_replace( '/[ \\t]*\\n+\\s*/', "\n", $css );

		// separate common descendent selectors w/ newlines (to limit line lengths)
		$css = preg_replace( '/([\\w#\\.\\*]+)\\s+([\\w#\\.\\*]+){/', "$1\n$2{", $css );

		// Use newline after 1st numeric value (to limit line lengths).
		$css = preg_replace( '/
            ((?:padding|margin|border|outline):\\d+(?:px|em)?) # 1 = prop : 1st numeric value
            \\s+
            /x'
				  , "$1\n", $css );

		// prevent triggering IE6 bug: http://www.crankygeek.com/ie6pebug/
		$css = preg_replace( '/:first-l(etter|ine)\\{/', ':first-l$1 {', $css );

		return trim( $css );
	}

	/**
	 * Replace what looks like a set of selectors
	 *
	 * @param array $m regex matches
	 *
	 * @return string
	 */
	protected function _selectorsCB($m)
	{
		// remove ws around the combinators
		return preg_replace( '/\\s*([,>+~])\\s*/', '$1', $m[0] );
	}

	/**
	 * Process a comment and return a replacement
	 *
	 * @param array $m regex matches
	 *
	 * @return string
	 */
	protected function _commentCB($m)
	{
		$hasSurroundingWs = (trim( $m[0] ) !== $m[1]);
		$m = $m[1];
		// $m is the comment content w/o the surrounding tokens,
		// but the return value will replace the entire comment.
		if ( $m === 'keep' )
		{
			return '/**/';
		}
		if ( $m === '" "' )
		{
			// component of http://tantek.com/CSS/Examples/midpass.html
			return '/*" "*/';
		}
		if ( preg_match( '@";\\}\\s*\\}/\\*\\s+@', $m ) )
		{
			// component of http://tantek.com/CSS/Examples/midpass.html
			return '/*";}}/* */';
		}
		if ( $this->_inHack )
		{
			// inversion: feeding only to one browser
			if ( preg_match( '@
                    ^/               # comment started like /*/
                    \\s*
                    (\\S[\\s\\S]+?)  # has at least some non-ws content
                    \\s*
                    /\\*             # ends like /*/ or /**/
                @x', $m, $n )
			)
			{
				// end hack mode after this comment, but preserve the hack and comment content
				$this->_inHack = FALSE;

				return "/*/{$n[1]}/**/";
			}
		}
		if ( substr( $m, - 1 ) === '\\' )
		{ // comment ends like \*/
			// begin hack mode and preserve hack
			$this->_inHack = TRUE;

			return '/*\\*/';
		}
		if ( $m !== '' && $m[0] === '/' )
		{ // comment looks like /*/ foo */
			// begin hack mode and preserve hack
			$this->_inHack = TRUE;

			return '/*/*/';
		}
		if ( $this->_inHack )
		{
			// a regular comment ends hack mode but should be preserved
			$this->_inHack = FALSE;

			return '/**/';
		}
		// Issue 107: if there's any surrounding whitespace, it may be important, so
		// replace the comment with a single space
		return $hasSurroundingWs // remove all other comments
				  ? ' ' : '';
	}

	/**
	 * Process a font-family listing and return a replacement
	 *
	 * @param array $m regex matches
	 *
	 * @return string
	 */
	protected function _fontFamilyCB($m)
	{
		$m[1] = preg_replace( '/
                \\s*
                (
                    "[^"]+"      # 1 = family in double qutoes
                    |\'[^\']+\'  # or 1 = family in single quotes
                    |[\\w\\-]+   # or 1 = unquoted family
                )
                \\s*
            /x', '$1', $m[1] );

		return 'font-family:' . $m[1] . $m[2];
	}

}
