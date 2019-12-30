<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_cheques extends CI_Controller
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
        $this->template->write('title', 'États des Cheques', TRUE);
        $this->template->write('header', 'États des Cheques');

        $data['clients'] = $this
            ->db
            ->query("SELECT * FROM clients;")
            ->result_array();

        $data['bordereau'] = $this
            ->db
            ->query("SELECT MAX(Num_Bordereau) AS Num_Bordereau FROM `cheques` WHERE YEAR(`cheques`.`Date_Created`) = YEAR(CURRENT_DATE);")
            ->row()
            ->Num_Bordereau;

        $this->template->write_view('content', 'tes/etats_cheques', $data, true);
        $this->template->render();
    }

    function generate_report()
    {
        $data = [];
        $bordereau = $this->input->post('bordereau');
        $year = $this->input->post('year');

        //get company name
        $data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
        $data['title'] = "Etat des Chèques à Encaisser Bord. Num {$bordereau}/{$year}";

        $sql = "
SELECT `cheques`.`Num_Cheque`, `banques`.`Nom_Banque`, `cheques`.`Montant_Cheque`, `factures_clients`.`Id_Client`, `cheques`.`Tire`, `cheques`.`Endosseur`, `factures_clients`.`Num_Facture`, `factures_clients`.`Date_Facture`
FROM `cheques`
LEFT JOIN `banques` ON `banques`.`Id_Banque` = `cheques`.`Id_Banque`
LEFT JOIN `factures_clients` ON `factures_clients`.`Id_Facture` = `cheques`.`Id_Facture`
WHERE `Num_Bordereau` = {$bordereau} AND YEAR(`cheques`.`Date_Created`) = {$year};
        ";

        $query = $this->db->query($sql);
        $data["result"] = $query->result_array();

        $filename = "ETATS_CHEQUES_BORDEREAU_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
        $pdf_view = $this->load->view('pdf/etats_cheques', $data, true);

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
        $this->html2pdf->isHtml5ParserEnabled = true;
        //Download the file
        $this->html2pdf->create('download');

        die('Generation Finished.');
    }

    function json_change_key($arr, $oldkey, $newkey)
    {
        $json = str_replace('"' . $oldkey . '":', '"' . $newkey . '":', json_encode($arr));

        return json_decode($json, true);
    }
}
