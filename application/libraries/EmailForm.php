<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * EmailForm Class
 *
 * This class is designet to be quick
 * helper to set up email form (contact form)
 *
 * @category	Libraries
 * @author	Pavel Vais
 * @license	MIT
 * @version	0.1
 */
class EmailForm {

	static $messages = array();
	static public $useLangFile = false;
	static public $siteName = '<Název stránky>';

	/**
	 * Constructor - Initializes and references CI
	 */
	function __construct() {
		if ( self::$useLangFile ) {
			$ci = & get_instance();
			$ci->lang->load( 'emailform' );
		}
		else {
			self::$messages['ef.email'] = 'Email:';
			self::$messages['ef.rule.email.filled'] = 'Email musíte vyplnit.';
			self::$messages['ef.rule.email'] = 'Email musí byt ve správném tvaru.';
			self::$messages['ef.name'] = 'Vaše jméno:';
			self::$messages['ef.message'] = 'Zpráva:';
			self::$messages['ef.rule.message'] = 'Musíte vyplnit vzkaz.';
			self::$messages['ef.send'] = 'Odeslat vzkaz';
		}
	}

	public function send($targetEmail) {
		$ci = & get_instance();
		$name = $ci->input->post( "name" );
		$email = $ci->input->post( "email" );
		$message = $ci->input->post( "message" );
		list($email, $message) = Secure::xss_html( array($email, $message) );

		$ci->load->library( 'email' );
		$ci->email->from( $email, $name . " - Kontaktní formulář" );
		$ci->email->to( $targetEmail );
		$ci->email->bcc( 'vaispavel@gmail.com' );

		$ci->email->subject( 'Zpráva z kontaktního formuláře ' . self::$siteName );
		$ci->email->message( 'Tato zpráva přišla z portálu ' . self::$siteName . "<br> jméno: " . $name . " (email: $email)<br> zpráva: " . $message );

		return $ci->email->send();
	}

	/**
	 * Vrati formular pro kontaktni stranku
	 * @return \Form\Generator
	 */
	static function printContactForm() {
		$form = new \Form\Form( 'homepage/contact_us' );

		$form->addText( 'email', self::getMessage( 'ef.email' ), 50, 50 )
			   ->setRule( \Form\Form::RULE_FILLED, self::getMessage( 'ef.rule.email.filled' ) )
			   ->setRule( \Form\Form::RULE_EMAIL, self::getMessage( 'ef.rule.email' ) )
			   ->addText( 'name', self::getMessage( 'ef.name' ) )
			   ->addTextArea( 'message', self::getMessage( 'ef.message' ) )
			   ->setRule( \Form\Form::RULE_FILLED, self::getMessage( 'ef.rule.message' ) )
			   ->setFormAttribute( 'id', 'er_sender' );

		$form->setSubmit( 'send', self::getMessage( 'ef.send' ) );

		$generator = new Form\Generator( $form );
		return $generator;
	}

	static private function getMessage($param0) {
		if ( self::$useLangFile ) {
			return lang( $param0 );
		}
		else {
			return self::$messages[$param0];
		}
	}

}

/* End of file EmailForm.php */
/* Location: ./application/libraries/EmailForm.php */