<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_effets_fournisseurs extends CI_Controller
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
        $this->template->write('title', 'États des Effets Fournisseurs', TRUE);
        $this->template->write('header', 'États des Effets Fournisseurs');

        $data['fournisseurs'] = $this
            ->db
            ->query("SELECT * FROM fournisseurs;")
            ->result_array();

        $this->template->write_view('content', 'tes/etats_effets_fournisseurs', $data, true);
        $this->template->write('javascript', '$("#fournisseur-select").chosen();');
        $this->template->render();
    }

    function liste_effets_payer()
    {
        $data = [];
        $fournisseur_id = $this->input->post('fournisseur');

        //get company name
        $data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
        $data['title'] = "Liste des Effets à Payer Non-Echus de tous les Fournisseurs";

        $sql = "SELECT * FROM `etats_41` WHERE Date_Echeance > CURRENT_DATE() ";

        if (isset($fournisseur_id) && (int)$fournisseur_id > 0) {
            $sql .= "AND `Id_Fournisseur` = {$fournisseur_id}";
            $data['fournisseur_name'] = $this->db->query("SELECT `fournisseurs`.`RaisonSociale` from `fournisseurs` where `fournisseurs`.`Id_Fournisseur` = {$fournisseur_id}")->result()[0]->RaisonSociale;
            $data['title'] = "Liste des Effets à Payer Non-Echus du fournisseur {$data['fournisseur_name']}";
        }

        $query = $this->db->query($sql);
        $result = $query->result_array();
        $data['result'] = empty($result) ? array() : $result;

        $pdf_view = $this->load->view('pdf/etats_effets_fournisseurs', $data, true);
        $filename = "EFFETS_FOURNISSEURS" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';

        // load the library Html2pdf
        $this->load->library('Html2pdf');
        //Set folder to save PDF to
        $this->html2pdf->folder('./assets/pdfs/');
        //Set the paper defaults
        $this->html2pdf->paper('a4', 'portrait');
        //Set the filename to save/download as
        $this->html2pdf->filename($filename);
        //Load html view
        $this->html2pdf->html($pdf_view);
//        $this->html2pdf->isHtml5ParserEnabled = true;
        //Download the file
        $this->html2pdf->create('download');

        die('Generation Finished.');
    }
}
