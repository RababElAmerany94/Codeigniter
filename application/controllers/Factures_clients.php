<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Factures_clients extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->library('grocery_CRUD');
		$this->db = $this->load->database(get_current_db(), true);
		
		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}
	
	function index()
	{
		$this->template->write('title', 'Factures', true);
		$this->template->write('header', 'Factures');
		
		$crud = new grocery_CRUD();
		$crud->set_table('factures_clients');
		$crud->set_subject('Facture');
		
		$columns = [
			'Id_Client',
			'Montant_Facture',
			'Num_Facture',
			'Date_Facture'
		];
		$fields = [
			'Id_Client',
			'Num_Facture',
			'Date_Facture',
			'Num_Carnet',
			'Note',
			'TVA_Facture',
			'Montant_Facture',
			'Reste_Facture',
			'HT_Facture',
			'Quantite',
			'Unite'
		];
		
		if ('read' == $crud->getState()) {
			$columns = [
				'Id_Client',
				'Num_Facture',
				'Date_Facture',
				'Num_Carnet',
				'Note',
				'HT_Facture',
				'TVA_Facture',
				'Reste_Facture',
				'Montant_Facture',
				'Quantite',
				'Unite'
			];
		} elseif ('insert' == $crud->getState() || 'update' == $crud->getState()) {
			$state_info = $crud->getStateInfo();
			$Id_Facture = isset($state_info->primary_key) ? $state_info->primary_key : null;
			$Num_Facture = $state_info->unwrapped_data['Num_Facture'];
			$Date_Facture = DateTime::createFromFormat('d/m/Y', $state_info->unwrapped_data['Date_Facture'])->format('Y');
			
			$sql = "SELECT * FROM factures_clients
               WHERE factures_clients.Num_Facture LIKE '$Num_Facture'
               AND YEAR(factures_clients.Date_Facture) = '$Date_Facture';
           ";
			$query = $this->db->query($sql);
			$row = $query->row_array();

			if ($row != null && ('insert' == $crud->getState() || ('update' == $crud->getState() && $row['Id_Facture'] != $Id_Facture))) {

				echo '{"success":false,"error_message":"<p>Le champ Num Facture est deja exist.<\/p>\n","error_fields":{"Num_Facture":"Le champ Num Facture est deja exist."}}';
				exit();
			}
		} elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
			$fields = [
				'Id_Client',
				'Num_Facture',
				'Date_Facture',
				'Num_Carnet',
				'Note',
				'HT_Facture',
				'TVA_Facture',
				'Montant_Facture',
				'Reste_Facture',
				'Quantite',
				'Unite'
			];
		}
		
		$crud->display_as('Id_Facture', 'Id Facture');
		$crud->display_as('Id_Client', 'Client');
		$crud->display_as('Montant_Facture', 'Montant TTC');
		$crud->display_as('Num_Facture', 'Num Facture');
		$crud->display_as('Num_Carnet', 'Num Carnet');
		$crud->display_as('Date_Facture', 'Date Facture');
		$crud->display_as('TVA_Facture', 'Montant TVA');
		$crud->display_as('HT_Facture', 'Montant HT');
		$crud->display_as('Reste_Facture', 'Reste');
		$crud->display_as('Note', 'Note');
		$crud->display_as('Quantite', 'Quantité');
		$crud->display_as('Unite', 'Unité');
		
		$crud->set_relation('Id_Client', 'clients', '{Id_Client} - {RaisonSociale}', null, 'RaisonSociale ASC');
		
		$crud->order_by('Date_Created', 'DESC');
		
		$crud->field_type('Reste_Facture', 'invisible');
		
		if (!can_list(get_class($this))) {
			$crud->unset_list();
		}
		if (!can_read(get_class($this))) {
			$crud->unset_read();
		}
		if (!can_add(get_class($this))) {
			$crud->unset_add();
		}
		if (!can_edit(get_class($this))) {
			$crud->unset_edit();
		}
		if (!can_delete(get_class($this))) {
			$crud->unset_delete();
		}
		
		
		// add JS for auto calculate TVA (20%)
		$this->template->write('javascript', '
        $("#field-Montant_Facture").on("keypress keyup cut copy paste mousedown mouseup focus blur change", function(event) {
            var TVA_Facture = $(this).val() - ($(this).val() / 1.2);
            TVA_Facture = ceiling(TVA_Facture);
            $("#field-TVA_Facture").val(TVA_Facture);
        });
        ');
		
		//add required field validation
		$crud->required_fields('Id_Client', 'Montant_Facture', 'Num_Facture', 'Date_Facture', 'TVA_Facture', 'Note');
		// load helper for validate all integer fields
		$this->load->helper('validation_helper');
		integer_validation($crud, $this->db->field_data($crud->basic_db_table), $crud->required_fields);
		
		//check if the current URI is not add/edit page, if Not set the User relation --marouane
		$isRead = false;
		if (in_array('read', $this->uri->segment_array())) {
			$isRead = true;
			$crud->set_relation('Id_Creator', 'utilisateurs', '{Prenom} {Nom}');
			$crud->set_relation('Id_Editor', 'utilisateurs', '{Prenom} {Nom}');
		}
		
		//Load Insert Helper for adding Id_Creator, Date_Created, Id_Editor, Date_Edited fields -- marouane
		$this->load->helper('insert_helper');
		$fields_data = insert_addExtrafields($crud, $columns, $fields, $isRead); // get fields data
		//show columns only in the read page
		if ($isRead) {
			$columns = $fields_data['columns'];
		}
		$fields = $fields_data['fields'];
		
		
		// add field HT_Fature adn make it hidden -- Hamza
		$crud->field_type('HT_Facture', 'hidden');
		
		//Set callback when user try to insert or update
		$crud->callback_before_insert(array(
			$this,
			'insert_callback'
		));
		$crud->callback_before_update(array(
			$this,
			'update_callback'
		));
		
		$crud->columns($columns);
		$crud->fields($fields);
		
		
		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
	
	
	function insert_callback($post_array)
	{
		//load insert helper
		$this->load->helper('insert_helper');
		
		//get the current user from session
		$user = $this->session->get_userdata();
		
		$post_array['HT_Facture'] = $post_array['Montant_Facture'] - $post_array['TVA_Facture'];
		$post_array['Reste_Facture'] = $post_array['Montant_Facture'];
		
		//call to the insert_helper
		return insert_helper_callback($post_array, $user['user_id']);
	}
	
	function update_callback($post_array)
	{
		//load insert helper
		$this->load->helper('insert_helper');
		
		//get the current user from session
		$user = $this->session->get_userdata();
		
		$post_array['HT_Facture'] = $post_array['Montant_Facture'] - $post_array['TVA_Facture'];
		$post_array['Reste_Facture'] = $post_array['Montant_Facture'];
		
		//call to the insert_helper
		return update_helper_callback($post_array, $user['user_id']);
	}
}
