<?php
$total_factures = 0;
$montant_effet = 0;
$montant_cheques = 0;
$montant_virements = 0;

$total_general = 0;
$order = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Etats des Règlements</title>
    <link rel="stylesheet" href="assets/css/paper.min.css"/>
    <link rel="stylesheet" href="assets/css/print.css"/>
    <style>
        .table-title {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .cheque_title {
            font-size: 10px;
            padding: 0;
        }

        .total {
            font-size: 14px;
        }

        .report-header {
            font-weight: normal;
        }

        .report-header span {
            font-weight: bolder;
        }
    </style>
</head>

<body>
<header>
    <div class="row">
        <div class="row_left">
            <?= $company_name ?>
        </div>
        <div class="row_right">
            <?= date('d/m/Y') ?>
        </div>
    </div>
    <br>
    <br>
    <h2 class="report-header" style="text-align:center">Règlement Factures Mixte</h2>
    <br><br>
    <?php if (!empty($client)) { ?>
        <div><label class='d-bold'>Nom Client: </label> <?= $client['RaisonSociale'] ?></div>
        <div><label class='d-bold'>Num Client: </label> <?= $client['Id_Client'] ?></div>
        <!--        <div><label class='d-bold'>Total Facture: </label> <? //= number_format($total_facture, 2, ",", " ") ?> Dhs</div>-->
    <?php } ?>
</header>

<h3 class="table-title">Factures</h3>
<table style="page-break-inside: avoid;">
    <tr>
        <th>NUM FACTURE</th>
        <th>DATE</th>
        <th>MONTANT</th>
    </tr>
    <?php for ($i = 0; $i < count($factures); $i++) { ?>
        <tr>
            <td><?= $factures[$i]['Num_Facture'] ?></td>
            <td><?= (new DateTime($factures[$i]['Date_Created']))->format('d/m/Y') ?></td>
            <td style="width: 80px"><?= number_format($factures[$i]['Montant_Facture'], 2, ',', ' ') ?></td>
        </tr>
        <?php
        $total_factures += $factures[$i]['Montant_Facture'];
    } ?>

    <tr style="padding:20px 0;">
        <th colspan="2">TOTAL</th>
        <th style="width: 80px"><?= number_format($total_factures, 2, ',', ' ') ?></th>
    </tr>

        <tr>
            <th colspan="3" style="text-align:left">NOMBRE DES FACTURES: <?= count($factures) ?></th>
        </tr>
</table>

<?php
if (0 < count($effets)) {
    $order++;
    ?>
    <h3 class="table-title"><?= $order ?>. Effets</h3>
    <table style="page-break-inside: avoid;">
        <tr>
            <th>NUM EFFET</th>
            <th>ECHEANCE</th>
            <th style="width: 80px">MONTANT</th>
            <th>TIRE</th>
            <th>ENDOSSEUR</th>
            <th>DOMICILIATION</th>
        </tr>
        <?php
        for ($j = 0; $j < count($effets); $j++) {
            $effet = $effets[$j];
            ?>
            <tr>
                <td><?= $effet['Num_Effet'] ?></td>
                <td><?= (new DateTime($effet['ECHEANCE']))->format('d/m/Y') ?></td>
                <td class="number" style="width: 80px"><?= number_format($effet['MONTANT'], 2, ",", " ") ?></td>
                <td><?= $effet['TIRE'] ?></td>
                <td><?= $effet['ENDOSSEUR'] ?></td>
                <td><?= $effet['DOMICILIATION'] ?></td>
            </tr>
            <?php
            $montant_effet += $effet['MONTANT'];
            $total_general += $effet['MONTANT'];
        }
        ?>
        <tr style="padding:20px 0;">
            <th colspan="2">TOTAL 1</th>
            <th class="number" style="width: 80px"><?= number_format($montant_effet, 2, ",", " ") ?></th>
            <th colspan="3"></th>
        </tr>
        <tr>
            <th colspan="6" style="text-align:left">NOMBRE DES EFFETS: <?= count($effets) ?></th>
        </tr>
    </table>
    <?php
}
if (0 < count($cheques)) {
    $order++;
    ?>
    <h3 class="table-title"><?= $order ?>. Chèques</h3>
    <table style="page-break-inside: avoid;">
        <tr class="cheque_title">
            <th>NUM CHEQUE</th>
            <th style="width: 80px">MONTANT</th>
            <th>TIRE</th>
            <th>ENDOSSEUR</th>
            <th>DOMICILIATION</th>
            <th>VILLE</th>
            <th>NUM REMISE</th>
            <th>DATE REMISE</th>
        </tr>
        <?php
        for ($j = 0; $j < count($cheques); $j++) {
            $cheque = $cheques[$j];
            ?>
            <tr>
                <td><?= $cheque['NUM CHEQUE'] ?></td>
                <td class="number" style="width: 80px"><?= number_format($cheque['MONTANT'], 2, ",", " ") ?></td>
                <td><?= $cheque['TIRE'] ?></td>
                <td><?= $cheque['ENDOSSEUR'] ?></td>
                <td><?= $cheque['DOMICILIATION'] ?></td>
                <td><?= $cheque['VILLE'] ?></td>
                <td><?= $cheque['NUM REMISE'] ?></td>
                <td><?= (new DateTime($cheque['DATE REMISE']))->format('d/m/Y') ?></td>
            </tr>
            <?php
            $montant_cheques += $cheque['MONTANT'];
            $total_general += $cheque['MONTANT'];
        }
        ?>
        <tr>
            <th colspan="1">TOTAL 2</th>
            <th class="number" style="width: 80px"><?= number_format($montant_cheques, 2, ",", " ") ?></th>
            <th colspan="6"></th>
        </tr>
        <tr>
            <th colspan="8" style="text-align:left">NOMBRE DES CHEQUES: <?= count($cheques) ?></th>
        </tr>
    </table>
    <?php
}
if (0 < count($virements)) {
    $order++;
    ?>
    <h3 class="table-title"><?= $order ?>. Espèces / Virement</h3>
    <table style="page-break-inside: avoid;">
        <tr>
            <th>NUM VERSEMENT / VIREMENT</th>
            <th style="width: 120px">MONTANT</th>
            <th>DATE VERSEMENT / VIREMENT</th>
        </tr>
        <?php
        for ($i = 0; $i < count($virements); $i++) {
            $virement = $virements[$j];
            ?>
            <tr>
                <td><?= $virement['NUM VERSEMENT'] ?></td>
                <td style="width: 120px"><?= number_format($virement['MONTANT'], 2, ",", " ") ?></td>
                <td><?= (new DateTime($virement['DATE VERSEMENT']))->format('d/m/Y') ?></td>
            </tr>
            <?php
            $montant_virements += $virement['MONTANT'];
            $total_general += $virement['MONTANT'];
        }
        ?>
        <tr style="padding:20px 0;">
            <th colspan="1">TOTAL 3</th>
            <th class="number" style="width: 80px"><?= number_format($montant_virements, 2, ",", " ") ?></th>
            <th colspan="1"></th>
        </tr>
        <tr>
            <th colspan="3" style="text-align:left">NOMBRE D'OPERATIONS: <?= count($virements) ?></th>
        </tr>
    </table>
    <?php
}
?>

<div class="total"><span>TOTAL GENERAL =</span> <?= number_format($total_general, 2, ',', ' ') ?> Dhs</div>
<br>
<br>
<div class="total"><span>TOTAL FACTURES =</span> <?= number_format($total_factures, 2, ',', ' ') ?> Dhs</div>
<div class="total"><span>REGLEMENT =</span> <?= number_format($total_general, 2, ',', ' ') ?> Dhs</div>
<div class="total"><span>DIFFERENCE  =</span> <?= number_format($total_factures - $total_general, 2, ',', ' ') ?> DHs</div>

<script type="text/php">
    if (isset($pdf)) {
        $x = $pdf->get_width() - 85;
        $y = $pdf->get_height()-35;
        $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>
</body>
</html>
