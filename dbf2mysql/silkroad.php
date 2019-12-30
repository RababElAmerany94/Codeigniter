<?php header('Content-type: text/html; charset=iso-8859-1'); ?>
<html>
<head></head>
<body>
    <div style="border: 1px solid black;">
        <center>
<?php
session_start();
set_time_limit(0); 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);

include("mapping.php");

require 'src/XBase/Table.php';
require 'src/XBase/Column.php';
require 'src/XBase/Record.php';
require 'src/XBase/Memo.php';
use XBase\Table;
    
$mysqli->set_charset("latin1");
$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE); 
$mysqli->autocommit(FALSE);

if(isset($_POST["submit"])) 
{

foreach($_FILES as $name => $file) {

    if(empty($file['tmp_name'])) continue;

    $tbl = $table[$name];
    echo "<h1>".$name." -> ".$tbl."</h1>";

    $dbf = new Table($file['tmp_name']);
    $mysqli->query("TRUNCATE $tbl");
    
    switch($name) {
    case "dbfClients":
        $inserted = 0;
        while($record = $dbf->nextRecord())
        {
            $stmt = $mysqli->prepare("INSERT INTO $tbl (Id_Client,RaisonSociale,Adresse,Ville,IBAN_1,IBAN_2) VALUES  (?,?,?,?,?,?)");
            $stmt->bind_param("isssss", $Id_Client, $RaisonSociale, $Adresse,$Ville,$IBAN_1,$IBAN_2);
            
            $Id_Client = $record->forceGetString("n_cli");
            $RaisonSociale = $record->forceGetString("nom_cli"); 
            $Adresse = $record->forceGetString("adr"); 
            $Ville = $record->forceGetString("ville"); 
            $IBAN_1 = $record->forceGetString("cpt_bcr1");
            $IBAN_2 = $record->forceGetString("cpt_bcr2");
            
            if(trim($Id_Client) == "0") continue;
            if(!$stmt->execute()) {echo $mysqli->error."<br>"; break;}
            
            $inserted++;
            $stmt->close();
        }
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfFactures":
        $inserted = 0;
        while($record = $dbf->nextRecord())
        {
            $stmt = $mysqli->prepare("INSERT INTO $tbl (Num_Facture,Id_Client,Num_Carnet,Date_Facture,Montant_Facture,TVA_Facture,HT_Facture,Reste_Facture,Note,Unite,Quantite) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("iiisddddssd", $Num_Facture,$Id_Client,$Num_Carnet,$Date_Facture,$Montant_Facture,$TVA_Facture,$HT_Facture,$Reste_Facture,$Note,$Unite,$Quantite);

            $Num_Facture = $record->forceGetString("n_fact");
            $Id_Client = $record->forceGetString("n_cli"); 
                if(empty($Id_Client)) $Id_Client = 1043;
            $Num_Carnet = $record->forceGetString("n_car"); 
            $Date_Facture = $record->forceGetString("d_fact"); 
            $Montant_Facture = $record->forceGetString("m_fact"); 
            $TVA_Facture = $record->forceGetString("tva"); 
            $HT_Facture = $Montant_Facture - $TVA_Facture;
            $Reste_Facture = $record->forceGetString("reste"); 
            $Note = $note_facture[strtoupper($record->forceGetString("note"))]; 
            
            $METRAGE = $record->forceGetString("metrage");
            $QUANTITE = $record->forceGetString("quantite");
            $FILS = $record->forceGetString("fils");
            $DECHET = $record->forceGetString("dechet");
            $TRAV_FACON = $record->forceGetString("trav_facon");
            $MARCHANDIS = $record->forceGetString("marchandis");
            
            if(isset($METRAGE) && is_numeric($METRAGE) && $METRAGE != 0) {
                $Quantite = $METRAGE;  
                $Unite = "Mètres";  
                $Note = empty($Note) ? $note_facture["P"] : $Note;
            } elseif(isset($QUANTITE) && is_numeric($QUANTITE) && $QUANTITE != 0) {
                $Quantite = $QUANTITE;
                $Unite = "Kilogrammes";
                $Note = empty($Note) ? $note_facture["P"] : $Note;
            } elseif(isset($FILS) && is_numeric($FILS) && $FILS != 0) {
                $Quantite = $FILS;
                $Unite = "Mètres";
                $Note = $note_facture["R"];
            } elseif(isset($DECHET) && is_numeric($DECHET) && $DECHET != 0) {
                $Quantite = $DECHET;
                $Unite = "Kilogrammes";
                $Note = $note_facture["R"];
            } elseif(isset($TRAV_FACON) && is_numeric($TRAV_FACON) && $TRAV_FACON != 0) {
                $Quantite = $TRAV_FACON;
                $Unite = "Kilogrammes";
                $Note = $note_facture["T"];
            } elseif(isset($MARCHANDIS) && is_numeric($MARCHANDIS) && $MARCHANDIS != 0) {
                $Quantite = $MARCHANDIS;
                $Unite = "Kilogrammes";
                $Note = $note_facture["M"];
            }

            
            if(!$stmt->execute()) {
                echo $mysqli->error."<br>"; 
                break;
            }

            $inserted++;
            $stmt->close();
        }
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfEffets":
        $inserted = 0;
        while($record = $dbf->nextRecord())
        {
            $stmt = $mysqli->prepare("INSERT INTO $tbl (Num_Effet,Id_Facture,Num_Bordereau,Date_Echeance,Date_Valeur,Date_Paiement,Montant,Endosseur,Tire,Observation,Etat_Effet,Id_Banque,Date_Created) VALUES  (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("iiisssdssssis", $Num_Effet,$Id_Facture,$Num_Bordereau,$Date_Echeance,$Date_Valeur,$Date_Paiement,$Montant,$Endosseur,$Tire,$Observation,$Etat_Effet,$Id_Banque,$Date_Created);
            
            $Num_Effet = $record->forceGetString("n_ef");
            $Id_Facture = getIdFacture($record->forceGetString("n_fact"),$record->forceGetString("d_fact")); 
            $Num_Bordereau = $record->forceGetString("b"); 
            $Date_Echeance = $record->forceGetString("d_ech"); 
            $Date_Valeur = $record->forceGetString("d_val"); 
            $Date_Paiement = $record->forceGetString("d_paiement"); 
            $Montant = $record->forceGetString("m_ef");
            $Endosseur = $record->forceGetString("endo");
            $Tire = $record->forceGetString("tire");
            $Observation = $record->forceGetString("obs");
            $Etat_Effet = $etat_effet[$record->forceGetString("etat")];
            $Id_Banque = $banque[$record->forceGetString("banque")];
            $Date_Created = $record->forceGetString("d_s");

            if(!$stmt->execute()) {echo $mysqli->error."<br>"; break;}
            $inserted++;
            $stmt->close();
        }
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfCheques":
        $inserted = 0;
        while($record = $dbf->nextRecord())
        {
            $stmt = $mysqli->prepare("INSERT INTO $tbl (Num_Cheque,Id_Facture,Num_Bordereau,Montant_Cheque,Endosseur,Tire,Observation,Num_Remise,Date_Remise,Date_Created) VALUES  (?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("siiidsssss", $Num_Cheque,$Id_Facture,$Num_Bordereau,$Montant_Cheque,$Endosseur,$Tire,$Observation,$Num_Remise,$Date_Remise,$Date_Created);
            
            $Num_Cheque = $record->forceGetString("n_chq");
            $Id_Facture = getIdFacture($record->forceGetString("n_fact"),$record->forceGetString("d_fact")); 
            $Num_Bordereau = $record->forceGetString("b"); 
            $Montant_Cheque = $record->forceGetString("m_chq"); 
            $Endosseur = $record->forceGetString("endo"); 
            $Tire = $record->forceGetString("tire");
            $Observation = $record->forceGetString("obs");
            $Id_Banque = $banque[$record->forceGetString("dom")];
            $Num_Remise = $record->forceGetString("n_rem");
            $Date_Remise = $record->forceGetString("date_rem");
            $Date_Created = $record->forceGetString("d_s");

            if(!$stmt->execute()) {echo $mysqli->error."<br>"; break;}
            $inserted++;
            $stmt->close();
        }
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfVirementsVersements":
        $inserted = 0;
        while($record = $dbf->nextRecord())
        {
            $stmt = $mysqli->prepare("INSERT INTO $tbl (Num_Operation,Montant,Id_Facture,Observation,Type_Operation,Date_Operation,Date_Created) VALUES (?,?,?,?,?,?,?)");
            echo $mysqli->error;
            
            $stmt->bind_param("sdissss", $Num_Operation,$Montant,$Id_Facture,$Observation,$Type_Operation,$Date_Operation,$Date_Created);
            $Num_Operation = $record->forceGetString("n"); 
            $Montant = $record->forceGetString("m"); 
            $n_fact = $record->forceGetString("n_fact"); 
            $Id_Facture = getIdFacture($n_fact,$record->forceGetString("d_fact")); 
            $Observation = $record->forceGetString("o");
            $Type_Operation = ($Observation == "VIREMENT")? "Virement" : "Versement";
            $Date_Operation = $record->forceGetString("date");
            $Date_Created = $record->forceGetString("d_s");
            
            if(!$stmt->execute()) {echo $mysqli->error."<br>"; break;}
            $inserted++;
            $stmt->close();
        }        
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfFournisseurs":
        $inserted = 0;
        while($record = $dbf->nextRecord())
        {
            $stmt = $mysqli->prepare("INSERT INTO $tbl (Id_Fournisseur,RaisonSociale,Adresse,Ville,IBAN_1,IBAN_2) VALUES  (?,?,?,?,?,?)");
            $stmt->bind_param("isssss", $Id_Fournisseur, $RaisonSociale, $Adresse,$Ville,$IBAN_1,$IBAN_2);
            
            $Id_Fournisseur = $record->forceGetString("n_f");
            $RaisonSociale = $record->forceGetString("nom_f"); 
            $Adresse = $record->forceGetString("adr"); 
            $Ville = $record->forceGetString("ville"); 
            $IBAN_1 = $record->forceGetString("cb1");
            $IBAN_2 = $record->forceGetString("cb2");
            
            if(!$stmt->execute()) {echo $mysqli->error."<br>"; break;}
            
            $inserted++;
            $stmt->close();
        }
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfDUM":
        $inserted = 0;
        $mysqli->query("TRUNCATE import_engagements_importation;");
        $mysqli->query("TRUNCATE engagements_importation;");

        while($record = $dbf->nextRecord())
        {
            $Id_Engagement = $record->forceGetString("sereng"); 
                if(!$Id_Engagement) continue;

            $Num_Engagement = $record->forceGetString("n_e"); 
            $Date = $record->forceGetString("d_e"); 
            $Id_Engagement = getIdEngagement($Num_Engagement, $Date);

            if(!$Id_Engagement) {
                $stmt = $mysqli->prepare("INSERT INTO engagements_importation (Id_Engagement,Num_Engagement,Date,Montant,Id_Banque) VALUES (?,?,?,?,?)");            
                $stmt->bind_param("isdii",$Id_Engagement,$Num_Engagement,$Date,$Montant,$Id_Banque);

                $Montant = $record->forceGetString("m_e"); 
                $Id_Banque = $banque[$record->forceGetString("bque")];

                if(!$stmt->execute()) {
                    echo $mysqli->error."<br>"; 
                    break;
                } 
            }

            $stmt = $mysqli->prepare("INSERT INTO $tbl (Id_Achat,Num_DUM,Date_DUM,Num_Facture,Date_Facture,TVA,Droits_Douane_HT,Montant_MAD,Marchandises,Id_Nature,Id_Code,Poids_Net,Poids_Brut,Id_Fournisseur,Origine,Devise,Montant_Devise,Taux_Change,Num_Quittance,Date_Quittance,Total_Quittance,Date_Created) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("isdsdiiisiiiiissiisdid",$Id_Achat,$Num_DUM,$Date_DUM,$Num_Facture,$Date_Facture,$TVA,$Droits_Douane_HT,$Montant_MAD,$Marchandises,$Id_Nature,$Id_Code,$Poids_Net,$Poids_Brut,$Id_Fournisseur,$Origine,$Devise,$Montant_Devise,$Taux_Change,$Num_Quittance,$Date_Quittance,$Total_Quittance,$Date_Created);

            $Id_Achat = $record->forceGetString("serdm");
            $Num_DUM = $record->forceGetString("n_dm"); 
            $Date_DUM = $record->forceGetString("d_dm"); 
            $Num_Facture = $record->forceGetString("n_fd"); 
            $Num_Facture = empty($Num_Facture) ? $record->forceGetString("n_fp") : $Num_Facture;
            $Date_Facture = $record->forceGetString("d_fd");
            $Date_Facture = empty($Date_Facture) ? $record->forceGetString("d_fp") : $Date_Facture;
            $TVA = $record->forceGetString("tva");
            $Droits_Douane_HT = $record->forceGetString("d_d")+$record->forceGetString("d_ds");
            $Montant_MAD = $record->forceGetString("mdh");
            $Marchandises = $record->forceGetString("tmse");
            $Id_Nature = $nature[$record->forceGetString("natmse")];
            $Id_Code = $record->forceGetString("cod_ms");
            $Poids_Net = $record->forceGetString("pn");
            $Poids_Brut = $record->forceGetString("pb");
            $Id_Fournisseur = getIdFournisseur($record->forceGetString("f"));
            $Origine = $record->forceGetString("orig");
            $Devise = $record->forceGetString("dev");
            $Montant_Devise = $record->forceGetString("md");
            $Taux_Change = $record->forceGetString("ch");
            $Num_Quittance = $record->forceGetString("n_quit");
            $Date_Quittance = $record->forceGetString("d_quit");
            $Total_Quittance = $record->forceGetString("net");
            $Date_Created = $record->forceGetString("d_s");

            if(!$stmt->execute()) {
                echo $mysqli->error."<br>"; 
                break;
            }

            $mysqli->query("INSERT INTO import_engagements_importation VALUES($Id_Achat, $Id_Engagement)");

            $inserted++;
            $stmt->close();
        }
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfCredocs": // LETTRE.DBF
        $inserted = 0;
        $mysqli->query("TRUNCATE effets_fournisseurs;");

        while($record = $dbf->nextRecord())
        {
            $type = $record->forceGetString("type");
            $motif = $record->forceGetString("motif");

            $Id_Fournisseur = getIdFournisseur($record->forceGetString("fourni"));
            $Montant_MAD = $record->forceGetString("m_ef");
            $Id_Banque = $banque[$record->forceGetString("banque")];
            $Date_Echeance = $record->forceGetString("d_ech"); 
            $Date_Created = $record->forceGetString("d_s");

            // Has DUM Information
            if(strpos($motif, "DUM") === 0 && preg_match('/DUM\s?(N°\s?)?(\d+\s?\w)\s*DU\s*([0-3][0-9]\/[0-1][0-9]\/([1-2][09])?[09][0-9])/i',$motif,$matches)) {
                $Num_DUM = $matches[2];
                $Date_DUM = $matches[3];
                $Id_Achat = getIdAchatImport($Num_DUM, $Date_DUM); 
            } elseif(strpos($motif, "FACT") === 0 && preg_match('/FACT\s?(N°\s?)?(\d+)\s*DU\s*([0-3][0-9]\/[0-1][0-9]\/([1-2][09])?[09][0-9])/i',$motif,$matches)) {
                $Num_Facture = $matches[2];
                $Date_Facture = $matches[3];
                $Id_Achat = getIdAchatLocal($Num_Facture, $Date_Facture); 
            } 
            
            // Credoc
            if($type == "C") {
                if($Id_Achat != -1) {
                    $stmt = $mysqli->prepare("INSERT INTO credocs_fournisseurs (Id_Achat,Num_Credoc,Id_Fournisseur,Montant_MAD,Id_Banque,Date_Echeance,Montant_Devise,Devise,Taux_Change,Date_Created) VALUES  (?,?,?,?,?,?,?,?,?,?)");
                    $stmt->bind_param("isiiidisid",$Id_Achat,$Num_Credoc,$Id_Fournisseur,$Montant_MAD,$Id_Banque,$Date_Echeance,$Montant_Devise,$Devise,$Taux_Change,$Date_Created);
                } else {
                    $stmt = $mysqli->prepare("INSERT INTO credocs_fournisseurs (Num_Credoc,Id_Fournisseur,Montant_MAD,Id_Banque,Date_Echeance,Montant_Devise,Devise,Taux_Change,Date_Created) VALUES  (?,?,?,?,?,?,?,?,?)");
                    $stmt->bind_param("siiidisid", $Num_Credoc,$Id_Fournisseur,$Montant_MAD,$Id_Banque,$Date_Echeance,$Montant_Devise,$Devise,$Taux_Change,$Date_Created);
                }

                $Num_Credoc = $record->forceGetString("n_ef");
                $Montant_Devise = $record->forceGetString("mt_dev");
                $Devise = $record->forceGetString("dev");
                $Taux_Change = $record->forceGetString("chge");
            
            // Effet Fournisseur 
            } else {
                if($Id_Achat != -1) {
                    $stmt = $mysqli->prepare("INSERT INTO effets_fournisseurs (Id_Achat,Num_Effet,Id_Fournisseur,Montant_MAD,Id_Banque,Date_Echeance,Date_Created) VALUES  (?,?,?,?,?,?,?)");
                    $stmt->bind_param("isiiidd",$Id_Achat,$Num_Effet,$Id_Fournisseur,$Montant_MAD,$Id_Banque,$Date_Echeance,$Date_Created);     
                } else {
                    $stmt = $mysqli->prepare("INSERT INTO effets_fournisseurs (Num_Effet,Id_Fournisseur,Montant_MAD,Id_Banque,Date_Echeance,Date_Created) VALUES  (?,?,?,?,?,?)");
                    $stmt->bind_param("siiidd",$Num_Effet,$Id_Fournisseur,$Montant_MAD,$Id_Banque,$Date_Echeance,$Date_Created);    
                }
                
                $Num_Effet = $record->forceGetString("n_ef");
            }

            if(!$stmt->execute()) {echo $mysqli->error."<br>"; break;}
    
            $inserted++;
            $stmt->close();
        }
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfAchats":
        $inserted = 0;
        while($record = $dbf->nextRecord())
        {
             $stmt = $mysqli->prepare("INSERT INTO $tbl (Id_Fournisseur,Num_Facture,Date_Facture,Marchandises,Poids,Montant_HT,TVA,Montant_TTC,Id_Nature,Id_Code,Date_Created) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("isdsiiiiiid",$Id_Fournisseur,$Num_Facture,$Date_Facture,$Marchandises,$Poids,$Montant_HT,$TVA,$Montant_TTC,$Id_Nature,$Id_Code,$Date_Created);

            $Id_Fournisseur = $record->forceGetString("n_f");
            $Id_Fournisseur = empty($Id_Fournisseur) ? 
                getIdFournisseur($record->forceGetString("four")) : $Id_Fournisseur;
            $Num_Facture = $record->forceGetString("n_fact"); 
            $Date_Facture = $record->forceGetString("d_fact");
            $Marchandises = $record->forceGetString("mse");
            $Poids = $record->forceGetString("poids");
            $Montant_HT = $record->forceGetString("m_ht");
            $TVA = $record->forceGetString("tva");
            $Montant_TTC = $record->forceGetString("ttc");
            $Id_Nature = $nature[$record->forceGetString("natmse")];
            $Id_Code = $record->forceGetString("cod_ms");
            $Date_Created = $record->forceGetString("date_s");
            
            if(!$stmt->execute()) {
                echo $mysqli->error."<br>"; 
                break;
            }

            $inserted++;
            $stmt->close();
        }
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfAvoirs":
        $inserted = 0;
        while($record = $dbf->nextRecord())
        {
            $stmt = $mysqli->prepare("INSERT INTO $tbl (Num_Avoir,Date_Avoir,Total_Avoir,Observation,Date_Created) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sdisd", $Num_Avoir,$Date_Avoir,$Total_Avoir,$Observation,$Date_Created);
            
            $Num_Avoir = $record->forceGetString("n_av"); 
            $Date_Avoir = $record->forceGetString("d_av"); 
            $Total_Avoir = $record->forceGetString("t_av"); 
            $Observation = $record->forceGetString("obs");  
            $Date_Created = $record->forceGetString("d_s");
            
            if(!$stmt->execute()) {echo $mysqli->error."<br>"; break;}
            $inserted++;
            $stmt->close();
        }        
        echo $inserted."/".$dbf->recordCount;
    break;
    case "dbfSalaires":
        $inserted = 0;
        while($record = $dbf->nextRecord())
        {
            $stmt = $mysqli->prepare("INSERT INTO $tbl (Mois,Annee,Brut_Mensuel,Nombre_Salaries) VALUES (?,?,?,?)");
            $stmt->bind_param("iidi", $Mois,$Annee,$Brut_Mensuel,$Nombre_Salaries);
            
            $Mois = $record->forceGetString("mois"); 
            $Annee = $record->forceGetString("annees"); 
            $Brut_Mensuel = $record->forceGetString("brut_cit"); 
            $Nombre_Salaries = $record->forceGetString("nbr_cit");  
            
            if(!$stmt->execute()) {echo $mysqli->error."<br>"; break;}
            $inserted++;
            $stmt->close();
        }        
        echo $inserted."/".$dbf->recordCount;
    break;
    }    
flush();
ob_flush();
}

echo $mysqli->commit() ? "<h1>OK</h1>" : "<h1>FAIL</h1>"; 

$mysqli->close();    

} else {
?>
    <form action="silkroad.php" method="POST" enctype="multipart/form-data">
        <br/>
        CLIENT.DBF: <input type="file" name="dbfClients" id="dbfClients"><br/><br/>
        FACTURE.DBF: <input type="file" name="dbfFactures" id="dbfFactures"><br/><br/>
        EFFET.DBF: <input type="file" name="dbfEffets" id="dbfEffets"><br/><br/>
        CHEQUE.DBF: <input type="file" name="dbfCheques" id="dbfCheques"><br/><br/>
        ESPECE.DBF: <input type="file" name="dbfVirementsVersements" id="dbfVirementsVersements"><br/><br/>
        REG_MIXT.DBF: <input type="file" name="dbfRegMixte" id="dbfRegMixte"><br/><br/>
        <hr/>
        FOUR.DBF: <input type="file" name="dbfFournisseurs" id="dbfFournisseurs"><br/><br/>
        DM.DBF: <input type="file" name="dbfDUM" id="dbfDUM"><br/><br/>
        LETTRE.DBF: <input type="file" name="dbfCredocs" id="dbfCredocs"><br/><br/>
        ACHAT.DBF: <input type="file" name="dbfAchats" id="dbfAchats"><br/><br/>
        AVOIR.DBF: <input type="file" name="dbfAvoirs" id="dbfAvoirs"><br/><br/>
        <hr/>
        SALAIRE.DBF: <input type="file" name="dbfSalaires" id="dbfSalaires"><br/><br/>
        <br/><br/>
        <input type="submit" value="Upload" name="submit" style="width: 200px;">
    </form>
<?php 
}
?>
</center>
</div>
</body>
</html>