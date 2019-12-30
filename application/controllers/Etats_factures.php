<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_factures extends CI_Controller
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
        $this->template->write('title', 'Etats des Factures', TRUE);
        $this->template->write('header', 'Etats des Factures');
        $query = $this->db->query("SELECT * FROM clients");
        $result = $query->result_array();
        $data['clients'] = $query->result_array();

        // load custom css
        $this->template->write('style', "");
        // load custom javascript
        $this->template->write('javascript', $this->custom_javascript());
        $this->template->write_view('content', 'tes/etats_factures', $data, true);
        $this->template->render();
    }

    function generate_report()
    {
        $data = [];
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $carnet = $this->input->post('carnet');
        $client = $this->input->post('client');
        $type = $this->input->post('type');

        if (isset($client)) {
            $list_facture = true;
            $client = ($client == 0) ? null : $client;
            $query = "";
            if ($client) {
                $query = " and c.Id_Client = $client";
            }
            if (isset($type)) {
                $seuil_facture_non_payee = $this->db->query("SELECT value from settings where settings.key = 'app_SeuilFactureNonPayee'")->result()[0]->value;
                $query .= " and f.Reste_Facture > $seuil_facture_non_payee";
            }
            if (isset($year)) {
                $query .= " and  year(f.Date_Facture) = $year ";
            }
            $sql = "SELECT  f.Num_facture As 'Facture N',
                            DATE_FORMAT(f.Date_Facture,'%d/%m/%Y') As 'Date Facture', 
                            c.Id_Client As 'Code Client', 
                            c.RaisonSociale As 'Client', 
                            f.HT_Facture As 'H.T.',
                            f.TVA_Facture As 'TVA', 
                            f.Montant_Facture As 'TTC',
                            f.Unite,
                            f.Quantite
                    from factures_clients f, clients c
                    WHERE f.Id_Client = c.Id_Client
                    $query
                    ORDER BY f.Date_Facture ASC, CAST(f.Num_facture AS UNSIGNED) ASC";
        } else if (isset($carnet)) {
            $sql = "SELECT  f.Num_facture As 'Facture N',
                            DATE_FORMAT(f.Date_Facture,'%d/%m/%Y') As 'Date Facture', 
                            c.Id_Client As 'Code Client', 
                            c.RaisonSociale As 'Client', 
                            f.HT_Facture As 'H.T.',
                            f.TVA_Facture As 'TVA', 
                            f.Montant_Facture As 'TTC',
                            f.Unite,
                            f.Quantite
                    FROM factures_clients f, clients c
                    WHERE f.Id_Client = c.Id_Client
                    AND f.Num_carnet = $carnet
                    AND YEAR(Date_Facture) = '$year'
                    ORDER BY f.Date_Facture ASC, CAST(f.Num_facture AS UNSIGNED) ASC";
        } else {
            $sql = "SELECT  f.Num_facture As 'Facture N',
                        DATE_FORMAT(f.Date_Facture,'%d/%m/%Y') As 'Date Facture', 
                        c.Id_Client As 'Code Client', 
                        c.RaisonSociale As 'Client', 
                        f.HT_Facture As 'H.T.',
                        f.TVA_Facture As 'TVA', 
                        f.Montant_Facture As 'TTC',
                        f.Unite
                from factures_clients f, clients c
                WHERE f.Id_Client = c.Id_Client
                and month(f.Date_Facture) = $month
                and year(f.Date_Facture) = $year
                ORDER BY f.Date_Facture ASC, CAST(f.Num_facture AS UNSIGNED) ASC";
        }

        $query = $this->db->query($sql);

        if (isset($list_facture)) {
            $data['list_facture'] = true;
            $data['year'] = $year;
            $data['client'] = $client;
            if (isset($client)) {
                // get the client name
                $client_sql = "SELECT RaisonSociale FROM clients where Id_Client = $client";
                $result_client = $this->db->query($client_sql)->result_array();
                $data['client_name'] = $result_client[0]['RaisonSociale'];
            }
            $data['type'] = $type;
        } else if (isset($carnet)) {
            $data['carnet'] = $carnet;
            $data['year'] = $year;
        } else {
            $data['month'] = $month;
            $data['year'] = $year;
        }

        $data['result'] = $query->result_array();
        // get company name
        $data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;

        if (isset($carnet) || isset($list_facture)) {
            $current_year = (new DateTime())->format('Y');
            // get the the taux_conversion_m_kg
            $sql = "SELECT Taux from taux_conversion_m_kg t WHERE t.Annee = $current_year";
            $query = $this->db->query($sql);
            $result = $query->result_array();
            $data['Taux'] = $result[0]['Taux'];
        }

        if (isset($carnet)) {
            $pdf_view = $this->load->view('pdf/etats_facture_carnet', $data, true);
            $filename = "ET_FACTURE_$carnet" . "_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
        } else if (isset($list_facture)) {
            $pdf_view = $this->load->view('pdf/etats_facture_carnet', $data, true);
            $filename = "ET_FACTURE_" . "_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
        } else {
            $pdf_view = $this->load->view('pdf/etats_facture', $data, true);
            $filename = "ET_FACTURE_$month" . "_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
        }

//        $this->template->write_view('content', 'pdf/etats_facture_carnet', $data, true);
//        $this->template->render();
//        return;

        if (isset($list_facture)) {
            // load the library Html2pdf
            $this->load->library('HtmlToPdf');
            //Load html view
            $this->htmltopdf->html($pdf_view);
            //Set folder to save PDF to
            $this->htmltopdf->folder('./assets/pdfs/');
            //Set the filename to save/download as
            $this->htmltopdf->filename($filename);
            //Download the file
            $this->htmltopdf->create('download');
        } else {
            // load the library Html2pdf
            $this->load->library('Html2pdf');
            // Set folder to save PDF to
            $this->html2pdf->folder('./assets/pdfs/');
            // Set the paper defaults
            $this->html2pdf->paper('a4', 'portrait');
            // Set the filename to save/download as
            $this->html2pdf->filename($filename);
            // Load html view
            $this->html2pdf->html($pdf_view);
            $this->html2pdf->isHtml5ParserEnabled = true;
            // Download the file
            $this->html2pdf->create('download');
        }

        die('Generation Finished.');
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
