<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_ca extends CI_Controller
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
        $this->template->write('title', 'États du C.A.', TRUE);
        $this->template->write('header', 'États du C.A.');
        $this->template->write_view('content', 'tes/etats_ca', '', true);
        $this->template->write('style', "");
        $this->template->render();
    }

    function generate_report()
    {
        $data = [];
        $year = $this->input->post('year');
        $report = $this->input->post('report');
        $recapitulation = $this->input->post('recapitulation');

        // load the library Html2pdf
        $this->load->library('Html2pdf');
        //Set folder to save PDF to
        $this->html2pdf->folder('./assets/pdfs/');
        //Set the paper defaults
        $this->html2pdf->paper('a4', 'portrait');

        if (isset($recapitulation) && $recapitulation == 'dirham') {
            $sql = "SELECT * FROM etats_6 WHERE Annee IN ($year, " . ($year - 1) . ", " . ($year - 2) . ", " . ($year - 3) . ")";
        } elseif (isset($recapitulation) && $recapitulation == 'quantite') {
            $sql = "SELECT * FROM etats_7 WHERE Annee IN ($year, " . ($year - 1) . ", " . ($year - 2) . ", " . ($year - 3) . ")";
        } elseif (isset($report) && $report == 'ca_a') {
            $sql = "SELECT * FROM etats_1 WHERE Mois LIKE \"%/$year\" ORDER BY Mois";
        } elseif (isset($report) && $report == 'ca_a_n') {
            $sql = "SELECT * FROM etats_2 WHERE Mois LIKE \"%/$year\" ORDER BY Mois";
        } else {
            $sql = "SELECT * FROM etats_1 WHERE Mois LIKE \"%/$year\" ORDER BY Mois";
        }

        $data['sql'] = $sql;
        $data['year'] = $year;
        $data['report'] = $report;
        $data['recapitulation'] = $recapitulation;
        //get company name
        $data['company_name'] =  $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;

        $query = $this->db->query($sql);
        $result = $query->result_array();

        if (isset($recapitulation) && ($recapitulation == 'dirham' || $recapitulation == 'quantite')) {
            foreach ($result as $row) {
                $data['result'][$row['Mois']][$row['Annee']] = $row['Total'];
            }
        } elseif (isset($report) && ($report == 'ca_a' || $report == 'ca_a_n')) {
            $data['result'] = $result;
        }

        if (isset($recapitulation) && $recapitulation == 'dirham') {
            $data['title'] = 'Récapitulation du C.A. Annuel (4 ans) en Dhs';
            $pdf_view = $this->load->view('pdf/recap', $data, true);
            $filename = "RECAP_DIRHAM_$year" . '_' . ($year - 3);
        } elseif (isset($recapitulation) && $recapitulation == 'quantite') {
            $data['title'] = 'Récapitulation du C.A. Annuel (4 ans) en Kg';
            $pdf_view = $this->load->view('pdf/recap', $data, true);
            $filename = "RECAP_QUANTITE_$year" . '_' . ($year - 3);
        } elseif (isset($report) && $report == 'ca_a_n') {
            $pdf_view = $this->load->view('pdf/ca_year_ht', $data, true);
            $filename = "CA_HT_$year";
        } else {
            $pdf_view = $this->load->view('pdf/ca_year', $data, true);
            $filename = "CA_$year";
        }

        $filename .= "_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';

        //Set the filename to save/download as
        $this->html2pdf->filename($filename);

        //Load html view
        $this->html2pdf->html($pdf_view);
        //Download the file
        $this->html2pdf->create('download');

        die('Generation Finished.');
    }
}
