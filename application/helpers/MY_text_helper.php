<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

function character_limiter($str, $n = 100, $end_char = '&#8230;', $exact_length = FALSE)
{
	if ( mb_strlen( $str,'utf8' ) < $n )
	{
		return $str;
	}

	$str = preg_replace( "/\s+/", ' ', str_replace( array("\r\n", "\r", "\n"), ' ', $str ) );

	if (  mb_strlen( $str,'utf8' )  <= $n )
	{
		return $str;
	}

	$out = "";
	if ( !$exact_length )
		foreach ( explode( ' ', trim( $str ) ) as $val )
		{
			$out .= $val . ' ';

			if ( strlen( $out ) >= $n )
			{
				$out = trim( $out );
				return (strlen( $out ) == strlen( $str )) ? $out : $out . $end_char;
			}
		}
	else
		return  mb_substr( $str, 0, $n ,'utf8') . $end_char;
}

function character_limiter_html($text, $length, $suffix = '&#8230;', $isHTML = true)
{
	$i = 0;
	$simpleTags = array('br' => true, 'hr' => true, 'input' => true, 'image' => true, 'link' => true, 'meta' => true);
	$tags = array();
	if ( $isHTML )
	{
		preg_match_all( '/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER );
		foreach ( $m as $o )
		{
			if ( $o[0][1] - $i >= $length )
				break;
			$t = substr( strtok( $o[0][0], " \t\n\r\0\x0B>" ), 1 );
			// test if the tag is unpaired, then we mustn't save them
			if ( $t[0] != '/' && (!isset( $simpleTags[$t] )) )
				$tags[] = $t;
			elseif ( end( $tags ) == substr( $t, 1 ) )
				array_pop( $tags );
			$i += $o[1][1] - $o[0][1];
		}
	}

	// output without closing tags
	$output = substr( $text, 0, $length = min( strlen( $text ), $length + $i ) );
	// closing tags
	$output2 = (count( $tags = array_reverse( $tags ) ) ? '</' . implode( '></', $tags ) . '>' : '');

	// Find last space or HTML tag (solving problem with last space in HTML tag eg. <span class="new">)
	$pos = (int) end( end( preg_split( '/<.*>| /', $output, -1, PREG_SPLIT_OFFSET_CAPTURE ) ) );
	// Append closing tags to output
	$output.=$output2;

	// Get everything until last space
	$one = substr( $output, 0, $pos );
	// Get the rest
	$two = substr( $output, $pos, (strlen( $output ) - $pos ) );
	// Extract all tags from the last bit
	preg_match_all( '/<(.*?)>/s', $two, $tags );
	// Add suffix if needed
	if ( strlen( $text ) > $length )
	{
		$one .= $suffix;
	}
	// Re-attach tags
	$output = $one . implode( $tags[0] );

	//added to remove  unnecessary closure
	$output = str_replace( '</!-->', '', $output );

	return $output;
}