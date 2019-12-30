<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_carnets extends CI_Controller
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
        $this->template->write('title', 'États des Carnets', TRUE);
        $this->template->write('header', 'États des Carnets');
        $this->template->write_view('content', 'tes/etats_carnets', '', true);
        $this->template->write('style', "");
        $this->template->render();
    }

    function generate_report()
    {
        $data = [];
        $year = $this->input->post('year');

        // load the library Html2pdf
        $this->load->library('Html2pdf');
        //Set folder to save PDF to
        $this->html2pdf->folder('./assets/pdfs/');
        //Set the paper defaults
        $this->html2pdf->paper('a4', 'portrait');

        $sql = "SELECT * FROM etats_5 WHERE Annee = $year ORDER BY Num_Carnet";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        $data['result'] = $result;
        $data['year'] = $year;
        //get company name
        $data['company_name'] =  $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;

        $pdf_view = $this->load->view('pdf/etats_carnet', $data, true);
        $filename = "CARNET_$year" . "_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
        
        //Set the filename to save/download as
        $this->html2pdf->filename($filename);

        //Load html view
        $this->html2pdf->html($pdf_view);
        //Download the file
        $this->html2pdf->create('download');

        die('Generation Finished.');
    }
}
