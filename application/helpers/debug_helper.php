<?php

/**
 * Debug Helper
 *
 * Outputs the given variable(s) with formatting and location
 *
 * @access        public
 * @param        mixed    variables to be output
 */
function dump()
{
	$args = func_get_args();

	$backtrace = debug_backtrace();
	$code = file( $backtrace[0]['file'] );
	list($trace) = $backtrace;
	echo '<fieldset class="php-debug" style="background: #fefefe !important; border:2px red solid; padding:5px">';
	echo '<legend style="background:lightgrey; padding:5px;">' . $trace['file'] . ' @ line: ' . $trace['line'] . '</legend><pre>';
	echo "<pre style='background: #eee; border: 1px solid #aaa; clear: both; overflow: auto; padding: 10px; text-align: left; margin-bottom: 5px'>";

	echo "<b>" . htmlspecialchars( trim( $code[$backtrace[0]['line'] - 1] ) ) . "</b>\n";

	echo "\n";

	ob_start();
	$i = 0;
	foreach ( $args as $arg )
	{
		echo '<br/><strong>Debug #' . (++$i) . ' of ' . count( $args ) . '</strong>: ';
		var_dump( $arg );
	}

	$str = ob_get_contents();

	ob_end_clean();

	$str = preg_replace( '/=>(\s+)/', ' => ', $str );
	$str = preg_replace( '/ => NULL/', ' &rarr; <b style="color: #000">NULL</b>', $str );
	$str = preg_replace( '/}\n(\s+)\[/', "}\n\n" . '$1[', $str );
	$str = preg_replace( '/ (float|int)\((\-?[\d\.]+)\)/', " <span style='color: #888'>$1</span> <b style='color: brown'>$2</b>", $str );

	$str = preg_replace( '/array\((\d+)\) {\s+}\n/', "<span style='color: #888'>array&bull;$1</span> <b style='color: brown'>[]</b>", $str );
	$str = preg_replace( '/ string\((\d+)\) \"(.*)\"/', " <span style='color: #888'>str&bull;$1</span> <b style='color: brown'>'$2'</b>", $str );
	$str = preg_replace( '/\[\"(.+)\"\] => /', "<span style='color: purple'>'$1'</span> &rarr; ", $str );
	$str = preg_replace( '/object\((\S+)\)#(\d+) \((\d+)\) {/', "<span style='color: #888'>obj&bull;$2</span> <b style='color: #0C9136'>$1[$3]</b> {", $str );
	$str = str_replace( "bool(false)", "<span style='color:#888'>bool&bull;</span><span style='color: red'>false</span>", $str );
	$str = str_replace( "bool(true)", "<span style='color:#888'>bool&bull;</span><span style='color: green'>true</span>", $str );

	echo $str;

	echo "</pre>";
	echo "</fieldset>";
}
