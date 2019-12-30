<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Effets_clients extends CI_Controller
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
		
		$this->template->write('title', 'Effets Clients', TRUE);
		$this->template->write('header', 'Effets Clients');
		
		$crud = new grocery_CRUD();
		$crud->set_table('effets_clients');
		$crud->set_subject('Effet');
		
		$columns = ['Num_Effet', 'Id_Facture', 'Id_Banque', 'Date_Echeance', 'Date_Paiement', 'Montant', 'Tire'];
		$fields = ['Num_Effet', 'Id_Facture', 'Id_Banque', 'Num_Bordereau', 'Date_Echeance', 'Date_Paiement', 'Date_Valeur', 'Montant', 'Tire', 'Endosseur', 'Observation', 'Etat_Effet', 'Ville'];
		
		$seuil_facture_non_payee = $this->db->query("SELECT value from settings where settings.key = 'app_SeuilFactureNonPayee'")->result()[0]->value;
		$crud->set_relation('Id_Facture', 'factures_clients', '{Num_Facture} / {Date_Facture}', 'factures_clients.Reste_Facture > ' . $seuil_facture_non_payee, 'factures_clients.Date_Facture DESC');
		$crud->order_by('Date_Created', 'DESC');
		
		$crud->field_type('Num_Bordereau', 'hidden');
		
		if ('read' == $crud->getState()) {
			$columns = ['Num_Effet', 'Id_Facture', 'Num_Bordereau', 'Num_Repartition', 'Id_Banque', 'Date_Echeance', 'Date_Paiement', 'Date_Valeur', 'Montant', 'Tire', 'Endosseur', 'Observation', 'Etat_Effet', 'Banque_Etat', 'Ville'];
			$crud->set_relation('Id_Facture', 'factures_clients', '{Num_Facture} / {YEAR(Date_Facture)}');
			$crud->field_type('Num_Bordereau', 'string');
		} elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
			$fields = ['Num_Effet', 'Id_Facture', 'Num_Bordereau', 'Id_Banque', 'Date_Echeance', 'Date_Paiement', 'Date_Valeur', 'Montant', 'Tire', 'Endosseur', 'Observation', 'Etat_Effet', 'Ville'];
			$crud->set_relation('Id_Facture', 'factures_clients', '{Num_Facture} / {YEAR(Date_Facture)}', 'factures_clients.Reste_Facture > ' . $seuil_facture_non_payee, 'factures_clients.Date_Facture DESC');
			$crud->field_type('Num_Bordereau', 'string');
		}
		
		$crud->display_as('Num_Effet', 'Num Effet');
		$crud->display_as('Id_Effet', 'Id Effet');
		$crud->display_as('Id_Facture', 'Facture');
		$crud->display_as('Num_Repartition', 'Num Repartition');
		$crud->display_as('Id_Banque', 'Banque');
		$crud->display_as('Date_Echeance', 'Date Echeance');
		$crud->display_as('Date_Paiement', 'Date Paiement');
		$crud->display_as('Date_Valeur', 'Date Valeur');
		$crud->display_as('Montant', 'Montant');
		$crud->display_as('Tire', 'Tire');
		$crud->display_as('Endosseur', 'Endosseur');
		$crud->display_as('Observation', 'Observation');
		$crud->display_as('Etat_Effet', 'Etat Effet');
		$crud->display_as('Ville', 'Ville');
		
		$crud->set_relation('Id_Banque', 'banques', 'Nom_Banque');
		
		$crud->order_by('Date_Created', 'DESC');
		
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
		
		//Set callback when user try to insert or update
		$crud->callback_before_insert(array($this, 'insert_callback'));
		$crud->callback_after_insert(array($this, 'set_num_repartition'));
		$crud->callback_after_update(array($this, 'set_num_repartition'));
		$crud->callback_before_update(array($this, 'update_callback'));
		
		$crud->columns($columns);
		$crud->fields($fields);
		
		if (!can_list(get_class($this))) $crud->unset_list();
		if (!can_read(get_class($this))) $crud->unset_read();
		if (!can_add(get_class($this))) $crud->unset_add();
		if (!can_edit(get_class($this))) $crud->unset_edit();
		if (!can_delete(get_class($this))) $crud->unset_delete();
		
		
		$crud->required_fields('Id_Facture', 'Id_Client', 'Id_Banque', 'Date_Echeance', 'Montant', 'Tire');
		// load helper for validate all integer fields
		$this->load->helper('validation_helper');
		integer_validation($crud, $this->db->field_data($crud->basic_db_table), $crud->required_fields);
		
		//load custom javascript
		$this->template->write('javascript', $this->custom_javascript());
		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
	
	function insert_callback($post_array)
	{
		//load insert helper
		$this->load->helper('insert_helper');
		//get the current user from session
		$user = $this->session->get_userdata();
		//call to the insert_helper
		$post_data = insert_helper_callback($post_array, $user['user_id']);
		
		//SET NUM BORDERAU
		//First get the max num bordereau for the current Year
		$sql = "SELECT max(e.Num_Bordereau) as 'Num_Bordereau'
                from effets_clients e 
                where YEAR(e.Date_Created)= YEAR(CURDATE())";
		
		$query = $this->db->query($sql);
		$result = $query->result_array();
		
		$num_bodereau = $result[0]['Num_Bordereau'];
		if (is_null($num_bodereau)) {
			$num_bodereau = 1;
		}
		
		//check if today has a num bordereau
		$sql = "SELECT max(e.Num_Bordereau) as 'Num_Bordereau'
               from effets_clients e 
               where YEAR(e.Date_Created)= YEAR(CURDATE())
               and MONTH(e.Date_Created) =  MONTH(CURDATE())
               and DAY(e.Date_Created) =  DAY(CURDATE())";
		
		$query = $this->db->query($sql);
		$result = $query->result_array();
		$today_num_bodereau = $result[0]['Num_Bordereau'];
		
		//if today num_borderau is null , and it's not a new year, increment num_burderau by 1
		if (is_null($today_num_bodereau)) {
			//check if this Year has already a Num_bordereau
			$sql = "SELECT max(e.Num_Bordereau) as 'Num_Bordereau'
                    from effets_clients e 
                    where YEAR(e.Date_Created)= YEAR(CURDATE())";
			$query = $this->db->query($sql);
			$result = $query->result_array();
			
			// if it's null, it means it's a new year
			if (is_null($result[0]['Num_Bordereau'])) {
				$num_bodereau = 1;
			} else { // else incrument today num bordereau by 1
				$num_bodereau += 1;
			}
			
		}
		
		//set num_bordereau to the new value
		$post_data['Num_Bordereau'] = $num_bodereau;
		
		return $post_data;
	}
	
	function set_num_repartition($post_array)
	{
		//get num_repartition
		$sql = " SELECT `Id_Effet`, `Num_Bordereau`, `Num_Repartition`, `Date_Echeance` , `Date_Created`
                from effets_clients e 
                where YEAR(e.Date_Created)= YEAR(CURDATE())
                and Num_Bordereau = " . $post_array['Num_Bordereau'] . " order by e.`Date_Echeance`";
		
		$query = $this->db->query($sql);
		$results = $query->result_array();
		
		//init num repartition
		$num_Repartition = 0;
		$previous_month = null;
		for ($i = 0; $i < count($results); $i++) {
			$current_month = date("m", strtotime($results[$i]['Date_Echeance']));
			if ($previous_month != $current_month) {
				$num_Repartition++; //increment num repartiion
			}
			$sql = "UPDATE `effets_clients` set `Num_Repartition` = $num_Repartition  where `Id_Effet` = " . $results[$i]['Id_Effet'];
			$query = $this->db->query($sql);
			
			$previous_month = date("m", strtotime($results[$i]['Date_Echeance']));
		}
		return $post_array;
	}
	
	function update_callback($post_array)
	{
		//load insert helper
		$this->load->helper('insert_helper');
		//get the current user from session
		$user = $this->session->get_userdata();
		//call to the update_helper
		$post_array = update_helper_callback($post_array, $user['user_id']);
		
		return $post_array;
	}
	
	public function custom_javascript()
	{
		$project_url = $this->config->base_url();
		$isadd = false;
		if (in_array('add', $this->uri->segment_array())) {
			$isadd = true;
			
		return '
            $(document).ready(function() {
                //determine if the field_Id_Facture_chosen is selected
                var selected_code = null;
                
                //first check if Nature code is selected
                if($("#field-Id_Facture").val()) {
                    selected_code = $("#field-Id_Facture").val();
                    fetch_tire_name();
                } else {
                    //if tire is not selected, clear the field
                     clear_tire_field();
                }

                // make ajax request when user select another facture
                $("#field-Id_Facture").change(function(){
                    fetch_tire_name();
                });
                
                //function to clear tire field
                function clear_tire_field() {
                    $("#field-Tire").val("");
                }

                //function to fetch tire name
                function fetch_tire_name() {
                    $.ajax({
                        beforeSend : function() {
                            //before send request, hide the dropdown and display loading
                            $("#field-Tire").val(`chargement...`);
                        },
                        url :"' . $project_url . '/effets_clients/get_tire_name?id_facture="+$("#field-Id_Facture").val(),
                        success :function(response){
                            $("#field-Tire").val(response);
                        }
                    });
                }
            });
            ';
            		}
            		else{
            			return null;
            		}
	}
	
	public function get_tire_name()
	{
		$id_facture = $this->input->get('id_facture', TRUE);
		
		$sql = "SELECT `clients`.`RaisonSociale` as 'name'
               FROM clients, factures_clients
               WHERE clients.Id_Client = factures_clients.Id_Client
               AND factures_clients.Id_Facture = $id_facture";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		
		header('Content-Type: application/json');
		echo json_encode($row['name'], true);
		exit(0);
	}
}
