<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_achats extends CI_Controller {

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
		$this->template->write('title', 'États des Achats', true);
		$this->template->write('header', 'États des Achats');


		$data['code_natures'] = $this->db
			->query("SELECT e.`Code`,nm.`Code_Nature`, nm.`Description` from `etats_30` e , `nature_marchandises` nm
                     where e.`Code` like '%_Achats'
                     and nm.`Code_Nature` =  LEFT(e.`Code`,2)
                     group by  e.`Code`")
			->result();

		$this->template->write_view('content', 'tes/etats_achats', $data, true);
		$this->template->render();
	}

	function generate_report()
	{
		$data = [];
		$code_nature = explode('-', $this->input->post('code_nature'))[0];
		$description_nature = explode('-', $this->input->post('code_nature'))[1];
		$year = $this->input->post('year');
		$etats = $this->input->post('etats');

		if ($code_nature == "MP_Achats")
		{

			$this->generate_report_2();

			return;
		}

		//get company name
		$data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
		if ($etats == 'kg')
		{
			$data['title'] = " Evolution Annuelle des Achats Totaux $description_nature en " . (($description_nature == 'Eau' || $description_nature == 'Electricité') ? 'quantité' : 'Kg');
		}
		else
		{
			$data['title'] = " Evolution Annuelle des Achats Totaux $description_nature en DH. H.T";
		}


		for ($i = 3; $i >= 0; $i --)
		{
			$sql = "SELECT e.* from `etats_31` e
            where code = '$code_nature'
            and unite = 'Kilogrammes'
            and SUBSTRING_INDEX(e.`Mois`, '/', -1 ) = " . ($year - $i);
			$query = $this->db->query($sql);
			$data["poids_" . ($i + 1)] = $query->result();

			$sql = "SELECT e.* from `etats_30` e
            where code = '$code_nature'
            and LEFT(e.`Mois` , 4) = " . ($year - $i);
			$query = $this->db->query($sql);
			$data["montant_" . ($i + 1)] = $query->result();

			$sql = "SELECT sum(e.montant) as montant  from `etats_30` e
            where code = '$code_nature'
            and LEFT(e.`Mois` , 4) = " . ($year - $i);
			$query = $this->db->query($sql);
			$data["montant_year_" . ($i + 1)] = $query->result()[0]->montant;
		}

		$data['year'] = $year;
		$data['etats'] = $etats;
		$filename = "ETATS_ACHATS_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
		$pdf_view = $this->load->view('pdf/etats_achats', $data, true);

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

	public function generate_report_2()
	{

		$year = $this->input->post('year');
		$type = $this->input->post('type');
		$etats = $this->input->post('etats');

		//get company name
		$data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;
		$data['etats'] = $etats;

		if ($etats == 'dh')
		{
			$data['title_1'] = "Evolution Annuelle des Achats MP / ARTICLE EN DH. H.T";
			$sql = "select * from etats_36_a where year in ($year, $year - 1 , $year - 2, $year - 3) group by year, description";
			$query = $this->db->query($sql);
			$data['result_1'] = $query->result();


			$data['title_2'] = "Evolution Annuelle du prix d'achats MP / ARTICLE EN DH. H.T";
			$sql = "select * from etats_36_b where year in ($year, $year - 1 , $year - 2, $year - 3) group by year, description";
			$query = $this->db->query($sql);
			$data['result_2'] = $query->result();

			$data['avg_1'] = $this->db->query('select avg(`montant`) as montant from etats_36_b where year = ' . $year)->result_array()[0]['montant'];
			$data['avg_2'] = $this->db->query('select avg(`montant`) as montant from etats_36_b where year = ' . ($year - 1))->result_array()[0]['montant'];
			$data['avg_3'] = $this->db->query('select avg(`montant`) as montant from etats_36_b where year = ' . ($year - 2))->result_array()[0]['montant'];
			$data['avg_4'] = $this->db->query('select avg(`montant`) as montant from etats_36_b where year = ' . ($year - 3))->result_array()[0]['montant'];

		}
		else
		{
			$data['title_1'] = "Evolution Annuelle des Achats MP / ARTICLE EN KG";
			$sql = "select * from etats_37_a where year in ($year, $year - 1 , $year - 2, $year - 3) group by year, description";
			$query = $this->db->query($sql);
			$data['result_1'] = $query->result();


			$data['title_2'] = "Evolution Annuelle de la quantité d'achats MP / ARTICLE EN KG";
			$sql = "select * from etats_37_b where year in ($year, $year - 1 , $year - 2, $year - 3) group by year, description";
			$query = $this->db->query($sql);
			$data['result_2'] = $query->result();

			$data['avg_1'] = $this->db->query('select avg(`poids`) as poids from etats_37_b where year = ' . $year)->result_array()[0]['poids'];
			$data['avg_2'] = $this->db->query('select avg(`poids`) as poids from etats_37_b where year = ' . ($year - 1))->result_array()[0]['poids'];
			$data['avg_3'] = $this->db->query('select avg(`poids`) as poids from etats_37_b where year = ' . ($year - 2))->result_array()[0]['poids'];
			$data['avg_4'] = $this->db->query('select avg(`poids`) as poids from etats_37_b where year = ' . ($year - 3))->result_array()[0]['poids'];
		}


		$sql = "select description from `etats_36_a` group by description";
		$query = $this->db->query($sql);
		$data['articles'] = $query->result();


		$data['year'] = $year;

		$filename = "ETATS_ACHATS_MP_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
		$pdf_view = $this->load->view('pdf/etats_achats_mp', $data, true);

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


	public function generate_report_3()
	{

		$code_nature = explode('-', $this->input->post('code_nature'))[0];
		$code_nature = substr($code_nature, 0, 2);
		$description_nature = explode('-', $this->input->post('code_nature'))[1];
		$year = $this->input->post('year');


		//get company name
		$data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;


		if ($code_nature == "MP")
		{
			$data['title'] = "LISTE DES ACHATS MATIERE PREMIERE";
		}
		else
		{
			$data['title'] = "LISTE DES ACHATS " . strtoupper($description_nature);
		}


		$sql = "SELECT ai.Num_DUM as NUM_DUM_NUM_FACTURE, ai.`Date_Facture`, f.`RaisonSociale`, ai.`Marchandises`, ai.`Poids_Net` as 'poids', ai.`Montant_MAD` as 'montant', (ai.`Montant_MAD` / ai.`Poids_Net`) as  'PU'
                FROM achats_import ai, `fournisseurs` f, `nature_marchandises` am
                where ai.`Id_Fournisseur` = f.`Id_Fournisseur`
                and am.`Id_Nature` = ai.`Id_Nature`
                and am.`Code_Nature` = '$code_nature'
                and YEAR(ai.`Date_Created`) = $year
                UNION DISTINCT
                SELECT al.Num_Facture as NUM_DUM_NUM_FACTURE , al.`Date_Facture`, f.`RaisonSociale`, al.`Marchandises`, al.`Poids` as 'poids', al.`Montant_HT` as 'montant', (al.`Montant_HT` / al.`Marchandises`) as  'PU'
                FROM achats_locaux al, `fournisseurs` f, `nature_marchandises` am
                where al.`Id_Fournisseur` = f.`Id_Fournisseur`
                and am.`Id_Nature` = al.`Id_Nature`
                and am.`Code_Nature` = '$code_nature'
                and YEAR(al.`Date_Created`) = $year
                order by Date_Facture, NUM_DUM_NUM_FACTURE";


		$query = $this->db->query($sql);
		$data["result"] = $query->result_array();


		$sql = "SELECT sum(ai.`Poids_Net`) as 'poids', sum(ai.`Montant_MAD`) as 'montant', sum(ai.`Montant_MAD` / ai.`Poids_Net`) as  'PU'
                FROM achats_import ai,  `nature_marchandises` am
                where am.`Id_Nature` = ai.`Id_Nature`
                and am.`Code_Nature` =  '$code_nature'
                and YEAR(ai.`Date_Created`) = $year";
		$query = $this->db->query($sql);
		$data["importation"] = $query->result_array()[0];

		$sql = "SELECT sum(ai.`Poids`) as 'poids', sum(ai.`Montant_HT`) as 'montant', sum(ai.`Montant_HT` / ai.`Poids`) as  'PU'
                FROM achats_locaux ai,  `nature_marchandises` am
                where am.`Id_Nature` = ai.`Id_Nature`
                and am.`Code_Nature` = '$code_nature'
                and YEAR(ai.`Date_Created`) =  $year";
		$query = $this->db->query($sql);
		$data["marche_local"] = $query->result_array()[0];

		$sql = "SELECT count(al.`Num_Facture`) as nbr from `achats_locaux` al , `nature_marchandises` am
                where al.Id_Nature = am.`Id_Nature`
                and am.`Code_Nature` = '$code_nature'
                and YEAR(al.`Date_Created`) = $year";
		$query = $this->db->query($sql);
		$data["nbr_facture"] = $query->result()[0]->nbr;


		$sql = "SELECT count(al.Num_DUM) as nbr from `achats_import` al  , `nature_marchandises` am
                where al.Id_Nature = am.`Id_Nature`
                and am.`Code_Nature` = '$code_nature'
                and YEAR(al.`Date_Created`) = $year";
		$query = $this->db->query($sql);
		$data["nbr_DUM"] = $query->result()[0]->nbr;


		if ($code_nature == "MP" || $code_nature == "EM")
		{
			$sql = "SELECT `Code`, `Description`, sum(poids) AS 'poids', sum(montant) as 'montant', (sum(montant) / sum(poids)) as 'PU' from  
                        (
                            SELECT  cm.`Code` ,cm.`Description`, sum(ai.`Poids_Net`) as 'poids', sum(ai.`Montant_MAD`) as 'montant', (sum(ai.`Montant_MAD`) / sum(ai.`Poids_Net`)) as  'PU'
                            FROM achats_import ai, `fournisseurs` f, `nature_marchandises` am, `code_marchandises` cm
                            where ai.`Id_Fournisseur` = f.`Id_Fournisseur`
                            and cm.`Id_Code` = ai.`Id_Code`
                            and am.`Id_Nature` = cm.`Id_Nature`
                            and am.`Code_Nature` = '$code_nature'
                            and YEAR(ai.`Date_Created`) = $year
                            group by cm.`Code`
                            UNION all
                            SELECT cm.`Code` ,cm.`Description`, sum(al.`Poids`) as 'poids', sum(al.`Montant_HT`) as 'montant', (sum(al.`Montant_HT`) / sum(al.`Poids`)) as  'PU'
                            FROM achats_locaux al, `fournisseurs` f, `nature_marchandises` am, `code_marchandises` cm
                            where al.`Id_Fournisseur` = f.`Id_Fournisseur`
                            and cm.`Id_Code` = al.`Id_Code`
                            and am.`Id_Nature` = cm.`Id_Nature`
                            and am.`Code_Nature` = '$code_nature'
                            and YEAR(al.`Date_Created`) = $year
                            group by cm.`Code`
                        )  t group by Code";


			$query = $this->db->query($sql);
			$data["result_2"] = $query->result_array();

			$data['totalPoids'] = array_sum(array_map(function ($var)
			{
				return $var['poids'];
			}, $data["result_2"]));


			if ($code_nature == "MP")
			{
				$data['fibres'] = array_values(array_filter($data["result_2"], function ($var)
				{
					return strstr($var['Description'], "Fibre");
				}));

				$data['totalPoids_fibres'] = array_sum(array_map(function ($var)
				{
					return $var['poids'];
				}, $data["fibres"]));

				$data['fils'] = array_values(array_filter($data["result_2"], function ($var)
				{
					return strstr($var['Description'], "Fil");
				}));

				$data['totalPoids_fils'] = array_sum(array_map(function ($var)
				{
					return $var['poids'];
				}, $data["fils"]));

			}

		}


		$data['year'] = $year;
		$filename = "ETATS_ACHATS_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';

		$pdf_view = $this->load->view('pdf/etats_achats_detaillee', $data, true);


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


	public function generate_report_4()
	{
		$code_nature = explode('-', $this->input->post('code_nature'))[0];
		$description_nature = explode('-', $this->input->post('code_nature'))[1];
		$year = $this->input->post('year');
		$etats = $this->input->post('etats');

		//get company name
		$data['company_name'] = $this->db->query("SELECT value from settings where settings.key = 'app_nom'")->result()[0]->value;

		if ($etats == 'kg')
		{
			$data['title'] = " Résumé annuel des Achats $description_nature pour $year";

			$sql = "SELECT e.* from `etats_31` e
            where code = '$code_nature'
            and SUBSTRING_INDEX(e.`Mois`, '/', -1 ) = $year";
			$query = $this->db->query($sql);
			$data['result'] = $query->result();
		}
		else
		{
			$data['title'] = " Résumé annuel des Achats en DH H.T. pour $year";

			$sql = "SELECT e.* from `etats_30` e
            where code = '$code_nature'
            and LEFT(Mois , 4)  = $year";
			$query = $this->db->query($sql);
			$data['result'] = $query->result();
		}


		$sql = "SELECT sum(`Quantite`) As 'quantite' from `etats_31` e
        where code = '$code_nature'
        and SUBSTRING_INDEX(e.`Mois`, '/', -1 ) = $year";
		$query = $this->db->query($sql);
		$data['total_quantity'] = $query->result()[0]->quantite;

		$sql = "SELECT sum(`Montant`) As 'montant' from `etats_30` e
        where code = '$code_nature'
        and  LEFT(Mois , 4)  = $year";
		$query = $this->db->query($sql);
		$data["total_amount"] = $query->result()[0]->montant;

		$data['year'] = $year;
		$data['etats'] = $etats;
		$filename = "ETATS_ACHATS_" . (new DateTime())->format("dmY_Hi") . '_' . (new DateTime())->getTimestamp() . '.pdf';
		$pdf_view = $this->load->view('pdf/etats_achats_resume', $data, true);

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
