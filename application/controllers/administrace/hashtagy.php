<?php

if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * Administracni rozcestnik
 * @author Pavel Vais
 * @property CI_Loader $load
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Email $email
 * @property CI_URI $uri
 * @property CI_DB_active_record $db
 * @property Header $header
 * @property Menu $menu
 * @property Tank_auth $tank_auth
 * @property DbBackup $dbbackup
 * @property Roles $roles
 * @property Cache $cache
 */
class Hashtagy extends My_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library( 'message' );
	}

	public function index()
	{
		$cm = new ConfessionModel();
		$hm = new HashtagsModel();
		$data['count_confessions'] = $cm->count();
		$data['hashtags'] = $hm->count_hashtags_uses();
		$data['count_hashtags'] = $hm->count();
		$this->load->view( 'administrace/hashtagy/view_index', $data );
	}

	public function ukazat($hashtag = null)
	{
		$cm = new ConfessionModel();
		$hm = new HashtagsModel();
		$data['count_confessions'] = $cm->count();
		$data['count_hashtags'] = $hm->count();
		$data['confessions'] = $cm->get_by_hashtag( $hashtag );
		$data['hashtag_name'] = "#" . $hashtag;
		
		if ($data['confessions'] !== FALSE)
		{
		$form = new Form( "administrace/hashtagy/prejmenovat" );
		$form->addText( "rename_hashtag" )
			   ->set_value( $hashtag )
			   ->set_attribute( "class", "span8" )
			   ->set_rule( "nospace", "Hashtag nesmí obsahovat žádnou mezeru." )
			   ->set_rule( "onlychars", "Hashtag musí obsahovat jenom písmena." )
			   ->set_form_attribute( "id", "form-edit" )
			   ->addHidden( "hashtag_id", $data['confessions'][0]->hashtag_id )
			   ->addHidden( "old_hashtag", $hashtag )
			   ->setSubmit( "save", "Uložit přiznání" );
		$data['form'] = $form;
		}
		$this->load->view( 'administrace/hashtagy/view_ukazat', $data );
	}

	public function prejmenovat()
	{
		$old_id = $this->input->post( "hashtag_id" );
		$new_name = $this->input->post( "rename_hashtag" );
		$old_name = $this->input->post( "old_hashtag" );

		if ( $old_name == $new_name )
		{
			$this->session->set_flashdata( "admin", "Hashtag zůstal nezměněn." );
			redirect( 'administrace/hashtagy/ukazat/' . $new_name );
		}

		$hm = new HashtagsModel;
		if ( $hm->rename_over( $old_id, $new_name ) == FALSE )
		{
			//todo udelat rollback!
			$this->session->set_flashdata( "error", "Při přejmenovávání hashtagu došlo k chybě, nic se nezměnilo." );
			redirect( 'administrace/hashtagy/ukazat/' . $old_name );
		}
		else
		{
			$this->session->set_flashdata( "admin", "Úspěšně jste přejmenoval hashtag #$old_name na #$new_name." );
			redirect( 'administrace/hashtagy/ukazat/' . $new_name );
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */