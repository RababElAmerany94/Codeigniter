<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Banques extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->library('grocery_CRUD');
        $this->db = $this->load->database(get_current_db(), TRUE);
        
        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index() {
        $this->template->write('title', 'Banques', TRUE);
        $this->template->write('header', 'Banques');

        $crud = new grocery_CRUD();
        $crud->set_table('banques');
        $crud->set_subject('Banque');

        $columns = ['Nom_Banque','Telephone','Adresse','Ville','Pays'];
        $fields = ['Nom_Banque','Code_Banque','Telephone','Fax','Email','Adresse','Ville','Pays','Taux'];

        if ('read' == $crud->getState()) {
            $columns = ['Nom_Banque','Telephone','Fax','Email','Adresse','Ville','Pays','Taux','Code_Banque'];
        } elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
            $fields = ['Nom_Banque','Code_Banque','Telephone','Fax','Email','Adresse','Ville','Pays','Taux'];
        }
        
        $crud->display_as('Nom_Banque','Nom Banque');
        $crud->display_as('Code_Banque','Code Banque');
		$crud->display_as('Telephone','Telephone');
		$crud->display_as('Fax','Fax');
		$crud->display_as('Email','Email');
		$crud->display_as('Adresse','Adresse');
		$crud->display_as('Ville','Ville');
		$crud->display_as('Pays','Pays');
		$crud->display_as('Taux','Taux');
        //Make taux field as Integer
        $crud->field_type('Taux' , 'integer');

        $crud->columns($columns);
        $crud->fields($fields);

        if(!can_list(get_class($this))) $crud->unset_list();
        if(!can_read(get_class($this))) $crud->unset_read();
        if(!can_add(get_class($this)))  $crud->unset_add();
        if(!can_edit(get_class($this))) $crud->unset_edit();
        if(!can_delete(get_class($this))) $crud->unset_delete();

        
        $crud->required_fields('Nom_Banque','Taux');
        // load helper for validate all integer fields
        $this->load->helper('validation_helper');
        integer_validation($crud, $this->db->field_data($crud->basic_db_table) , $crud->required_fields );
        
        $this->template->write_view('content', 'example', $crud->render());
        $this->template->render();
    }

}
