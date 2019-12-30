<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Controle_reglement_factures extends CI_Controller
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
        $sql = 'SELECT max(effets_clients.Num_Bordereau) AS Num_Bordereau FROM effets_clients WHERE DATE(effets_clients.Date_Created)= DATE(CURDATE())';
        $effets_Bordereau = $this->db->query($sql)->result()[0]->Num_Bordereau;
        $data['effets_Bordereau'] = is_null($effets_Bordereau) ? 0 : $effets_Bordereau;
        $sql = 'SELECT max(cheques.Num_Bordereau) AS Num_Bordereau FROM cheques WHERE DATE(cheques.Date_Created)= DATE(CURDATE())';
        $cheques_Bordereau = $this->db->query($sql)->result()[0]->Num_Bordereau;
        $data['cheques_Bordereau'] = is_null($cheques_Bordereau) ? 0 : $cheques_Bordereau;

//highlight_string("<?php\n".var_export($cheques_Bordereau, true));die();

        $sql = "
SELECT `RaisonSociale`, `Id_Client`, `Num_Facture`, DATE_FORMAT(`etats_26`.`Date_Facture`,'%d/%m/%Y') As `Date_Facture`, `Montant`, `Montant_Facture`,
        id_reglement_mixte, `Num_Bordereau`, `table`
FROM `etats_26`
WHERE YEAR(Date_Facture)=YEAR(CURRENT_DATE)
AND (
    (`table`='cheques' AND `Num_Bordereau`={$data['cheques_Bordereau']})
    OR (`table`='effets' AND `Num_Bordereau`={$data['effets_Bordereau']})
    OR (`table`='versements')
)
ORDER BY Date_Facture ASC, Num_facture ASC;
";
        $items = $this
            ->db
            ->query($sql)
            ->result_array();

        $data['items'] = array_filter($items, function ($item) {
            return $item['id_reglement_mixte'] === null;
        });

        $data['items_mixte'] = array_filter($items, function ($item) {
            return $item['id_reglement_mixte'] !== null;
        });

//        $ids_reglement_mixte = array_unique(array_map(function($item) {
//            return $item['id_reglement_mixte'];
//        }, $data['items_mixte']));

//highlight_string("<?php\n" . var_export($data, true));die();

        $this->template->write('title', 'Contrôle de Règlement des Factures', TRUE);
        $this->template->write('header', 'Contrôle de Règlement des Factures');
        $this->template->write_view('content', 'tes/controle_reglement_factures', $data, true);
        $this->template->write('style', '');
        $this->template->render();
    }

    function generate_report()
    {
        //get company name
        $data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
        $data['title'] = 'Contrôle de Règlement des Factures';

        $sql = 'SELECT max(effets_clients.Num_Bordereau) AS Num_Bordereau FROM effets_clients WHERE DATE(effets_clients.Date_Created)= DATE(CURDATE())';
        $effets_Bordereau = $this->db->query($sql)->result()[0]->Num_Bordereau;
        $data['effets_Bordereau'] = is_null($effets_Bordereau) ? 0 : $effets_Bordereau;
        $sql = 'SELECT max(cheques.Num_Bordereau) AS Num_Bordereau FROM cheques WHERE DATE(cheques.Date_Created)= DATE(CURDATE())';
        $cheques_Bordereau = $this->db->query($sql)->result()[0]->Num_Bordereau;
        $data['cheques_Bordereau'] = is_null($cheques_Bordereau) ? 0 : $cheques_Bordereau;

        $sql = "
SELECT `RaisonSociale`, `Id_Client`, `Num_Facture`, DATE_FORMAT(`etats_26`.`Date_Facture`,'%d/%m/%Y') As `Date_Facture`, `Montant`, `Montant_Facture`, `Id_Facture`,
        id_reglement_mixte, `Num_Bordereau`, `table`
FROM `etats_26`
WHERE YEAR(Date_Facture)=YEAR(CURRENT_DATE)
AND (
    (`table`='cheques' AND `Num_Bordereau`={$data['cheques_Bordereau']})
    OR (`table`='effets' AND `Num_Bordereau`={$data['effets_Bordereau']})
    OR (`table`='versements')
)
ORDER BY Date_Facture ASC, Num_facture ASC;
";
        $items = $this
            ->db
            ->query($sql)
            ->result_array();

        $data['items'] = array_filter($items, function ($item) {
            return $item['id_reglement_mixte'] === null;
        });

        $rests = [];
        foreach ($data['items'] as $item) {
            $rests[$item['Id_Facture']] = isset($rests[$item['Id_Facture']]) ? $rests[$item['Id_Facture']] - $item['Montant'] :
                $item['Montant_Facture'] - $item['Montant'];
        }

        foreach ($rests as $Id_Facture => $rest) {
            $sql = "UPDATE `factures_clients`
                    SET `factures_clients`.`Reste_Facture`=$rest
                    WHERE `factures_clients`.`Id_Facture`=$Id_Facture;";
            $this->db->simple_query($sql);
        }

        $data['items_mixte'] = array_filter($items, function ($item) {
            return $item['id_reglement_mixte'] !== null;
        });
//highlight_string("<?php\n" . var_export($data, true));die();
        $pdf_view = $this->load->view('pdf/controle_reglement_factures', $data, true);
        $filename = 'CONTROLE_REGLEMENT_FACTURES' . (new DateTime())->format('dmY_Hi') . '_' . (new DateTime())->getTimestamp() . '.pdf';

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
        //Download the file
        $this->html2pdf->create('download');

        die('Generation Finished.');
    }
}
