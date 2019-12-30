<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_effets extends CI_Controller
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
        $data['clients'] = $this
            ->db
            ->query("SELECT * FROM clients;")
            ->result_array();

        $data['bordereau'] = $this
            ->db
            ->query("SELECT MAX(Num_Bordereau) AS Num_Bordereau FROM `effets_clients` WHERE YEAR(`effets_clients`.`Date_Created`) = YEAR(CURRENT_DATE);")
            ->row()
            ->Num_Bordereau;

        $this->template->write('title', 'États des Effets', TRUE);
        $this->template->write('header', 'États des Effets');
        $this->template->write('javascript', '
$("#date_debut1, #date_fin1, #date_debut2, #date_fin2").datepicker({
    format: "dd/mm/yyyy"
});
$("#client-select1, #client-select2").chosen();
$("#form_resume").on("submit", function(event) {
    if ($("#client-select2").chosen().val() == 0) {
        var date_debut = new Date($("#date_debut2").datepicker("getDate"));
        var date_fin = new Date($("#date_fin2").datepicker("getDate"));
        var months = 1 + date_fin.getMonth() - date_debut.getMonth() + (12 * (date_fin.getFullYear() - date_debut.getFullYear()))
    
        if(6 <= months) {
            alert("Vous devez sélectionner une intervalle inférieure à 5 mois.");
            event.preventDefault();
        }
    }
});
        ');
        $this->template->write_view('content', 'tes/etats_effets', $data, true);
        $this->template->render();
    }

    function liste_effets_recus()
    {
        $data = [];
        $date_debut = DateTime::createFromFormat("d/m/Y", $this->input->post('date_debut'));
        $date_fin = DateTime::createFromFormat("d/m/Y", $this->input->post('date_fin'));
        $non_echus = $this->input->post('non_echus');
        $portefeuille = $this->input->post('portefeuille');
        $client_id = $this->input->post('client');
        $today = (new DateTime())->format('Y-m-d');

        $data['date_debut'] = $date_debut->format('d/m/Y');
        $data['date_fin'] = $date_fin->format('d/m/Y');
        //get company name
        $data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
        $data['title'] = "Liste des Effets Reçus entre {$data['date_debut']} et  {$data['date_fin']}";

        $sql = "
            SELECT `effets_clients`.`Montant`, `effets_clients`.`Date_Echeance`, `effets_clients`.`Tire`, `effets_clients`.`Endosseur`, `effets_clients`.`Etat_Effet`, `factures_clients`.`Num_Facture`, `factures_clients`.`Date_Facture`, `effets_clients`.`Num_Effet`
            FROM `effets_clients` 
            LEFT JOIN `factures_clients` ON `factures_clients`.`Id_Facture` = `effets_clients`.`Id_Facture`
            WHERE DATE(`effets_clients`.`Date_Created`) BETWEEN \"{$date_debut->format('Y-m-d')}\" AND \"{$date_fin->format('Y-m-d')}\"
        ";

        if (isset($client_id) && (int)$client_id > 0) {
            $sql .= " AND `factures_clients`.`Id_Client` = $client_id ";
            $data['client_name'] = $this->db->query("SELECT `clients`.`RaisonSociale` from `clients` where `clients`.`Id_Client` = $client_id")->result()[0]->RaisonSociale;
        }
        if (isset($non_echus) && $non_echus == 'non_echus') {
            $sql .= " AND \"$today\" < `effets_clients`.`Date_Echeance` ";
            $data['title'] = "Liste des Effets non-échus entre {$data['date_debut']} et  {$data['date_fin']}";
        }
        if (isset($portefeuille) && $portefeuille == 'portefeuille') {
            $sql .= " AND NOT (`effets_clients`.`Etat_Effet` = \"Encaissement\" OR `effets_clients`.`Etat_Effet` = \"Escompte\") ";

            if (isset($data['title'])) {
                $data['title'] = "Liste des Effets non-échus en portefeuille entre {$data['date_debut']} et {$data['date_fin']}";
            } else {
                $data['title'] = "Liste des Effets en portefeuille entre {$data['date_debut']} et {$data['date_fin']}";
            }
        }

        $sql .= " ORDER BY `effets_clients`.`Date_Echeance` ASC";

        $query = $this->db->query($sql);
        $result = $query->result_array();
        $data['result'] = $result;

        $pdf_view = $this->load->view('pdf/etats_effets', $data, true);
        $filename = "EFFETS_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';

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

    function effets_recus_periode()
    {
        $data = [];
        $date_debut = DateTime::createFromFormat("d/m/Y", $this->input->post('date_debut'));
        $date_fin = DateTime::createFromFormat("d/m/Y", $this->input->post('date_fin'));
        $client_id = $this->input->post('client');

        $data['date_debut'] = $date_debut->format('d/m/Y');
        $data['date_fin'] = $date_fin->format('d/m/Y');
        //get company name
        $data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;

        $start = clone $date_debut;
        $start->modify('first day of this month');
        $end = clone $date_fin;
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        if (isset($client_id) && (int)$client_id > 0) {
            $data['client_name'] = $this->db->query("SELECT `clients`.`RaisonSociale` from `clients` where `clients`.`Id_Client` = {$client_id}")->result()[0]->RaisonSociale;
            $data['title'] = "Résumé des Effets par période du client {$data['client_name']}";
            $data["result"] = array();

            $sql = "
                SELECT `clients`.`RaisonSociale` AS `RaisonSociale`, `effets_clients`.`Montant` AS `Montant`, `effets_clients`.`Date_Created`
                FROM `effets_clients`
                LEFT JOIN `factures_clients` ON `factures_clients`.`Id_Facture` = `effets_clients`.`Id_Facture`
                LEFT JOIN `clients` ON `clients`.`Id_Client` = `factures_clients`.`Id_Client`
                WHERE DATE(`effets_clients`.`Date_Created`) BETWEEN \"{$date_debut->format("Y-m-d")}\" AND \"{$date_fin->format("Y-m-d")}\"
                AND `factures_clients`.`Id_Client` = {$client_id}
                ORDER BY DATE(`effets_clients`.`Date_Created`);
            ";
            $query = $this->db->query($sql);
            $result = $query->result_array();

            foreach ($period as $month) {
                $suffixe = $month->format("m/Y");
                $data["result"][$suffixe]["DU 01 -------&gt; 05"] = 0;
                $data["result"][$suffixe]["DU 06 -------&gt; 10"] = 0;
                $data["result"][$suffixe]["DU 11 -------&gt; 15"] = 0;
                $data["result"][$suffixe]["DU 16 -------&gt; 20"] = 0;
                $data["result"][$suffixe]["DU 21 -------&gt; 25"] = 0;
                $data["result"][$suffixe]["DU 26 -------&gt; 31"] = 0;
            }

            foreach ($result as $row) {
                $Date_Created = new DateTime($row["Date_Created"]);
                $suffixe = $Date_Created->format("m/Y");
                $strtotime = $Date_Created->getTimestamp();
                $d1 = DateTime::createFromFormat("d/m/Y", "01/{$suffixe}")->getTimestamp();
                $d2 = DateTime::createFromFormat("d/m/Y", "06/{$suffixe}")->getTimestamp();
                $d3 = DateTime::createFromFormat("d/m/Y", "11/{$suffixe}")->getTimestamp();
                $d4 = DateTime::createFromFormat("d/m/Y", "16/{$suffixe}")->getTimestamp();
                $d5 = DateTime::createFromFormat("d/m/Y", "20/{$suffixe}")->getTimestamp();
                $d6 = DateTime::createFromFormat("d/m/Y", "24/{$suffixe}")->getTimestamp();
                $d7 = DateTime::createFromFormat("d/m/Y", "31/{$suffixe}")->getTimestamp();

                if ($d1 <= $strtotime && $strtotime < $d2) {
                    $data["result"][$suffixe]["DU 01 -------&gt; 05"] += $row["Montant"];
                } elseif ($d2 <= $strtotime && $strtotime < $d3) {
                    $data["result"][$suffixe]["DU 06 -------&gt; 10"] += $row["Montant"];
                } elseif ($d3 <= $strtotime && $strtotime < $d4) {
                    $data["result"][$suffixe]["DU 11 -------&gt; 15"] += $row["Montant"];
                } elseif ($d4 <= $strtotime && $strtotime < $d5) {
                    $data["result"][$suffixe]["DU 16 -------&gt; 20"] += $row["Montant"];
                } elseif ($d5 <= $strtotime && $strtotime < $d6) {
                    $data["result"][$suffixe]["DU 21 -------&gt; 25"] += $row["Montant"];
                } elseif ($d6 <= $strtotime && $strtotime <= $d7) {
                    $data["result"][$suffixe]["DU 26 -------&gt; 31"] += $row["Montant"];
                }
            }

            $i = 0;
            $previousFrom = null;
            $previousTo = null;
            foreach (array("01" => "05", "06" => "10", "11" => "15", "16" => "20", "21" => "25", "26" => "31") as $from => $to) {
                if (0 < $i) unset($data["result"][$date_debut->format("m/Y")]["DU {$previousFrom} -------&gt; {$previousTo}"]);

                if ($from <= $date_debut->format("d") && $date_debut->format("d") <= $to) {
                    $data["result"][$date_debut->format("m/Y")] = $this->json_change_key($data["result"][$date_debut->format("m/Y")], "DU {$from} -------&gt; {$to}", "DU {$date_debut->format("d")} -------&gt; {$to}");
                    break;
                }

                $i++;
                $previousFrom = $from;
                $previousTo = $to;
            }

            $i = 0;
            $marlCursor = 10;
            foreach (array("01" => "05", "06" => "10", "11" => "15", "16" => "20", "21" => "25", "26" => "31") as $from => $to) {
                if ($from <= $date_fin->format("d") && $date_fin->format("d") <= $to) {
                    $data["result"][$date_fin->format("m/Y")] = $this->json_change_key($data["result"][$date_fin->format("m/Y")], "DU {$from} -------&gt; {$to}", "DU {$from} -------&gt; {$date_fin->format("d")}");
                    $marlCursor = $i;
                }

                if ($marlCursor < $i) unset($data["result"][$date_fin->format("m/Y")]["DU {$from} -------&gt; {$to}"]);

                $i++;
            }

            $pdf_view = $this->load->view('pdf/etats_effets_resume_client', $data, true);
            $filename = "EFFETS_RÉSUMÉ_CLIENT" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
        } else {
            $data['title'] = "Résumé des Effets par période de tous les Clients";
            $sql = "
                SELECT `clients`.`RaisonSociale` AS `RaisonSociale`, SUM(`effets_clients`.`Montant`) AS `Montant`, concat(lpad(month(`effets_clients`.`Date_Created`),2,'0'),'/',year(`effets_clients`.`Date_Created`)) AS `Mois`
                FROM `effets_clients`
                LEFT JOIN `factures_clients` ON `factures_clients`.`Id_Facture` = `effets_clients`.`Id_Facture`
                LEFT JOIN `clients` ON `clients`.`Id_Client` = `factures_clients`.`Id_Client`
                WHERE DATE(`effets_clients`.`Date_Created`) BETWEEN \"{$date_debut->format("Y-m-d")}\" AND \"{$date_fin->format("Y-m-d")}\"
                GROUP BY `factures_clients`.`Id_Client`, `Mois`
                ORDER BY `Mois`;
            ";

            $query = $this->db->query($sql);
            $result = $query->result_array();

            foreach ($result as $row) {
                $data['result'][$row['RaisonSociale']][$row['Mois']] = $row['Montant'];
            }

            if (empty($result)) $data['result'] = array();

            $months = array();
            foreach ($period as $dt) {
                $months[] = $dt->format("m/Y");
            }
            $data['months'] = $months;

            $pdf_view = $this->load->view('pdf/etats_effets_resume', $data, true);
            $filename = "EFFETS_RÉSUMÉ_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
        }

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

    function effets_recevoir()
    {
        $data = [];
        $bordereau = $this->input->post('bordereau');
        $repartitions = $this->input->post('répartitions');
        $year = (new DateTime())->format("Y");

        //get company name
        $data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
        $data['title'] = "Etat des Effets à recevoir bordereau N {$bordereau}/{$year}";

        $sql = "
            SELECT `effets_clients`.`Num_Effet`, DATE_FORMAT(`effets_clients`.`Date_Echeance`, '%d/%m/%Y') As 'Date_Echeance', `effets_clients`.`Montant`, `factures_clients`.`Id_Client`, `effets_clients`.`Tire`, `effets_clients`.`Endosseur`, `factures_clients`.`Num_Facture`, DATE_FORMAT(`factures_clients`.`Date_Facture`, '%d/%m/%Y') As 'Date_Facture', `effets_clients`.`Num_Repartition`, `banques`.`Code_Banque`
            FROM `effets_clients`
            LEFT JOIN `factures_clients` ON `factures_clients`.`Id_Facture` = `effets_clients`.`Id_Facture`
            JOIN `banques` ON `banques`.`Id_Banque`=`effets_clients`.`Id_Banque`
            WHERE `Num_Bordereau` = {$bordereau} AND YEAR(`effets_clients`.`Date_Created`) = {$year};
        ";

        $query = $this->db->query($sql);
        $result = $query->result_array();

        $filename = "EFFETS_RECEVOIR_BORDEREAU_";
        if (isset($repartitions) && $repartitions == "répartitions") {
            $data['title'] = "Répartition des Effets à recevoir Bordereau Num {$bordereau}/{$year}";

            foreach ($result as $row) {
                $data["result"][$row["Num_Repartition"]][] = $row;
            }

            $pdf_view = $this->load->view('pdf/etats_effets_recevoir_repartitions', $data, true);
            $filename .= "RÉPARTITION_";
        } else {
            $data["result"] = $result;
            $pdf_view = $this->load->view('pdf/etats_effets_recevoir_bordereau', $data, true);
        }
        $filename .= (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';

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
