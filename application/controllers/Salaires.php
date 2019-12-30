<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Salaires extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->library('grocery_CRUD');
        $this->db = $this->load->database(get_current_db(), TRUE);
        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index() {
        $this->template->write('title', 'Salaires', TRUE);
        $this->template->write('header', 'Salaires');

        $crud = new grocery_CRUD();
        $crud->set_table('salaires');
        $crud->set_subject('Salaire');
        $crud->order_by('Annee, Mois','DESC');

        $columns = ['Mois','Annee','Brut_Mensuel','Nombre_Salaries'];
        $fields = ['Mois','Annee','Brut_Mensuel','Nombre_Salaries'];

        if ('read' == $crud->getState()) {
            $columns = ['Mois','Annee','Brut_Mensuel','Nombre_Salaries'];
        } elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
            $fields = ['Mois','Annee','Brut_Mensuel','Nombre_Salaries'];
        }
	
	    $crud->order_by('Date_Created', 'DESC');
        
        $crud->display_as('Mois','Mois');
		$crud->display_as('Annee','Annee');
		$crud->display_as('Brut_Mensuel','Brut Mensuel');
		$crud->display_as('Nombre_Salaries','Nombre Salaries');
        
        

        $crud->columns($columns);
        $crud->fields($fields);

        if(!can_list(get_class($this))) $crud->unset_list();
        if(!can_read(get_class($this))) $crud->unset_read();
        if(!can_add(get_class($this)))  $crud->unset_add();
        if(!can_edit(get_class($this))) $crud->unset_edit();
        if(!can_delete(get_class($this))) $crud->unset_delete();

        $crud->required_fields('Mois','Annee','Brut_Mensuel','Nombre_Salaries');
        // load helper for validate all integer fields
        $this->load->helper('validation_helper');
        integer_validation($crud, $this->db->field_data($crud->basic_db_table) , $crud->required_fields );
        
        $this->template->write_view('content', 'example', $crud->render());
        $this->template->render();
    }

}
