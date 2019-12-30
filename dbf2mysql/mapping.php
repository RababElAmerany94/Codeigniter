<?php

if(isset($_COOKIE["ConnectionString"])) {
	list($hostname,$user,$password,$db) = explode(":",$_COOKIE["ConnectionString"]);
} elseif(isset($_POST["ConnectionString"])) {
	list($hostname,$user,$password,$db) = explode(":",$_POST["ConnectionString"]);
	setcookie("ConnectionString",$_POST["ConnectionString"], time()+3600*24*15);
} else {
	?><form method="POST">hostname:user:password:database <input type="text" name="ConnectionString"/></form><?php
	die();
}

$mysqli = new mysqli($hostname, $user, $password, $db);
$mysqli_aux = new mysqli($hostname, $user, $password, $db);
		

// dbf <=> table mapping
$table = array("dbfClients" => "clients", 
                "dbfFactures" => "factures_clients",
                "dbfEffets" => "effets_clients",
                "dbfCheques" => "cheques",
                "dbfVirementsVersements" => "virements_versements",
                "dbfFournisseurs" => "fournisseurs",
                "dbfDUM" => "achats_import",
                "dbfCredocs" => "credocs_fournisseurs",
                "dbfAchats" => "achats_locaux",
                "dbfAvoirs" => "avoirs",
                "dbfEngImport" => "engagements_importation",
            	"dbfSalaires" => "salaires");

// Note factures mapping
$note_facture = array("P" => 'Produits Finis',
    "R" => 'Produits Résiduels',
    "T" => 'Travaux et Façonnage',
    "M" =>'Marchandises');

$banque = array('' => -1,
 'SGMB'=> 8,
 'WB'=> 2,
 'BCM'=> 5,
 'BMCE'=> 3,
 'BMCI'=> 4,
 'BCMI'=> 4,
 'AB'=> 21,
 'UNIBAN'=> 23,
 'CA'=> 9,
 'BP'=> 1,
 'CM'=> 5,
 'BMAO'=> 45,
 'CIH'=> 6,
 'CNCA'=> 46,
 'CDM'=> 5,
 'UB'=> 11,
 'UMB'=> 11,
 'SMDC'=> 8,
 'bcm'=> 47,
 'B.P'=> 1,
 'ABN-AM'=> 48,
 'ABN-AMRO'=> 48,
 'ATWB'=> 2,
 'ATWM'=> 2,
 'ATMB'=> 2,
 'BARID BQ' => 20,
 'UMNIA BK' => 49);

$nature = array('' => -1,
	"MP" => 1,
	"PF" => 2,
	"EM" => 3,
	"PR" => 4,
	"MO" => 5,
	"MS" => 6,
	"EA" => 7,
	"EL" => 8,
	"FU" => 9,
	"BU" => 10,
	"EN" => 11,
	"PT" => 12,
	"DV" => 13,
	"LO" => 14,
	"AM" => 15,
	"SR" => 16);

$etat_effet = array("" => NULL, "ENC" => "Encaissement", "ESC" => "Escompte");

function getIdFacture($n_fact, $d_fact) {
	if(empty($n_fact) || empty($d_fact)) 
		return null;
	
	$n_fact = preg_replace("/[^0-9]/", "", $n_fact);
	$yr_fact = substr($d_fact, 0, 4); 
	
	global $mysqli_aux;
	$query = "SELECT Id_Facture FROM `factures_clients` 
		WHERE Num_Facture = '$n_fact'
		AND EXTRACT(YEAR FROM Date_Facture) = '$yr_fact'";
	$row = $mysqli_aux->query($query);
	
	if(!$row) 
		return null;
	
	return mysqli_fetch_row($row)[0];
}

function getIdEngagement($Num_Engagement,$Date) {
	if(empty($Num_Engagement) || empty($Date)) 
		return null;
	
	global $mysqli_aux;
	$query = "SELECT Id_Engagement FROM `engagements_importation` 
		WHERE Num_Engagement = '$Num_Engagement'
		AND Date = '$Date'";
	$row = $mysqli_aux->query($query);
	
	if(!$row) 
		return null;
	
	return mysqli_fetch_row($row)[0];
}

function getIdFournisseur($RaisonSociale) {
	if(empty($RaisonSociale)) 
		return null;
	$RaisonSociale = trim($RaisonSociale);

	global $mysqli_aux;
	$query = "SELECT Id_Fournisseur FROM `fournisseurs` 
		WHERE RaisonSociale = '$RaisonSociale'";
	$row = $mysqli_aux->query($query);
	
	if(!$row) 
		return null;

	return mysqli_fetch_row($row)[0];
}

function getIdAchatImport($Num_DUM,$Date_DUM) {
	if(empty($Num_DUM) || empty($Date_DUM))
		return null;
	
	global $mysqli_aux;
	$query = "SELECT Id_Achat FROM `achats_import` 
		WHERE Num_DUM = '$Num_DUM'
		AND Date_DUM = '$Date_DUM'";
	$row = $mysqli_aux->query($query);
	
	if(!$row) 
		return null;
	
	return mysqli_fetch_row($row)[0];	
}

function getIdAchatLocal($Num_Facture,$Date_Facture) {
	if(empty($Num_Facture) || empty($Date_Facture))
		return null;
	
	global $mysqli_aux;
	$query = "SELECT Id_Achat FROM `achats_locaux` 
		WHERE Num_Facture = '$Num_Facture'
		AND Date_Facture = '$Date_Facture'";
	$row = $mysqli_aux->query($query);
	
	if(!$row) 
		return null;
	
	return mysqli_fetch_row($row)[0];	
}

?>