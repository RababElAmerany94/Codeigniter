<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fournisseurs extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->library('grocery_CRUD');
        $this->db = $this->load->database(get_current_db(), TRUE);
        
        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index() {
        $this->template->write('title', 'Fournisseurs', TRUE);
        $this->template->write('header', 'Fournisseurs');

        $crud = new grocery_CRUD();
        $crud->set_table('fournisseurs');
        $crud->set_subject('Fournisseur');

        $columns = ['RaisonSociale','Ville','Pays','Telephone_1','Email_1'];
        $fields = ['RaisonSociale','Adresse','Ville','Pays','Telephone_1','Telephone_2','Email_1','Email_2','IBAN_1','IBAN_2'];

        if ('read' == $crud->getState()) {
            $columns = ['RaisonSociale','Adresse','Ville','Pays','Telephone_1','Telephone_2','Email_1','Email_2','IBAN_1','IBAN_2'];
        } elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
            $fields = ['RaisonSociale','Adresse','Ville','Pays','Telephone_1','Telephone_2','Email_1','Email_2','IBAN_1','IBAN_2'];
        }
	
	    $crud->order_by('RaisonSociale', 'DESC');
        
        $crud->display_as('RaisonSociale','Raison Sociale');
		$crud->display_as('Adresse','Adresse');
		$crud->display_as('Ville','Ville');
		$crud->display_as('Pays','Pays');
		$crud->display_as('Telephone_1','Telephone 1');
		$crud->display_as('Telephone_2','Telephone 2');
		$crud->display_as('Email_1','Email 1');
		$crud->display_as('Email_2','Email 2');
		$crud->display_as('IBAN_1','IBAN 1');
		$crud->display_as('IBAN_2','IBAN 2');
        
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

        $crud->required_fields('RaisonSociale','Adresse','Ville','Pays','Telephone_1','Email_1');
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
