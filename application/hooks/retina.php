<?php

/**
 */
class Retina
{

	/**
	 * If DPI is more than normal (HD+) but lower than retina displays, system will
	 * use image for HIGH_RESOLUTION monitors. It means that 
	 * DEFAULT_HIGH_POSTFIX will be used instead of DEFAULT_RETINA_POSTFIX;
	 */
	const APPLY_HIGH_RESOLUTION_IMAGES = false;

	/**
	 * If it is TRUE, function isRetina() returns true even if it is 
	 * only HD resolution.
	 */
	const HIGH_RESOLUTION_AS_RETINA = true;

	/**
	 * Postfix for images targeting to HD monitors but not to retina displays
	 */
	const DEFAULT_HIGH_POSTFIX = '_hdx';

	/**
	 * Postfix for images targeting to retina displays
	 */
	const DEFAULT_RETINA_POSTFIX = '_2x';

	/**
	 * Cookie name
	 */
	const COOKIE_NAME = 'ci_devicePixelRation';

	/**
	 * Image folder
	 */
	const DEFAULT_IMG_FOLDER = 'images/';

	/**
	 * Class name appended to img format
	 */
	const DEFAULT_CLASS_NAME = 'retina';

	private static $CI;
	static $ratio;

	public function __construct()
	{
		self::$CI = & get_instance();
	}

	public function init()
	{
		self::$CI->load->helper('htmlelement');
		self::$ratio = self::$CI->input->cookie( self::COOKIE_NAME );
		if ( !self::$ratio )
		{
			self::$ratio = 1;
		}
	}

	/**
	 * Returns current DPI
	 * @return type
	 */
	public static function getDPI()
	{
		return self::$ratio;
	}
	
	/**
	 * Return if it's retina display
	 * @return boolean
	 */
	public static function isRetina()
	{
		return self::$ratio >= (self::HIGH_RESOLUTION_AS_RETINA ? 1.1 : 2);
	}

	/**
	 * Return if it's HD display
	 * @return boolean
	 */
	public static function isHighDPI()
	{
		return self::$ratio > 1 && self::$ratio < 2;
	}

	/**
	 * Return if it's LQ display
	 * @return boolean
	 */
	public static function isLowDPI()
	{
		return self::$ratio <= 1;
	}

	/**
	 * Return IMG html tag
	 * @param String $imgName
	 * @param int $width
	 * @param int $height[null]
	 * @param String $class[null]
	 * @param Array $extraAttributes[null]
	 */
	static function retinaImg($imgName, $width = null, $height = null, $class = null, $extraAttributes = null)
	{

		$class = self::DEFAULT_CLASS_NAME . ($class == null ? '' : ' ' . $class);

		$element = HTMLElement::open( 'img' )->isPair( false )
				  ->addAttribute( 'class', $class );
		if ( $height != null )
		{
			$element->addAttribute( 'height', $height );
		}
		if ( $width != null )
		{
			$element->addAttribute( 'width', $width );
		}
		if ( $extraAttributes != null )
		{
			foreach ( $extraAttributes as $k => $l )
			{
				$element->addAttribute( $k, $l );
			}
		}

		//= URL generation
		return $element->addAttribute( 'src', self::getImageURL( $imgName ) )->generate();
	}

	/**
	 * Retun proper url depends on display resolution
	 * @param String $name
	 * @return String
	 */
	static function getImageURL($name)
	{
		if ( strpos( $name, 'http' ) !== false )
		{
			$path_parts = pathinfo( $name );
		}
		else
		{
			$path_parts = pathinfo( self::DEFAULT_IMG_FOLDER . $name );
		}
		
		$filename = $path_parts['dirname'] . '/' . $path_parts['filename'];
		$extension = $path_parts['extension'];

		if ( self::isRetina() || (!self::APPLY_HIGH_RESOLUTION_IMAGES && self::isHighDPI()) )
		{
			//= Huray! retina resolution will be used!
			return base_url($filename . self::DEFAULT_RETINA_POSTFIX . '.' . $extension);
		}
		else

		if ( self::APPLY_HIGH_RESOLUTION_IMAGES && self::isHighDPI() )
		{
			//= HD display
			return base_url($filename . self::DEFAULT_HIGH_POSTFIX . '.' . $extension);
		}
		else
		{
			//= Its low-res display so normal picture will be used
			return base_url($filename . '.' . $extension);
		}
	}

}