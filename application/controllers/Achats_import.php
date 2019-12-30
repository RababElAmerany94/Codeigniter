<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Achats_import extends CI_Controller
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
		$crud = new grocery_CRUD();
		$crud->set_subject('Achat');
		$crud->set_table('achats_import');
		
		$seuil_affichage_engagements_en_mois = $this->db->query("SELECT value from settings where settings.key = 'app_SeuilAffichageEngagementsEnMois'")->result()[0]->value;
		$crud->set_relation_n_n('engagements', 'import_engagements_importation', 'engagements_importation', 'import_id', 'engagement_id', '{Num_Engagement} - {Montant} - {Date}', null, "DATE_SUB(CURRENT_DATE, INTERVAL $seuil_affichage_engagements_en_mois MONTH) <= engagements_importation.Date_Created");
		
		$columns = ['Num_Facture', 'Date_Facture', 'Id_Fournisseur', 'Num_DUM', 'Origine', 'Id_Nature', 'Id_Code', 'Montant_Devise'];
		$fields = ['Num_Facture', 'Date_Facture', 'Id_Fournisseur', 'Num_DUM', 'Date_DUM', 'Origine', 'Marchandises', 'Poids_Net', 'Poids_Brut', 'Id_Nature', 'Id_Code', 'Montant_Devise', 'Devise', 'Taux_Change', 'Num_Quittance', 'Date_Quittance', 'Droits_Douane_HT', 'TVA', 'Total_Quittance', 'engagements'];
		
		if ('read' === $crud->getState()) {
			$columns = ['Num_Facture', 'Date_Facture', 'Id_Fournisseur', 'Num_DUM', 'Date_DUM', 'Origine', 'Marchandises', 'Poids_Net', 'Poids_Brut', 'Id_Nature', 'Id_Code', 'Montant_Devise', 'Devise', 'Taux_Change', 'Montant_MAD', 'Num_Quittance', 'Date_Quittance', 'Droits_Douane_HT', 'TVA', 'Total_Quittance', 'engagements'];
		} elseif ('edit' === $crud->getState() || 'update' === $crud->getState()) {
			$fields = ['Num_Facture', 'Date_Facture', 'Id_Fournisseur', 'Num_DUM', 'Date_DUM', 'Origine', 'Marchandises', 'Poids_Net', 'Poids_Brut', 'Id_Nature', 'Id_Code', 'Montant_Devise', 'Devise', 'Taux_Change', 'Num_Quittance', 'Date_Quittance', 'Droits_Douane_HT', 'TVA', 'Total_Quittance', 'engagements'];
		}
		
		$crud->order_by('Date_Created', 'DESC');
		
		$crud->display_as('Id_Achat', 'Id Achat');
		$crud->display_as('Num_Facture', 'Num Facture');
		$crud->display_as('Date_Facture', 'Date Facture');
		$crud->display_as('Id_Fournisseur', 'Fournisseur');
		$crud->display_as('Num_DUM', 'Num DUM');
		$crud->display_as('Date_DUM', 'Date DUM');
		$crud->display_as('Origine', 'Origine');
		$crud->display_as('Marchandises', 'Marchandises');
		$crud->display_as('Poids_Net', 'Poids Net');
		$crud->display_as('Poids_Brut', 'Poids Brut');
		$crud->display_as('Id_Nature', 'Nature');
		$crud->display_as('Id_Code', 'Code');
		$crud->display_as('Montant_Devise', 'Montant Devise');
		$crud->display_as('Devise', 'Devise');
		$crud->display_as('Taux_Change', 'Taux Change');
		$crud->display_as('Montant_MAD', 'Montant Mad');
		$crud->display_as('Num_Quittance', 'Num Quittance');
		$crud->display_as('Date_Quittance', 'Date Quittance');
		$crud->display_as('Droits_Douane_HT', 'Droits Douane HT');
		$crud->display_as('TVA', 'TVA');
		$crud->display_as('Total_Quittance', 'Total Quittance');
		$crud->display_as('engagements', 'Engagement');
		
		$crud->set_relation('Id_Fournisseur', 'fournisseurs', '{RaisonSociale} - {Pays}');
		$crud->set_relation('Id_Nature', 'nature_marchandises', '{Code_Nature} - {Description}', ['Import' => 1]);
		$crud->set_relation('Id_Code', 'code_marchandises', '{Code} - {Description}');
