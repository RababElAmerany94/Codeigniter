<?php
$ht_sum = 0;
$tva_sum = 0;
$ttc_sum = 0;
$nbr_fact = 0;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Journal Mensuel des Ventes par Carnet</title>
        <?php if (isset($list_facture)) { ?>
        <style>@page{size:A4;}body{color:#000;background-color:#fff;font-family:Arial,sans-serif;font-size:12px;}h1{font-size:2.4em;line-height:1.4em;font-weight:normal;}table{table-layout:fixed;width:100%;border-collapse:collapse;border-spacing:2px;margin-bottom:20px;}th,td{text-align:center;border:solid 1px;}th{padding:5px;white-space:nowrap;font-weight:bold;}td{padding:5px;text-align:center;}footer{width:100%;padding:8px 0;text-align:center;}.row{position:relative;width:100%;}.d-bold{font-weight:bolder;}</style>
        <?php } else { ?>
        <link rel="stylesheet" href="assets/css/print.css"/>
        <?php } ?>
    </head>
    <body>
        <header>
            <div class="row">
                <?php if (isset($list_facture)) { ?>
                <div style="float:left;">
                <?php } else { ?>
                <div class="row_left">
                <?php } ?>
                    <?= $company_name ?>
                </div>
                <?php if (isset($list_facture)) { ?>
                <div style="float:right;">
                <?php } else { ?>
                <div class="row_right">
                <?php } ?>
                    <?= date('d/m/Y') ?>
                </div>
            </div>
            <br><br>
            <?php if (!isset($list_facture)) { ?>
            <h2 class="title">Journal Mensuel des Ventes pour le Carnet Num <?= $carnet . '/' . $year ?></h2>
            <?php } else {
                if (isset($type)) echo "<div class='d-bold'>Factures Non-Réglées</div>";
                if (isset($client)) echo " <div><label class='d-bold'>Client: </label> $client_name</div>";
                if (isset($year)) echo " <div><label class='d-bold'>Annee:</label> $year </div>";
            } ?>
            <br><br><br>
        </header>
        <table>
            <tr>
                <th style="width: 85px">FACTURE N</th>
                <th style="width: 80px">DATE</th>
                <?php if (isset($list_facture) && !isset($client) || !isset($list_facture)) { ?>
                <th style="width: 200px">CLIENT</th>
                <?php } ?>
                <th>H.T.</th>
                <th>TVA</th>
                <th>TTC</th>
                <th>QUANTITE</th>
            </tr>
            <?php for ($i = 0; $i < count($result); $i++) { ?>
                <tr>
                    <td><?= $result[$i]['Facture N'] ?></td>
                    <td><?= $result[$i]['Date Facture'] ?></td>
                    <?php if (isset($list_facture) && !isset($client) || !isset($list_facture)) { ?>
                    <td><?= $result[$i]['Client'] . ' <br> Code Client: ' . $result[$i]['Code Client'] ?></td>
                    <?php } ?>
                    <td class="number"><?= number_format($result[$i]['H.T.'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['TVA'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['TTC'], 2, ",", " ") ?></td>
                    <?php if ($result[$i]['Unite'] == "Mètres") { ?>
                    <td class="number"><?= number_format($result[$i]['Quantite'] * $Taux, 2, ",", " ") ?></td>
                    <?php } else { ?>
                    <td class="number"><?= number_format($result[$i]['Quantite'], 2, ",", " ") ?></td>
                    <?php } ?>
                </tr>
                <?php
                $ht_sum += $result[$i]['H.T.'];
                $tva_sum += $result[$i]['TVA'];
                $ttc_sum += $result[$i]['TTC'];
                $nbr_fact++;
            }
            ?>
            <tr>
                <?php if (isset($list_facture) && !isset($client) || !isset($list_facture)) { ?>
                <th></th>
                <?php } ?>
                <th></th>
                <th style="text-align:center">TOTAL</th>
                <th></th>
                <th class="number"><?= number_format($ht_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($tva_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($ttc_sum, 2, ",", " ") ?></th>
            </tr>
            <tr>
                <?php if (isset($list_facture) && !isset($client) || !isset($list_facture)) { ?>
                <?php if (!isset($list_facture)) { ?>
                <th colspan='7' style='text-align:left'> NOMBRE DE FACTURES : <?= $nbr_fact ?> </th>
                <?php } ?>
                <?php } else { ?>
<!--                <th colspan='7' style='text-align:left'> NOMBRE DE FACTURES : --><?//= $nbr_fact ?><!-- </th>-->
                <?php } ?>
            </tr>
        </table>
        <?php if (isset($list_facture)) { ?>
        <div style="font-size:18px">NOMBRE DE FACTURES: <strong><?= $nbr_fact ?></strong></div>
        <?php } ?>
        <?php if (!isset($list_facture)) { ?>
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
        <?php } ?>
    </body>
</html>
