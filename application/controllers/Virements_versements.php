<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Virements_versements extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->library('grocery_CRUD');
        $this->db = $this->load->database(get_current_db(), TRUE);
        
        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index() {
        $this->template->write('title', 'Virements et Versements', TRUE);
        $this->template->write('header', 'Virements et Versements');

        $crud = new grocery_CRUD();
        $crud->set_table('virements_versements');
        $crud->set_subject('Opération');

        $columns = ['Id_Facture','Type_Operation','Num_Operation','Date_Operation','Montant','Observation'];
        $fields = ['Id_Facture','Type_Operation','Num_Operation','Date_Operation','Montant','Observation'];

	    $seuil_facture_non_payee = $this->db->query("SELECT value from settings where settings.key = 'app_SeuilFactureNonPayee'")->result()[0]->value;
	    $crud->set_relation('Id_Facture', 'factures_clients', '{Num_Facture} / {Date_Facture}', 'factures_clients.Reste_Facture > ' . $seuil_facture_non_payee, 'factures_clients.Date_Facture DESC');
	    $crud->order_by('Date_Created', 'DESC');

        if ('read' == $crud->getState()) {
            $columns = ['Id_Facture','Type_Operation','Num_Operation','Date_Operation','Montant','Observation'];
	        $crud->set_relation('Id_Facture','factures_clients','{Num_Facture} / {YEAR(Date_Facture)}');
        } elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
            $fields = ['Id_Facture','Type_Operation','Num_Operation','Date_Operation','Montant','Observation'];
	        $crud->set_relation('Id_Facture','factures_clients','{Num_Facture} / {YEAR(Date_Facture)}', 'factures_clients.Reste_Facture > ' . $seuil_facture_non_payee, 'factures_clients.Date_Facture DESC');
        }

    $crud->display_as('Id_Facture','Facture');
	$crud->display_as('Type_Operation','Type Operation');
	$crud->display_as('Num_Operation','Num Operation');
    $crud->display_as('Date_Operation','Date Operation');
    $crud->display_as('Montant','Montant');
	$crud->display_as('Observation','Observation');

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

       
        $crud->required_fields('Id_Facture','Type_Operation','Num_Operation','Montant');
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