//        $crud->set_relation('Id_Credoc', 'credocs_fournisseurs', '{Num_Credoc} - {Montant_MAD} Dhs');
//        $crud->set_relation('Id_Engagement', 'engagements_importation', '{Num_Engagement} - {Montant} - {Date}');
		
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
		$crud->callback_before_update(array($this, 'update_callback'));
		
		$crud->columns($columns);
		$crud->fields($fields);
		
		if (!can_list(get_class($this))) $crud->unset_list();
		if (!can_read(get_class($this))) $crud->unset_read();
		if (!can_add(get_class($this))) $crud->unset_add();
		if (!can_edit(get_class($this))) $crud->unset_edit();
		if (!can_delete(get_class($this))) $crud->unset_delete();
		
		$crud->required_fields('Id_Fournisseur', 'Id_Code', 'Origine', 'Marchandises', 'Poids_Net', 'Poids_Brut', 'Id_Nature');
		
		// load helper for validate all integer fields
		$this->load->helper('validation_helper');
		integer_validation($crud, $this->db->field_data($crud->basic_db_table), $crud->required_fields);
		
		$this->template->write('title', 'Achats Importation', TRUE);
		$this->template->write('header', 'Achats Importation');
		//load custom javascript
		$this->template->write('javascript', $this->custom_javascript());
		$this->template->write_view('content', 'example', $crud->render());
		$this->template->render();
	}
	
	public function get_code()
	{
		$id_nature = $this->input->get('id_nature', TRUE);
		$query = $this->db->get_where('code_marchandises', ['Id_Nature' => $id_nature]);
		header('Content-Type: application/json');
		echo json_encode($query->result(), true);
	}
	
	function insert_callback($post_array)
	{
		//load insert helper
		$this->load->helper('insert_helper');
		//get the current user from session
		$user = $this->session->get_userdata();
		//call to the insert_helper
		return insert_helper_callback($post_array, $user['user_id']);
	}
	
	function update_callback($post_array)
	{
		//load insert helper
		$this->load->helper('insert_helper');
		//get the current user from session
		$user = $this->session->get_userdata();
		//call to the insert_helper
		return update_helper_callback($post_array, $user['user_id']);
	}
	
	public function custom_javascript()
	{
		$project_url = $this->config->base_url();
		return '
            $(document).ready(function() {
                //determine if the field_id_code is selected
                var selected_code = null;
                //first check if Nature code is selected
                if($("#field-Id_Code").val()) {
                    selected_code = $("#field-Id_Code").val();
                    fetch_codes_natures(); // call to fetch_codes_natures method
                } else {
                    //if nature code is not selected, clear the dropdown
                     clear_natureCode_dropdown();
                }

                // make ajax request when user select another nature
                $("#field-Id_Nature").change(function(){
                    fetch_codes_natures();
                });
                
                //function to clear nature code dropdown
                function clear_natureCode_dropdown() {
                    $("#field-Id_Code").find("option:not(:first)").remove();  
                    $("#field-Id_Code").append("<option value=0>--</option>");
                    $("#field-Id_Code").val(0).trigger("chosen:updated");
                }

                //function to fetch natures codes
                function fetch_codes_natures() {
                    $.ajax({
                        beforeSend : function() {
                            //before send request, hide the dropdown and display loading
                            $("#Id_Code_input_box").after(`<p class="loading">chargement...</p>`);
                            $("#Id_Code_input_box .chosen-container").hide();
                        },
                        url :"' . $project_url . '/achats_import/get_code?id_nature="+$("#field-Id_Nature").val(),
                        success :function(response){
                            $data = response;
                            $("#field-Id_Code").find("option:not(:first)").remove();
                            for(var i = 0; i<response.length-1;i++) {
                                $("#field-Id_Code").append("<option value="+$data[i].Id_Code+">"+  $data[i].Code + " - " + $data[i].Description +"</option>");
                            }

                            //check if code nature is selected
                            if(selected_code) {
                                $("#field-Id_Code").val(selected_code).trigger("chosen:updated");
                            }
                            
                            //if response is empty  clear dropdown
                            if(response.length < 1) {
                                clear_natureCode_dropdown();
                            }

                            //update Jqeury Chosen dropdown
                            $("#field-Id_Code").trigger("chosen:updated");
                            
                            //set dropdown as default
                            $("#Id_Code_field_box .loading").remove()
                            $("#Id_Code_input_box .chosen-container").show();
                        }
                    });
                }   
            });
            ';
	}
}
