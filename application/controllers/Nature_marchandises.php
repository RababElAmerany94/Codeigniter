<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nature_marchandises extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->library('grocery_CRUD');
        $this->db = $this->load->database(get_current_db(), TRUE);
        
        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index() {
        $this->template->write('title', 'Nature des Marchandises', TRUE);
        $this->template->write('header', 'Nature des Marchandises');

        $crud = new grocery_CRUD();
        $crud->set_table('nature_marchandises');
        $crud->set_subject('Nature');

        $columns = ['Code_Nature','Description'];
        $fields = ['Code_Nature','Description'];

        if ('read' == $crud->getState()) {
            $columns = ['Code_Nature','Description'];
        } elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
            $fields = ['Code_Nature','Description'];
        }
        
        $crud->display_as('Code_Nature','Code');
		$crud->display_as('Description','Description');
        
        $crud->columns($columns);
        $crud->fields($fields);

        if(!can_list(get_class($this))) $crud->unset_list();
        if(!can_read(get_class($this))) $crud->unset_read();
        if(!can_add(get_class($this)))  $crud->unset_add();
        if(!can_edit(get_class($this))) $crud->unset_edit();
        if(!can_delete(get_class($this))) $crud->unset_delete();
        

        $crud->required_fields('Code_Nature','Description');
        // load helper for validate all integer fields
        $this->load->helper('validation_helper');
        integer_validation($crud, $this->db->field_data($crud->basic_db_table) , $crud->required_fields );
        
        $this->template->write_view('content', 'example', $crud->render());
        $this->template->render();
    }

}
