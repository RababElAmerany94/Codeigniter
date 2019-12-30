<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Achats_locaux extends CI_Controller
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
        $this->template->write('title', 'Achats Locaux', TRUE);
        $this->template->write('header', 'Achats Locaux');

        $crud = new grocery_CRUD();
        $crud->set_table('achats_locaux');
        $crud->set_subject('Achat');

        $columns = ['Id_Fournisseur', 'Num_Facture', 'Date_Facture', 'Id_Nature', 'Id_Code', 'Montant_TTC'];
        $fields = ['Id_Fournisseur', 'Num_Facture', 'Date_Facture', 'Marchandises', 'Poids', 'Id_Nature', 'Id_Code', 'TVA', 'Montant_TTC'];

        if ('read' == $crud->getState()) {
            $columns = ['Id_Fournisseur', 'Num_Facture', 'Date_Facture', 'Marchandises', 'Poids', 'Id_Nature', 'Id_Code', 'Montant_HT', 'TVA', 'Montant_TTC'];
        } elseif ('edit' == $crud->getState() || 'update' == $crud->getState()) {
            $fields = ['Id_Fournisseur', 'Num_Facture', 'Date_Facture', 'Marchandises', 'Poids', 'Id_Nature', 'Id_Code', 'Montant_HT', 'TVA', 'Montant_TTC'];
        }
	
	    $crud->order_by('Date_Created', 'DESC');

        $crud->display_as('Id_Achat', 'Id Achat');
        $crud->display_as('Id_Fournisseur', 'Fournisseur');
        $crud->display_as('Num_Facture', 'Num Facture');
        $crud->display_as('Date_Facture', 'Date Facture');
        $crud->display_as('Marchandises', 'Marchandises');
        $crud->display_as('Poids', 'Poids en Kg');
        $crud->display_as('Id_Nature', 'Nature');
        $crud->display_as('Id_Code', 'Code');
        $crud->display_as('Montant_HT', 'Montant HT');
        $crud->display_as('TVA', 'TVA');
        $crud->display_as('Montant_TTC', 'Montant TTC');
        
        $crud->set_relation('Id_Fournisseur', 'fournisseurs', '{RaisonSociale}');
        $crud->set_relation('Id_Nature', 'nature_marchandises', '{Code_Nature} - {Description}');
        $crud->set_relation('Id_Code', 'code_marchandises', '{Code} - {Description}');

       

        if (!can_list(get_class($this))) $crud->unset_list();
        if (!can_read(get_class($this))) $crud->unset_read();
        if (!can_add(get_class($this))) $crud->unset_add();
        if (!can_edit(get_class($this))) $crud->unset_edit();
        if (!can_delete(get_class($this))) $crud->unset_delete();

        
        // add field Montant_HT adn make it hidden -- Hamza
        $crud->field_type('Montant_HT', 'hidden');
        // callback runs before the auto insert/update of the crud
        $crud->callback_before_insert(array($this, 'ht_facture_callback'));
        $crud->callback_before_update(array($this, 'ht_facture_callback'));
        // add JS for auto calculate TVA (20%)
        $this->template->write('javascript', '
        $("#field-Montant_TTC").on("keypress keyup cut copy paste mousedown mouseup focus blur change", function(event) {
            var TVA = $(this).val() - ($(this).val() / 1.2);
            TVA = ceiling(TVA);
            $("#field-TVA").val(TVA);
        });
        ');


        //set the required fields
         $crud->required_fields('Id_Fournisseur','Num_Facture','Date_Facture','Marchandises','Id_Nature','Id_Code','TVA','Montant_TTC');
        // load helper for validate all integer fields --maraoune
        $this->load->helper('validation_helper');
        integer_validation($crud, $this->db->field_data($crud->basic_db_table) , $crud->required_fields );
        
        //load custom javascript
        $this->template->write('javascript', $this->custom_javascript() );

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

    
        $this->template->write_view('content', 'example', $crud->render());
        $this->template->render();
    }

    function ht_facture_callback($post_array)
    {
        $post_array['Montant_HT'] = $post_array['Montant_TTC'] - $post_array['TVA'];
        return $post_array;
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
    public function get_code(){
        $id_nature = $this->input->get('id_nature', TRUE);
        $query = $this->db->get_where("code_marchandises" , ["Id_Nature" => $id_nature]);
        header('Content-Type: application/json');
        echo json_encode($query->result(),true);
    }

    public function custom_javascript(){
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
                        url :"'.$project_url.'/achats_import/get_code?id_nature="+$("#field-Id_Nature").val(),
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
