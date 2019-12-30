<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_clients extends CI_Controller
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
        $data = array();

        $this->template->write('title', 'Liste des Clients', TRUE);
        $this->template->write('header', 'Liste des Clients');
        $this->template->write_view('content', 'tes/etats_clients', $data, true);
        $this->template->write('javascript', '');
        $this->template->render();
    }

    function generate_report()
    {
        $data = array();
        $data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
        $data['title'] = "Liste des Clients";

        $sql = "SELECT `Id_Client`, `RaisonSociale`, `Ville`, `Telephone_1`, `IBAN_1` FROM `clients`;";

        $query = $this->db->query($sql);
        $data['result'] = $query->result_array();

        $pdf_view = $this->load->view('pdf/etats_clients', $data, true);
        $filename = "CLIENTS_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';

        // load the library Html2pdf
        $this->load->library('HtmlToPdf');
        //Load html view
        $this->htmltopdf->html($pdf_view);
        //Set the filename to save/download as
        $this->htmltopdf->filename($filename);
        //Download the file
        $this->htmltopdf->create('download');

        die('Generation Finished.');
    }
}
