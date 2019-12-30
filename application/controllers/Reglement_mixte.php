<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reglement_mixte extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('general_helper');
		$this->db = $this->load->database(get_current_db(), true);
		$this->template->write_view('sidenavs', 'template/default_sidenavs.php', true);
		$this->template->write_view('navs', 'template/default_topnavs.php', true);
	}

	function index()
	{
		$this->template->write('title', 'Règlement Mixte', true);
		$this->template->write('header', 'Règlement Mixte');
		$this->template->write('head', '<link type="text/css" rel="stylesheet" href="assets/grocery_crud/themes/flexigrid/css/flexigrid.css"/>');
		$this->template->write('style', '
			.flexigrid div.bDiv td {
				padding: 0 5px;
			}
		');
		$this->template->write('scripts', '');
		$this->template->write('javascript', '');

		$data['state'] = 'index';
		$data['reglements'] = [];

		$reglements = $this->db->get('reglement_mixte')->result_array();

		foreach ($reglements as $reglement) {
			$sql = "SELECT * FROM factures_clients WHERE id_reglement_mixte = {$reglement['id_reglement_mixte']};";
			$query = $this->db->query($sql);
			$factures = $query->result_array();
			if (count($factures) === 0) {
				continue;
			}

			$factures_array = array_map(function ($facture) {
				return $facture['Num_Facture'] . '/' . (new DateTime($facture['Date_Facture']))->format('Y');
			}, $factures);

			$data['reglements'][$reglement['id_reglement_mixte']]['factures_string'] = implode(', ', $factures_array);

			if (count($factures_array) === 0) {
				continue;
			}

			$sql = 'SELECT `reglement_mixte`.`id_reglement_mixte`, `factures_clients`.`Id_Facture`, `factures_clients`.`Num_Facture` AS `Num_Facture`, YEAR(`factures_clients`.`Date_Facture`) AS `facture_year`
					FROM `factures_clients`
					JOIN `reglement_mixte` ON `reglement_mixte`.`id_reglement_mixte` = `factures_clients`.`id_reglement_mixte`
					WHERE `factures_clients`.`id_reglement_mixte` = ' . $reglement['id_reglement_mixte'] . ';';
			$query = $this->db->query($sql);
			$facture = $query->row();

			if ($facture === null) {
				continue;
			}

			$cheques = $this->get_cheques_by_id($reglement['id_reglement_mixte']);
			$effets = $this->get_effets_by_id($reglement['id_reglement_mixte']);
			$virements = $this->get_virements_by_id($reglement['id_reglement_mixte']);

			$cheques_string = array_unique($cheques[$reglement['id_reglement_mixte']]['cheques_string']);
			$data['reglements'][$reglement['id_reglement_mixte']]['cheques_string'] = implode(', ', $cheques_string);

			$effets_string = array_unique($effets[$reglement['id_reglement_mixte']]['effets_string']);
			$data['reglements'][$reglement['id_reglement_mixte']]['effets_string'] = implode(', ', $effets_string);

			$virements_string = array_unique($virements[$reglement['id_reglement_mixte']]['virements_string']);
			$data['reglements'][$reglement['id_reglement_mixte']]['virements_string'] = implode(', ', $virements_string);
		}

		krsort($data['reglements']);

		$this->template->write('content', $this->load->view('tes/reglement_mixte', ['data' => $data], true));
		$this->template->render();
	}

	public function show($id_reglement_mixte = null)
	{
		$this->template->write('title', 'Règlement Mixte', true);
		$this->template->write('header', 'Règlement Mixte');
		$this->template->write('style', '.panel-header {padding: 15px;} .panel-body h5 {font-size: 18xpx;}');
		$this->template->write('scripts', '');
		$this->template->write('javascript', '');

		$data['state'] = 'show';
		$data['reglements_id'] = $id_reglement_mixte;

		$sql = "SELECT * FROM factures_clients WHERE id_reglement_mixte = $id_reglement_mixte;";
		$query = $this->db->query($sql);
		$factures = $query->result_array();
		$data['factures'] = $factures;

		//EFFETS SQL
		$sql = "SELECT
                    e.Num_Effet,
                    f.Num_Facture as Num_Facture,
                    e.Date_Echeance As 'ECHEANCE',
                    e.Montant as 'MONTANT',
                    e.Tire as 'TIRE',
                    e.Endosseur as 'ENDOSSEUR',
                    b.Nom_Banque as 'DOMICILIATION',
                    e.Ville as 'VILLE'
                FROM
                    factures_clients f, 
                    effets_clients e, 
                    banques b
                WHERE f.Id_Facture = e.Id_Facture
                AND b.Id_Banque = e.Id_Banque
                AND f.id_reglement_mixte = $id_reglement_mixte";

		$query = $this->db->query($sql);
		$data['effets'] = $query->result_array();

		//CHEQUE SQL
		$sql = "SELECT 
                    c.Num_Cheque As 'NUM CHEQUE',
                    f.Num_Facture as Num_Facture,
                    c.Montant_Cheque as 'MONTANT',
                    c.Tire as 'TIRE',
                    c.Endosseur as 'ENDOSSEUR',
                    b.Nom_Banque as 'DOMICILIATION',
                    c.Ville as 'VILLE',
                    c.Num_Remise as 'NUM REMISE',
                    c.Date_Remise as 'DATE REMISE'
                FROM 
                    factures_clients f, 
                    cheques c, 
                    banques b
                WHERE f.Id_Facture = c.Id_Facture  
                AND b.Id_Banque = c.Id_Banque
                AND f.id_reglement_mixte = $id_reglement_mixte";
		$query = $this->db->query($sql);
		$data['cheques'] = $query->result_array();

		//Virement SQL
		$sql = "SELECT 
                    v.Num_Operation As 'NUM VERSEMENT',
                    f.Num_Facture as Num_Facture,
                    v.Montant as 'MONTANT',
                    v.Date_Created as 'DATE VERSEMENT'
                FROM 
                    factures_clients f, 
                    virements_versements v
                WHERE f.Id_Facture = v.Id_Facture  
                AND f.id_reglement_mixte = $id_reglement_mixte";

		$query = $this->db->query($sql);
		$data['virements'] = $query->result_array();

		//Get the client
		$sql = "SELECT c.Id_Client, c.RaisonSociale 
                FROM clients c, factures_clients f
                WHERE c.Id_Client = f.Id_Client 
                AND f.id_reglement_mixte = $id_reglement_mixte";
		$query = $this->db->query($sql);
		$data['client'] = $query->row_array();

		//Get Date facture
		$sql = "SELECT f.Num_Facture, f.Date_Facture, f.Montant_Facture
                FROM factures_clients f
                WHERE f.id_reglement_mixte = $id_reglement_mixte;";
		$query = $this->db->query($sql);
		$row = $query->row_array();
		if (!empty($row)) {
			$data[$row['Num_Facture']]['date_facture'] = $row['Date_Facture'];
			$data[$row['Num_Facture']]['total_facture'] = $row['Montant_Facture'];
		} else {
			$data[$row['Num_Facture']]['date_facture'] = '';
			$data[$row['Num_Facture']]['total_facture'] = 0;
		}

		$total_general = 0;
		//calcul total facture
		if (!empty($data[$row['Num_Facture']]['effets'])) {
			$total_general += array_sum(array_map(function ($effet) {
				return $effet['MONTANT'];
			}, $data[$row['Num_Facture']]['effets']));
		}
		if (!empty($data[$row['Num_Facture']]['cheques'])) {
			$total_general += array_sum(array_map(function ($effet) {
				return $effet['MONTANT'];
			}, $data[$row['Num_Facture']]['cheques']));
		}
		if (!empty($data[$row['Num_Facture']]['virement'])) {
			$total_general += array_sum(array_map(function ($effet) {
				return $effet['MONTANT'];
			}, $data[$row['Num_Facture']]['virement']));
		}

		$data[$row['Num_Facture']]['total_general'] = $total_general;

		$this->template->write('content', $this->load->view('tes/reglement_mixte', ['data' => $data], true));
		$this->template->render();
	}

	public function add()
	{
		$data['state'] = 'add';

		if ('POST' == $this->input->server('REQUEST_METHOD')) {
			$factures_ids = $this->db->escape($this->input->post('factures'));
			$factures_ids = str_replace('\'', '', $factures_ids);

			$this->db->insert('reglement_mixte', array('Id_creator' => $this->session->get_userdata()['user_id']));
			$reglement_mixte_id = $this->db->insert_id();

			$sql = "UPDATE `factures_clients`
                    SET `factures_clients`.`id_reglement_mixte`=$reglement_mixte_id
                    WHERE `factures_clients`.`Id_Facture` IN ($factures_ids);";
			$this->db->simple_query($sql);

			$montant = 0;

			$sql = "SELECT Id_Facture, sum(Montant_Cheque) AS `Montant` FROM cheques
               WHERE Id_Facture in ($factures_ids)
               GROUP BY Id_Facture";
			$query = $this->db->query($sql);
			$montant += $query->row() ? $query->row()->Montant : 0;

			$sql = "select sum(Montant) AS `Montant` FROM effets_clients
                WHERE Id_Facture in ($factures_ids)
                GROUP BY Id_Facture";
			$query = $this->db->query($sql);
			$montant += $query->row() ? $query->row()->Montant : 0;

			$sql = "select sum(Montant) AS `Montant` FROM virements_versements
                WHERE Id_Facture in ($factures_ids)
                GROUP BY Id_Facture";
			$query = $this->db->query($sql);
			$montant += $query->row() ? $query->row()->Montant : 0;

			//SELECT FACTURES
			$sql = "SELECT * FROM `factures_clients`
            WHERE `factures_clients`.`id_reglement_mixte`=$reglement_mixte_id 
            ORDER BY `factures_clients`.`Num_Facture` ";
			$q = $this->db->query($sql);
			$factures = $q->result();

			foreach ($factures as $facture) {
				$rest = 0;
				if ($facture->Reste_Facture > $montant) {
					$rest = $facture->Reste_Facture - $montant;
				} else {
					$montant -= $facture->Reste_Facture;
				}

				$sql = "UPDATE `factures_clients`
                    SET `factures_clients`.`Reste_Facture`=$rest
                    WHERE `factures_clients`.`Id_Facture`={$facture->Id_Facture};";
				$this->db->simple_query($sql);
			}

			header('Content-Type: application/json');
			echo json_encode(array(), true);
			exit(0);
		}

		//SELECT FACTURES
		$sql = "SELECT `factures_clients`.`Id_Facture`, `factures_clients`.`Num_Facture`, `factures_clients`.`Date_Facture`
            FROM `factures_clients`, `settings`
            WHERE id_reglement_mixte IS NULL 
            AND `factures_clients`.`Reste_Facture` > `settings`.`value`
            AND `settings`.`key` = 'app_SeuilFactureNonPayee'
            ORDER BY `factures_clients`.`Date_Facture` ASC, `factures_clients`.`Num_Facture` ";
		$q = $this->db->query($sql);
		$data['factures'] = $q->result();

		$this->template->write('content', $this->load->view('tes/reglement_mixte', ['data' => $data], true));
		$this->template->render();
	}

	public function delete($id_reglement_mixte = null)
	{
		$sql = 'UPDATE `factures_clients` SET `factures_clients`.`id_reglement_mixte` IS null WHERE `factures_clients`.`id_reglement_mixte`=' . $this->db->escape($id_reglement_mixte);
		$this->db->simple_query($sql);

		$sql = 'DELETE FROM `reglement_mixte` WHERE `reglement_mixte`.`id_reglement_mixte`=' . $this->db->escape($id_reglement_mixte);
		$this->db->simple_query($sql);

		redirect($_SERVER['HTTP_REFERER']);

		exit(0);
	}

	private function get_cheques_by_id($id_reglement_mixte)
	{
		$data[$id_reglement_mixte]['factures_string'] = [];
		$data[$id_reglement_mixte]['cheques_string'] = [];

		$sql = 'SELECT `cheques`.`Id_Facture`, `cheques`.`Num_Cheque` AS "num", YEAR(`cheques`.`Date_Created`) AS "year", `factures_clients`.`Num_Facture` AS `Num_Facture`, YEAR(`factures_clients`.`Date_Facture`) AS `facture_year`
				FROM `cheques`
				JOIN `factures_clients` ON `cheques`.`Id_Facture` = `factures_clients`.`Id_Facture`
				JOIN `reglement_mixte` ON `reglement_mixte`.`id_reglement_mixte` = `factures_clients`.`id_reglement_mixte`
				WHERE `factures_clients`.`id_reglement_mixte` = ' . $id_reglement_mixte;
		$q = $this->db->query($sql);
		$cheques = $q->result();

		foreach ($cheques as $cheque) {
			$data[$id_reglement_mixte]['cheques'][] = $cheque;
			$data[$id_reglement_mixte]['cheques_string'][] = "$cheque->num/$cheque->year";
			$data[$id_reglement_mixte]['factures_string'][] = "$cheque->Num_Facture/$cheque->facture_year";
		}

		return $data;
	}

	private function get_effets_by_id($id_reglement_mixte)
	{
		$data[$id_reglement_mixte]['factures_string'] = [];
		$data[$id_reglement_mixte]['effets_string'] = [];

		$sql = 'SELECT `effets_clients`.`Id_Facture`, `effets_clients`.`Num_Effet` AS "num", YEAR(`effets_clients`.`Date_Created`) AS "year", `factures_clients`.`Num_Facture` AS `Num_Facture`, YEAR(`factures_clients`.`Date_Facture`) AS `facture_year`
				FROM `effets_clients`
				JOIN `factures_clients` ON `effets_clients`.`Id_Facture` = `factures_clients`.`Id_Facture`
				JOIN `reglement_mixte` ON `reglement_mixte`.`id_reglement_mixte` = `factures_clients`.`id_reglement_mixte`
				WHERE `factures_clients`.`id_reglement_mixte` = ' . $id_reglement_mixte;
		$q = $this->db->query($sql);
		$effets = $q->result();

		foreach ($effets as $effet) {
			$data[$id_reglement_mixte]['effets'][] = $effet;
			$data[$id_reglement_mixte]['effets_string'][] = "$effet->num/$effet->year";
			$data[$id_reglement_mixte]['factures_string'][] = "$effet->Num_Facture/$effet->facture_year";
		}

		return $data;
	}

	private function get_virements_by_id($id_reglement_mixte)
	{
		$data[$id_reglement_mixte]['factures_string'] = [];
		$data[$id_reglement_mixte]['virements_string'] = [];

		$sql = 'SELECT `virements_versements`.`Id_Facture`, `virements_versements`.`Num_Operation` AS "num", YEAR(`virements_versements`.`Date_Created`) AS "year", `factures_clients`.`Num_Facture` AS `Num_Facture`, YEAR(`factures_clients`.`Date_Facture`) AS `facture_year`
				FROM `virements_versements`
				JOIN `factures_clients` ON `virements_versements`.`Id_Facture` = `factures_clients`.`Id_Facture`
				JOIN `reglement_mixte` ON `reglement_mixte`.`id_reglement_mixte` = `factures_clients`.`id_reglement_mixte`
				WHERE `factures_clients`.`id_reglement_mixte` = ' . $id_reglement_mixte;
		$q = $this->db->query($sql);
		$virements = $q->result();

		foreach ($virements as $virement) {
			$data[$id_reglement_mixte]['virements'][] = $virement;
			$data[$id_reglement_mixte]['virements_string'][] = "$virement->num/$virement->year";
			$data[$id_reglement_mixte]['factures_string'][] = "$virement->Num_Facture/$virement->facture_year";
		}

		return $data;
	}

	public function get_data()
	{
		$factures = implode(',', array_map(function ($facture) {
			return json_decode($facture)->id_facture;
		}, $this->input->post('factures', true)));

		$sql = "SELECT c.*, YEAR(f.Date_Facture) AS `year` FROM cheques c, factures_clients f
               WHERE c.Id_Facture = f.Id_Facture
               AND f.Id_Facture in ({$factures})";
		$query = $this->db->query($sql);
		$data['cheques'] = $query->result_array();

		$sql = "select c.*, YEAR(f.Date_Facture) AS `year` FROM effets_clients c, factures_clients f
                WHERE c.Id_Facture = f.Id_Facture
                AND f.Id_Facture in ({$factures})";
		$query = $this->db->query($sql);
		$data['effets'] = $query->result_array();

		$sql = "SELECT c.*, YEAR(f.Date_Facture) AS `year` FROM virements_versements c, factures_clients f
                WHERE c.Id_Facture = f.Id_Facture
                AND f.Id_Facture in ({$factures})";
		$query = $this->db->query($sql);
		$data['virements'] = $query->result_array();

		header('Content-Type: application/json');
		echo json_encode($data, true);
		exit(0);
	}
}
