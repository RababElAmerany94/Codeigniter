<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Verification_factures extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('general_helper');
        $this->load->helper('form');
        $this->db = $this->load->database(get_current_db(), TRUE);
        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index()
    {
        $this->template->write('title', 'Vérification de Factures ', TRUE);
        $this->template->write('header', 'Vérification de Factures ');
        //load custom javascript
        $this->template->write('javascript', $this->custom_javascript());
        $this->template->write_view('content', 'tes/verification_factures', '', true);
        $this->template->write('style', "");
        $this->template->render();
    }


    function check_record()
    {
        $num_facture = $this->input->post('num_facture');
        $year = $this->input->post('year');

        $sql = "SELECT * FROM factures_clients WHERE Num_Facture = $num_facture;";
        $query = $this->db->query($sql);
        $facture = $query->row();

        header('Content-Type: application/json');

        if ($facture && $facture->id_reglement_mixte) {
            echo json_encode(['success' => true], true);
            exit();
        } else {
            //EFFETS SQL
            $sql = "SELECT
                    e.Num_Effet,
                    e.Date_Echeance As 'ECHEANCE',
                    e.Montant AS 'MONTANT',
                    e.Tire AS 'TIRE',
                    e.Endosseur AS 'ENDOSSEUR',
                    b.Nom_Banque AS 'DOMICILIATION',
                    e.Ville AS 'VILLE'
                FROM 
                    factures_clients f, 
                    effets_clients e, 
                    banques b
                WHERE f.Id_Facture = e.Id_Facture  
                AND b.Id_Banque = e.Id_Banque
                AND YEAR(f.Date_Facture) = $year
                AND f.Num_Facture = $num_facture";

            $query = $this->db->query($sql);
            $data['effets'] = $query->result_array();

            //CHEQUE SQL
            $sql = "SELECT
                    c.Num_Cheque As 'NUM CHEQUE',
                    c.Montant_Cheque AS 'MONTANT',
                    c.Tire AS 'TIRE',
                    c.Endosseur AS 'ENDOSSEUR',
                    b.Nom_Banque AS 'DOMICILIATION',
                    c.Ville AS 'VILLE',
                    c.Num_Remise AS 'NUM REMISE',
                    c.Date_Remise AS 'DATE REMISE'
                from 
                    factures_clients f, 
                    cheques c, 
                    banques b
                WHERE f.Id_Facture = c.Id_Facture  
                AND b.Id_Banque = c.Id_Banque
                AND YEAR(f.Date_Facture) = $year
                AND f.Num_Facture = $num_facture";
            $query = $this->db->query($sql);
            $data['cheques'] = $query->result_array();

            if (!$data['effets'] && !$data['cheques']) {
                echo json_encode(['success' => false], true);
                exit();
            } else {
                echo json_encode(['success' => true], true);
                exit();
            }
        }
    }

    function generate_report()
    {
        $data = [];
        $num_facture = $this->input->post('num_facture');
        $year = $this->input->post('year');

        $sql = "SELECT * FROM factures_clients WHERE Num_Facture = $num_facture AND YEAR(Date_Facture) = $year;";
        $query = $this->db->query($sql);
        $facture = $query->row();

        if ($facture && $facture->id_reglement_mixte) {
            $data = array_merge($data, $this->Verification_factures_mixte($facture->id_reglement_mixte, $year));
            $filename = 'pdf/etats_reglements_mixte';
        } else {
            $data = array_merge($data, $this->Verification_factures_simple($year, $num_facture));
            $filename = 'pdf/etats_reglements';
        }

        //get company name
        $data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
        //Sen data
        $data['num_facture'] = $num_facture;
        $data['year'] = $year;

        $pdf_view = $this->load->view($filename, $data, true);
        $filename = "ET_REGELEMENT_$num_facture" . "_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';

        // load the library Html2pdf
        $this->load->library('Html2pdf');
        //Set folder to save PDF to
        $this->html2pdf->folder('./assets/pdfs/');
        //Set the paper defaults
        $this->html2pdf->paper('a4', 'portrait');
        //Set the filename to save/download as
        $this->html2pdf->filename($filename);
        // //Load html view
        $this->html2pdf->html($pdf_view);
        $this->html2pdf->isHtml5ParserEnabled = true;
        // //Download the file
        $this->html2pdf->create('download');

        die('Generation Finished.');
    }

    function Verification_factures_simple($year, $num_facture)
    {
        $data = [];

        //EFFETS SQL
        $sql = " SELECT     e.Num_Effet,
                            e.Date_Echeance As 'ECHEANCE',
                            e.Montant as 'MONTANT',
                            e.Tire as 'TIRE',
                            e.Endosseur as 'ENDOSSEUR',
                            b.Nom_Banque as 'DOMICILIATION',
                            e.Ville as 'VILLE'
                    from 
                        factures_clients f, 
                        effets_clients e, 
                        banques b
                    WHERE f.Id_Facture = e.Id_Facture  
                        and b.Id_Banque = e.Id_Banque
                        and YEAR(f.Date_Facture) = $year
                        and f.Num_Facture = $num_facture";

        $query = $this->db->query($sql);
        $data['effets'] = $query->result_array();


        //CHEQUE SQL
        $sql = "SELECT  c.Num_Cheque As 'NUM CHEQUE',
                        c.Montant_Cheque as 'MONTANT',
                        c.Tire as 'TIRE',
                        c.Endosseur as 'ENDOSSEUR',
                        b.Nom_Banque as 'DOMICILIATION',
                        c.Ville as 'VILLE',
                        c.Num_Remise as 'NUM REMISE',
                        c.Date_Remise as 'DATE REMISE'
                        from 
                            factures_clients f, 
                            cheques c, 
                            banques b
                        WHERE f.Id_Facture = c.Id_Facture  
                            and b.Id_Banque = c.Id_Banque
                            and YEAR(f.Date_Facture) = $year
                            and f.Num_Facture = $num_facture";
        $query = $this->db->query($sql);
        $data['cheques'] = $query->result_array();

        //Virement SQL
        $sql = "SELECT 
                    v.Num_Operation As 'NUM VERSEMENT',
                    v.Montant as 'MONTANT',
                    v.Date_Created as 'DATE VERSEMENT'
                FROM 
                    factures_clients f, 
                    virements_versements v
                WHERE f.Id_Facture = v.Id_Facture  
                AND YEAR(f.Date_Facture) = $year
                AND f.Num_Facture = $num_facture;
        ";

        $query = $this->db->query($sql);
        $data['virement'] = $query->result_array();

        //Get the client
        $sql = "SELECT
                    c.Id_Client,
                    c.RaisonSociale 
                FROM
                    clients c,
                    factures_clients f
                WHERE c.Id_Client = f.Id_Client 
                AND YEAR(f.Date_Facture) = $year
                AND f.Num_Facture = $num_facture;
        ";
        $query = $this->db->query($sql);
        $data['client'] = !empty($query->result_array()) ? $query->result_array()[0] : '';

        //Get Date facture
        $sql = "SELECT
                    f.Date_Facture,
                    f.Montant_Facture
                FROM factures_clients f
                WHERE f.Num_Facture= $num_facture
                AND YEAR(f.Date_Facture) = $year;
        ";
        $query = $this->db->query($sql);
        $row = $query->row_array();
        if (!empty($row)) {
            $data['date_facture'] = $row['Date_Facture'];
            $data['total_facture'] = $row['Montant_Facture'];
        } else {
            $data['date_facture'] = '';
            $data['total_facture'] = 0;
        }

        $total_general = 0;
        //calcul total facture
        if (!empty($data['effets'])) {
            $total_general += array_sum(array_map(function ($effet) {
                return $effet['MONTANT'];
            }, $data['effets']));
        }
        if (!empty($data['cheques'])) {
            $total_general += array_sum(array_map(function ($effet) {
                return $effet['MONTANT'];
            }, $data['cheques']));
        }
        if (!empty($data['virement'])) {
            $total_general += array_sum(array_map(function ($effet) {
                return $effet['MONTANT'];
            }, $data['virement']));
        }

        //update Reste Facture
        $total_facture = $data['total_facture'];
        $sql = "UPDATE factures_clients 
                SET Reste_Facture =  $total_facture  - $total_general  
                where Num_Facture = $num_facture";
        $this->db->simple_query($sql);

        $data['total_general'] = $total_general;

        return $data;
    }

    function Verification_factures_mixte($regelement_id, $year)
    {
        $sql = "SELECT * FROM factures_clients WHERE id_reglement_mixte = $regelement_id;";
        $query = $this->db->query($sql);
        $factures = $query->result_array();
        $data['factures'] = $factures;

//        foreach ($factures as $facture) {
        //EFFETS SQL
        $sql = "SELECT
                    e.Num_Effet,
                    f.Num_Facture as Num_Facture,
                    e.Date_Echeance As 'ECHEANCE',
                    e.Montant as 'MONTANT',
                    e.Tire as 'TIRE',
                    e.Endosseur as 'ENDOSSEUR',
                    b.Nom_Banque as 'DOMICILIATION',
                    e.Ville as 'VILLE'
                FROM
                    factures_clients f, 
                    effets_clients e, 
                    banques b
                WHERE f.Id_Facture = e.Id_Facture
                AND b.Id_Banque = e.Id_Banque
                AND YEAR(f.Date_Facture) = $year
                AND f.id_reglement_mixte = $regelement_id";
        // JOIN reglement_mixte ON reglement_mixte.id_reglement_mixte = factures_clients.id_reglement_mixte

        $query = $this->db->query($sql);
        $data['effets'] = $query->result_array();

        //CHEQUE SQL
        $sql = "SELECT 
                    c.Num_Cheque As 'NUM CHEQUE',
                    f.Num_Facture as Num_Facture,
                    c.Montant_Cheque as 'MONTANT',
                    c.Tire as 'TIRE',
                    c.Endosseur as 'ENDOSSEUR',
                    b.Nom_Banque as 'DOMICILIATION',
                    c.Ville as 'VILLE',
                    c.Num_Remise as 'NUM REMISE',
                    c.Date_Remise as 'DATE REMISE'
                FROM 
                    factures_clients f, 
                    cheques c, 
                    banques b
                WHERE f.Id_Facture = c.Id_Facture  
                AND b.Id_Banque = c.Id_Banque
                AND YEAR(f.Date_Facture) = $year
                AND f.id_reglement_mixte = $regelement_id";
        $query = $this->db->query($sql);
        $data['cheques'] = $query->result_array();

        //Virement SQL
        $sql = "SELECT 
                    v.Num_Operation As 'NUM VERSEMENT',
                    f.Num_Facture as Num_Facture,
                    v.Montant as 'MONTANT',
                    v.Date_Created as 'DATE VERSEMENT'
                FROM 
                    factures_clients f, 
                    virements_versements v
                WHERE f.Id_Facture = v.Id_Facture  
                AND YEAR(f.Date_Facture) = $year
                AND f.id_reglement_mixte = $regelement_id";

        $query = $this->db->query($sql);
        $data['virements'] = $query->result_array();

        //Get the client
        $sql = "SELECT c.Id_Client, c.RaisonSociale 
                FROM clients c, factures_clients f
                WHERE c.Id_Client = f.Id_Client 
                AND f.id_reglement_mixte = $regelement_id";
        $query = $this->db->query($sql);
        $data['client'] = $query->row_array();

        //Get Date facture
        $sql = "SELECT f.Num_Facture, f.Date_Facture, f.Montant_Facture
                FROM factures_clients f
                WHERE f.id_reglement_mixte = $regelement_id
                AND YEAR(f.Date_Facture) = $year";
        $query = $this->db->query($sql);
        $row = $query->row_array();
        if (!empty($row)) {
            $data[$row['Num_Facture']]['date_facture'] = $row['Date_Facture'];
            $data[$row['Num_Facture']]['total_facture'] = $row['Montant_Facture'];
        } else {
            $data[$row['Num_Facture']]['date_facture'] = '';
            $data[$row['Num_Facture']]['total_facture'] = 0;
        }

        $total_general = 0;
        //calcul total facture
        if (!empty($data[$row['Num_Facture']]['effets'])) {
            $total_general += array_sum(array_map(function ($effet) {
                return $effet['MONTANT'];
            }, $data[$row['Num_Facture']]['effets']));
        }
        if (!empty($data[$row['Num_Facture']]['cheques'])) {
            $total_general += array_sum(array_map(function ($effet) {
                return $effet['MONTANT'];
            }, $data[$row['Num_Facture']]['cheques']));
        }
        if (!empty($data[$row['Num_Facture']]['virement'])) {
            $total_general += array_sum(array_map(function ($effet) {
                return $effet['MONTANT'];
            }, $data[$row['Num_Facture']]['virement']));
        }

        // update Reste Facture
//            $total_facture = $data[$facture['Id_Facture']]["total_facture"];
//            $sql = "UPDATE factures_clients
//                SET Reste_Facture =  $total_facture  - $total_general
//                where Num_Facture = $num_facture";
//            $this->db->simple_query($sql);

        $data[$row['Num_Facture']]['total_general'] = $total_general;
//        }

//        highlight_string("<?php\n" . var_export($data, true));
//        die();

        return $data;
    }

    function custom_javascript()
    {
        return '
            $("#client-select").chosen();
            $("#toggle-year").click(function() {
                $("#year-list").prop("disabled", function(i, v) { return !v; });
            });
        ';
    }
}
