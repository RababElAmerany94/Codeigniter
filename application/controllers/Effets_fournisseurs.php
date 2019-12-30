<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Effets_fournisseurs extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->library('grocery_CRUD');
		$this->db = $this->load->database(get_current_db(), TRUE);
		
		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}
	
	function index() {
		$this->template->write('title', 'Effets Fournisseurs', TRUE);
		$this->template->write('header', 'Effets Fournisseurs');
		
		$crud = new grocery_CRUD();
		$crud->set_table('effets_fournisseurs');
		$crud->set_subject('Effet');
		
		$columns = ['Num_Effet','Id_Fournisseur','Id_Achat','Id_Banque','Date_Echeance'];
		$fields = ['Num_Effet','Id_Fournisseur','Id_Achat','Id_Banque','Date_Echeance','Date_Acceptation'];
		
		if ('read' == $crud->getState()) {
			$columns = ['Num_Effet','Id_Fournisseur','Id_Achat','Id_Banque','Date_Echeance','Montant_MAD','Date_Acceptation'];
		} elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
			$fields = ['Num_Effet','Id_Fournisseur','Id_Achat','Id_Banque','Date_Echeance','Montant_MAD','Date_Acceptation'];
		}
		
		$crud->order_by('Date_Created', 'DESC');
		
		$crud->display_as('Num_Effet','Num Effet');
		$crud->display_as('Id_Fournisseur','Fournisseur');
		$crud->display_as('Id_Achat','Achat');
		$crud->display_as('Id_Banque','Banque');
		$crud->display_as('Date_Echeance','Date Echeance');
		$crud->display_as('Montant_MAD','Montant Mad');
		$crud->display_as('Date_Acceptation','Date Acceptation');
		
		$seuil_affichage_achats_en_mois = $this->db->query("SELECT value from settings where settings.key = 'app_SeuilAffichageAchatsEnMois'")->result()[0]->value;
		$crud->set_relation('Id_Achat','achats_locaux','{Num_Facture} - {Date_Facture}', "DATE_SUB(CURRENT_DATE, INTERVAL $seuil_affichage_achats_en_mois MONTH) <= Date_Created", "Date_Facture DESC");
		$crud->set_relation('Id_Fournisseur','fournisseurs','RaisonSociale');
		$crud->set_relation('Id_Banque','banques','Nom_Banque');
		
		//check if the current URI is not add/edit page, if Not set the User relation --marouane
		$isRead = false;
		if(in_array('read',$this->uri->segment_array())){
			$isRead = true;
			$crud->set_relation('Id_Creator', 'utilisateurs', '{Prenom} {Nom}');
			$crud->set_relation('Id_Editor', 'utilisateurs', '{Prenom} {Nom}');
		}
		
		//Load Insert Helper for adding Id_Creator, Date_Created, Id_Editor, Date_Edited fields -- marouane
		$this->load->helper('insert_helper');
		$fields_data = insert_addExtrafields($crud,$columns,$fields,$isRead); // get fields data
		//show columns only in the read page
		if($isRead) {
			$columns = $fields_data['columns'];
		}
		$fields = $fields_data['fields'];
		
		//Set callback when user try to insert or update
		$crud->callback_before_insert(array($this, 'insert_callback'));
		$crud->callback_before_update(array($this, 'update_callback'));
		
		$crud->columns($columns);
		$crud->fields($fields);
		
		if(!can_list(get_class($this))) $crud->unset_list();
		if(!can_read(get_class($this))) $crud->unset_read();
		if(!can_add(get_class($this)))  $crud->unset_add();
		if(!can_edit(get_class($this))) $crud->unset_edit();
		if(!can_delete(get_class($this))) $crud->unset_delete();
		
		
		$crud->required_fields('Num_Effet','Id_Fournisseur','Id_Achat','Id_Banque','Date_Echeance','Montant_MAD');
		// load helper for validate all integer fields
		$this->load->helper('validation_helper');
		integer_validation($crud, $this->db->field_data($crud->basic_db_table) , $crud->required_fields );
		
		$this->template->write_view('content', 'example', $crud->render());
		
		$this->template->render();
	}
	
	function insert_callback($post_array){
		//load insert helper
		$this->load->helper('insert_helper');
		//get the current user from session
		$user = $this->session->get_userdata();
		//call to the insert_helper
		return insert_helper_callback($post_array,$user['user_id']);
	}
	
	function update_callback($post_array){
		//load insert helper
		$this->load->helper('insert_helper');
		//get the current user from session
		$user = $this->session->get_userdata();
		//call to the insert_helper
		return update_helper_callback($post_array,$user['user_id']);
	}
	
}
