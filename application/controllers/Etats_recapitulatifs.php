<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_recapitulatifs extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper('general_helper');
		$this->load->helper('form');
		$this->db = $this->load->database(get_current_db(), true);
		$this->template->write_view('sidenavs', 'template/default_sidenavs', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}

	function index()
	{
		$this->template->write('title', 'Etats Récapitulatifs', true);
		$this->template->write('header', 'Etats Récapitulatifs');
		// custom css
		$this->template->write('style', "");
		// custom javascript
		$this->template->write('javascript', "");
		$this->template->write_view('content', 'tes/etats_recapitulatifs', '', true);
		$this->template->render();
	}

	function generate_report()
	{
		$data = [];
		$year = $this->input->post('year');
		$period = $this->input->post('period');
		$type = $this->input->post('type');
		//get company name
		$data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
		$data['year'] = $year;
		$filename = 'RÉCAPITULATIFS_';

		if ($period === 'mois')
		{
			if ($type === 'dirhams')
			{
				$data['title'] = "Etat Général Récapitulatif par mois en DH H.T. pour $year";

				for ($i = 1; $i <= 12; $i ++)
				{
					foreach ([
						         'P_Ventes',
						         'T_Ventes',
						         'M_Ventes'
					         ] as $item)
						$data['table1'][str_pad($i, 2, "0", STR_PAD_LEFT)][$item] = 0;

					foreach ([
						         'MP_Achats',
						         'MS_Achats',
						         'PF_Achats',
						         'EM_Achats',
						         'PR_Achats',
						         'EL_Achats',
						         'EA_Achats'
					         ] as $item)
						$data['table2'][str_pad($i, 2, "0", STR_PAD_LEFT)][$item] = 0;

					foreach ([
						         'FU_Achats',
						         'BU_Achats',
						         'SA_Achats',
						         'SR_Achats',
						         'AM_Achats',
						         'PT_Achats',
						         'DV_Achats'
					         ] as $item)
						$data['table3'][str_pad($i, 2, "0", STR_PAD_LEFT)][$item] = 0;

					$data['totals']['ventes'][str_pad($i, 2, "0", STR_PAD_LEFT)] = 0;
					$data['totals']['achats'][str_pad($i, 2, "0", STR_PAD_LEFT)] = 0;
				}

				$data['totals']['ventes']['TOTAL'] = 0;
				$data['totals']['achats']['TOTAL'] = 0;

				foreach ([
					         'P_Ventes',
					         'T_Ventes',
					         'M_Ventes'
				         ] as $item)
					$data['table1']['TOTAL'][$item] = 0;
				foreach ([
					         'MP_Achats',
					         'MS_Achats',
					         'PF_Achats',
					         'EM_Achats',
					         'PR_Achats',
					         'EL_Achats',
					         'EA_Achats'
				         ] as $item)
					$data['table2']['TOTAL'][$item] = 0;
				foreach ([
					         'FU_Achats',
					         'BU_Achats',
					         'SA_Achats',
					         'SR_Achats',
					         'AM_Achats',
					         'PT_Achats',
					         'DV_Achats'
				         ] as $item)
					$data['table3']['TOTAL'][$item] = 0;

				$sql = "SELECT * FROM `etats_30` WHERE SUBSTRING(`Mois`, 1, 4) = {$year}";
				$result = $this
					->db
					->query($sql)
					->result_array();

				foreach ($result as $row)
				{
					$table = 0;
					if (in_array($row['Code'], [
						'P_Ventes',
						'T_Ventes',
						'M_Ventes'
					]))
					{
						$table = 1;
					}
					elseif (in_array($row['Code'], [
						'MP_Achats',
						'MS_Achats',
						'PF_Achats',
						'EM_Achats',
						'PR_Achats',
						'EL_Achats',
						'EA_Achats'
					]))
					{
						$table = 2;
					}
					elseif (in_array($row['Code'], [
						'FU_Achats',
						'BU_Achats',
						'SA_Achats',
						'SR_Achats',
						'AM_Achats',
						'PT_Achats',
						'DV_Achats'
					]))
					{
						$table = 3;
					}

					if ($table > 0)
					{
						$data["table$table"][str_replace($year, '', $row['Mois'])][$row['Code']] += $row['Montant'];
						$data["table$table"]['TOTAL'][$row['Code']] += $row['Montant'];
					}
				}

				$pdf_view_name = 'etats_recapitulatifs';
			}
			elseif ($type === 'quantité')
			{
				$data['title'] = "Etat Général Récapitulatif par mois en Quantité pour $year";
				$taux_conversion = $this->db->query("SELECT `Taux` from `taux_conversion_m_kg` where `taux_conversion_m_kg`.`Annee` = '$year'")->result()[0]->Taux;

				for ($i = 1; $i <= 12; $i ++)
				{
					foreach ([
						         'MT_Ventes',
						         'MF_Ventes',
						         'R_Ventes',
						         'F_Ventes',
						         'T_Ventes'
					         ] as $item)
						$data['table1'][str_pad($i, 2, "0", STR_PAD_LEFT)][$item] = 0;

					foreach ([
						         'MP_Achats',
						         'MS_Achats',
						         'PF_Achats',
						         'EM_Achats',
						         'PR_Achats',
						         'EL_Achats',
						         'EA_Achats'
					         ] as $item)
						$data['table2'][str_pad($i, 2, "0", STR_PAD_LEFT)][$item] = 0;

					foreach ([
						         'FU_Achats',
						         'BU_Achats'
					         ] as $item)
						$data['table3'][str_pad($i, 2, "0", STR_PAD_LEFT)][$item] = 0;

					$data['totals']['ventes'][str_pad($i, 2, "0", STR_PAD_LEFT)] = 0;
					$data['totals']['achats'][str_pad($i, 2, "0", STR_PAD_LEFT)] = 0;
				}

				$data['totals']['ventes']['TOTAL'] = 0;
				$data['totals']['achats']['TOTAL'] = 0;

				foreach ([
					         'MT_Ventes',
					         'MF_Ventes',
					         'R_Ventes',
					         'F_Ventes',
					         'T_Ventes'
				         ] as $item)
					$data['table1']['TOTAL'][$item] = 0;

				foreach ([
					         'MP_Achats',
					         'MS_Achats',
					         'PF_Achats',
					         'EM_Achats',
					         'EL_Achats',
					         'EA_Achats'
				         ] as $item)
					$data['table2']['TOTAL'][$item] = 0;

				foreach ([
					         'FU_Achats',
					         'BU_Achats'
				         ] as $item)
					$data['table3']['TOTAL'][$item] = 0;

				$sql = "SELECT * FROM `etats_31` WHERE SUBSTRING(`Mois`, 4, 4) = {$year}";
				$result = $this
					->db
					->query($sql)
					->result_array();

				foreach ($result as $row)
				{
					$table = 0;
					if (in_array($row['Code'], [
						'M_Ventes',
						'R_Ventes',
						'F_Ventes',
						'T_Ventes'
					]))
					{
						$table = 1;
					}
					elseif (in_array($row['Code'], [
						'MP_Achats',
						'MS_Achats',
						'PF_Achats',
						'EM_Achats',
						'EL_Achats',
						'EA_Achats'
					]))
					{
						$table = 2;
					}
					elseif (in_array($row['Code'], [
						'FU_Achats',
						'BU_Achats'
					]))
					{
						$table = 3;
					}

					if ($table > 0)
					{
						if ($row['Code'] == 'M_Ventes' && $row['Unite'] == 'Kilogrammes')
						{
							$row['Code'] = 'MF_Ventes';
						}
						elseif ($row['Code'] == 'M_Ventes' && $row['Unite'] == 'Mètres')
						{
							$row['Code'] = 'MT_Ventes';
							$row['Quantite'] = $row['Quantite'] * $taux_conversion;
						}

						if ($row['Unite'] == 'Mètres')
						{
							$row['Quantite'] = $row['Quantite'] * $taux_conversion;
						}

						$data["table$table"][str_replace("/$year", '', $row['Mois'])][$row['Code']] += $row['Quantite'];
						$data["table$table"]['TOTAL'][$row['Code']] += $row['Quantite'];
					}
				}

				$pdf_view_name = 'etats_recapitulatifs_quantity';
				$filename .= '_QUANTITÉ_';
			}
		}
		elseif ($period == 'an')
		{
			if ($type == 'dirhams')
			{
				$data['title'] = "Etat Général Récapitulatif Annuel en DH H.T. pour $year";

				$sql = "SELECT `Code`, SUM(`Montant`) AS Montant FROM `etats_30` WHERE SUBSTRING(`Mois`, 1, 4) = {$year} GROUP BY SUBSTRING(`Mois`, 1, 4), `Code`";
				$result = $this
					->db
					->query($sql)
					->result_array();

				$data['data']['totals']['ventes'] = 0;
				$data['data']['totals']['achats'] = 0;
				foreach ($result as $row)
				{
					$data['data'][$row['Code']] = $row['Montant'];

					if (strpos($row['Code'], '_Ventes') != false)
					{
						$data['data']['totals']['ventes'] += $row['Montant'];
					}
					elseif (strpos($row['Code'], '_Achats') != false)
					{
						$data['data']['totals']['achats'] += $row['Montant'];
					}
				}

				$pdf_view_name = 'etats_recapitulatifs_year';
				$filename .= '_YEAR_';
			}
			elseif ($type == 'quantité')
			{
				$data['title'] = "Etat Général Récapitulatif Annuel en Quantité pour $year";
				$taux_conversion = $this->db->query("SELECT `Taux` from `taux_conversion_m_kg` where `taux_conversion_m_kg`.`Annee` = '$year'")->result()[0]->Taux;

				$sql = "SELECT `Code`, `Unite`, SUM(`Quantite`) AS `Quantite` FROM `etats_31` WHERE SUBSTRING(`Mois`, 4, 4) = {$year} GROUP BY SUBSTRING(`Mois`, 4, 4), `Unite`, `Code`";
				$result = $this
					->db
					->query($sql)
					->result_array();

				$data['data']['totals']['ventes'] = 0;
				$data['data']['totals']['achats'] = 0;
				foreach ($result as $row)
				{
					if ($row['Code'] == 'M_Ventes' && $row['Unite'] == 'Kilogrammes')
					{
						$row['Code'] = 'MF_Ventes';
					}
					elseif ($row['Code'] == 'M_Ventes' && $row['Unite'] == 'Mètres')
					{
						$row['Code'] = 'MT_Ventes';
						$row['Quantite'] = $row['Quantite'] * $taux_conversion;
					}

					$data['data'][$row['Code']] = $row['Quantite'];

					if (in_array($row['Code'], [
						'MF_Ventes',
						'MT_Ventes',
						'R_Ventes',
						'F_Ventes',
						'T_Ventes'
					]))
					{
						$data['data']['totals']['ventes'] += $row['Quantite'];
					}
					elseif (in_array($row['Code'], [
						'MP_Achats',
						'MS_Achats',
						'PF_Achats',
						'EM_Achats',
						'EL_Achats',
						'EA_Achats',
						'FU_Achats',
						'BU_Achats'
					]))
					{
						$data['data']['totals']['achats'] += $row['Quantite'];
					}
				}

				$pdf_view_name = 'etats_recapitulatifs_year_quantity';
				$filename .= '_YEAR_QUANTITÈ_';
			}
		}

//$this->template->write_view('content', "pdf/$pdf_view_name", $data, true);
//$this->template->render();
//return;

		$pdf_view = $this->load->view("pdf/$pdf_view_name", $data, true);
		$filename .= $year . '_' . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';

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
		// Download the file
		$this->html2pdf->create('download');

		die('Generation Finished.');
	}

	function generate_report_portefeuille()
	{
		$data = [];
		$year = $this->input->post('year');
		$data['year'] = $year;
		//get company name
		$data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;

		// table 1
		$data['title1'] = "Etat des Effets en Portefeuille (Y compris les effets à l'encaissement non-réglées) & des effets à payer non-échus et des factures non-réglées";

		for ($i = 1; $i <= 12; $i ++)
		{
			$mois = $year . str_pad($i, 2, '0', STR_PAD_LEFT);
			$data['table1'][$mois]['Payer'] = 0;
			$data['table1'][$mois]['Portefeuille'] = 0;
			$data['table1'][$mois]['Difference'] = 0;
		}
		$data['table1']['total']['Payer'] = 0;
		$data['table1']['total']['Portefeuille'] = 0;
		$data['table1']['total']['Difference'] = 0;

		$sql = "SELECT * FROM `etats_27` WHERE SUBSTRING(`Mois`, 1, 4) = {$year}";
		$result = $this
			->db
			->query($sql)
			->result_array();

		foreach ($result as $row)
		{
			$data['table1'][$row['Mois']][$row['Code']] = $row['Montant'];
			$data['table1']['total'][$row['Code']] += $row['Montant'];
			$data['table1'][$row['Mois']]['Difference'] = $data['table1'][$row['Mois']]['Portefeuille'] - $data['table1'][$row['Mois']]['Payer'];
		}

		$data['table1']['total']['Difference'] = $data['table1']['total']['Portefeuille'] - $data['table1']['total']['Payer'];

		// table 2
		$data['title2'] = 'Les factures en cours de tous les Clients';

		$seuil_facture_non_payee = $this->db->query("SELECT value from settings where settings.key = 'app_SeuilFactureNonPayee'")->result()[0]->value;

		$sql = "
SELECT factures_clients.Num_Facture, factures_clients.Date_Facture, clients.RaisonSociale, factures_clients.Montant_Facture, factures_clients.Reste_Facture
FROM `factures_clients`
LEFT JOIN clients ON clients.Id_Client = factures_clients.Id_Client
WHERE YEAR(factures_clients.`Date_Facture`) = {$year}
AND factures_clients.Reste_Facture > {$seuil_facture_non_payee}
ORDER BY Date_Facture
        ";
		$result = $this
			->db
			->query($sql)
			->result_array();

		$data['table2'] = $result;

		// table 3
		$data['title3'] = '';

		$pdf_view = $this->load->view('pdf/etats_recapitulatifs_portefeuille', $data, true);
		$filename = "RÉCAPITULATIFS_PORTEFEUILLE_{$year}_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';

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
		// Download the file
		$this->html2pdf->create('download');

		die('Generation Finished.');
	}
}
