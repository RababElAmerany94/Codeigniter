<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Taux_conversion extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->library('grocery_CRUD');
        $this->db = $this->load->database(get_current_db(), TRUE);

        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index()
    {
        $this->template->write('title', 'Taux de Conversion m/kg', TRUE);
        $this->template->write('header', 'Taux de Conversion m/kg');

        $crud = new grocery_CRUD();
        $crud->set_table('taux_conversion_m_kg');
        $crud->set_subject('Taux de Conversion');

        $crud->columns(['Annee', 'Taux']);
        $crud->fields(['Annee', 'Taux']);
	
	    $crud->order_by('Date_Created', 'DESC');
        
        $crud->display_as('Annee', 'Annee');
        $crud->display_as('Taux', 'Taux (1 mètre)');

        if (!can_list(get_class($this))) $crud->unset_list();
        if (!can_read(get_class($this))) $crud->unset_read();
        if (!can_add(get_class($this))) $crud->unset_add();
        if (!can_edit(get_class($this))) $crud->unset_edit();
        if (!can_delete(get_class($this))) $crud->unset_delete();

        $crud->required_fields('Annee', 'Taux');
        // load helper for validate all integer fields
        $this->load->helper('validation_helper');
        integer_validation($crud, $this->db->field_data($crud->basic_db_table), $crud->required_fields);

        $this->template->write('javascript', "
        $('#Taux_input_box').append(' Kg');
        ");

        $this->template->write_view('content', 'example', $crud->render());
        $this->template->render();
    }
}
