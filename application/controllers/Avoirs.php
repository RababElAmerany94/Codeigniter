<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Avoirs extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->library('grocery_CRUD');
		$this->db = $this->load->database(get_current_db(), TRUE);
        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index() {
        $this->template->write('title', 'Avoirs', TRUE);
        $this->template->write('header', 'Avoirs');

        $crud = new grocery_CRUD();
        $crud->set_table('avoirs');
        $crud->set_subject('Avoir');

        $columns = ['Num_Avoir','Date_Avoir','Total_Avoir','Observation'];
        $fields = ['Num_Avoir','Date_Avoir','Total_Avoir','Observation'];

        if ('read' == $crud->getState()) {
            $columns = ['Id_Avoir','Num_Avoir','Date_Avoir','Total_Avoir','Observation','Id_Creator','Date_Created','Id_Editor','Date_Edited'];
        } elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
            $fields = ['Num_Avoir','Date_Avoir','Total_Avoir','Observation'];
        }
        
        $crud->display_as('Id_Avoir','Id Avoir');
			$crud->display_as('Num_Avoir','Num Avoir');
			$crud->display_as('Date_Avoir','Date');
			$crud->display_as('Total_Avoir','Total');
			$crud->display_as('Observation','Observation');
			$crud->display_as('Id_Creator','Id Creator');
			$crud->display_as('Date_Created','Date Created');
			$crud->display_as('Id_Editor','Id Editor');
			$crud->display_as('Date_Edited','Date Edited');
        
        

        $crud->columns($columns);
        $crud->fields($fields);

		
        if(!can_list(get_class($this))) $crud->unset_list();
        if(!can_read(get_class($this))) $crud->unset_read();
        if(!can_add(get_class($this)))  $crud->unset_add();
        if(!can_edit(get_class($this))) $crud->unset_edit();
        if(!can_delete(get_class($this))) $crud->unset_delete();
        
        $crud->required_fields('Num_Avoir','Date_Avoir','Total_Avoir');
        $this->load->helper('validation_helper');
        integer_validation($crud, $this->db->field_data($crud->basic_db_table) , $crud->required_fields );
        $this->template->write_view('content', 'example', $crud->render());
        $this->template->render();
    }

}
