<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller
 */
class Dashboard extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->load->helper('general_helper');

        if (!is_user_logged_in()) {
            redirect('login');
        }

        $this->load->helper('url');
        $this->load->library('grocery_CRUD');
        $this->db = $this->load->database(get_current_db(), TRUE);
        
        $this->template->write_view('sidenavs', 'template/default_sidenavs', true);
        $this->template->write_view('navs', 'template/default_topnavs.php', true);
    }

    function index()
    {
        $data['factures_year'] = $this->db->query("SELECT count(*) AS value FROM `factures_clients` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE)")->result()[0]->value;
        $data['effets_year'] = $this->db->query("SELECT count(*) AS value FROM `effets_clients` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE)")->result()[0]->value;
        $data['cheques_year'] = $this->db->query("SELECT count(*) AS value FROM `cheques` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE)")->result()[0]->value;
        $data['virements_year'] = $this->db->query("SELECT count(*) AS value FROM `virements_versements` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE)")->result()[0]->value;

        $data['factures_month'] = $this->db->query("SELECT count(*) AS value FROM `factures_clients` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE)")->result()[0]->value;
        $data['effets_month'] = $this->db->query("SELECT count(*) AS value FROM `effets_clients` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE)")->result()[0]->value;
        $data['cheques_month'] = $this->db->query("SELECT count(*) AS value FROM `cheques` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE)")->result()[0]->value;
        $data['virements_month'] = $this->db->query("SELECT count(*) AS value FROM `virements_versements` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE)")->result()[0]->value;

        $data['factures_day'] = $this->db->query("SELECT count(*) AS value FROM `factures_clients` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE) AND DAY(`Date_Created`) = DAY(CURRENT_DATE)")->result()[0]->value;
        $data['effets_day'] = $this->db->query("SELECT count(*) AS value FROM `effets_clients` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE) AND DAY(`Date_Created`) = DAY(CURRENT_DATE)")->result()[0]->value;
        $data['cheques_day'] = $this->db->query("SELECT count(*) AS value FROM `cheques` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE) AND DAY(`Date_Created`) = DAY(CURRENT_DATE)")->result()[0]->value;
        $data['virements_day'] = $this->db->query("SELECT count(*) AS value FROM `virements_versements` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE) AND DAY(`Date_Created`) = DAY(CURRENT_DATE)")->result()[0]->value;

        $data['engagements_year'] = $this->db->query("SELECT count(*) AS value FROM `engagements_importation` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE)")->result()[0]->value;
        $data['import_year'] = $this->db->query("SELECT count(*) AS value FROM `achats_import` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE)")->result()[0]->value;
        $data['credocs_year'] = $this->db->query("SELECT count(*) AS value FROM `credocs_fournisseurs` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE)")->result()[0]->value;
        $data['fournisseurs_year'] = $this->db->query("SELECT count(*) AS value FROM `effets_fournisseurs` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE)")->result()[0]->value;

        $data['engagements_month'] = $this->db->query("SELECT count(*) AS value FROM `engagements_importation` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE)")->result()[0]->value;
        $data['import_month'] = $this->db->query("SELECT count(*) AS value FROM `achats_import` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE)")->result()[0]->value;
        $data['credocs_month'] = $this->db->query("SELECT count(*) AS value FROM `credocs_fournisseurs` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE)")->result()[0]->value;
        $data['fournisseurs_month'] = $this->db->query("SELECT count(*) AS value FROM `effets_fournisseurs` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE)")->result()[0]->value;

        $data['engagements_day'] = $this->db->query("SELECT count(*) AS value FROM `engagements_importation` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE) AND DAY(`Date_Created`) = DAY(CURRENT_DATE)")->result()[0]->value;
        $data['import_day'] = $this->db->query("SELECT count(*) AS value FROM `achats_import` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE) AND DAY(`Date_Created`) = DAY(CURRENT_DATE)")->result()[0]->value;
        $data['credocs_day'] = $this->db->query("SELECT count(*) AS value FROM `credocs_fournisseurs` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE) AND DAY(`Date_Created`) = DAY(CURRENT_DATE)")->result()[0]->value;
        $data['fournisseurs_day'] = $this->db->query("SELECT count(*) AS value FROM `effets_fournisseurs` WHERE YEAR(`Date_Created`) = YEAR(CURRENT_DATE) AND MONTH(`Date_Created`) = MONTH(CURRENT_DATE) AND DAY(`Date_Created`) = DAY(CURRENT_DATE)")->result()[0]->value;


        $this->template->write('title', 'Tableau de Bord', TRUE);
        $this->template->write('header', 'Tableau de Bord');
        $this->template->write('style', "");
        $this->template->write_view('content', 'tes/dashboard', $data, true);
        $this->template->render();
    }

}