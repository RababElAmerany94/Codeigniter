<?php
$sum_facture = 0;
$sum_reste = 0;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
        <link rel="stylesheet" href="assets/css/print.css"/>
    </head>
    <body>
        <header>
            <div class="row">
                <div class="row_left"><?= $company_name ?></div>

                <div class="row_right"><?= date('d/m/Y')?></div>
            </div>

            <div class="row">
                <h2 class="title"><?= $title1 ?></h2>
            </div>
            <br><br>
        </header>

        <table border="1">
            <tr>
                <th>Mois</th>
                <th>Effets en Portefeulille</th>
                <th>Effets à Payer</th>
                <th>Diffrérence</th>
            </tr>
            <?php foreach ($table1 as $mois => $row) { ?>
            <tr>
	            <?php $style = ($mois == 'total') ? ' style="font-weight: bold;"' : ''; ?>
                <td<?= $style ?>><?= str_replace($year, '', $mois) ?></td>
	            <td<?= $style ?> class="number"><?= number_format($row['Portefeuille'],2,',',' ') ?></td>
	            <td<?= $style ?> class="number"><?= number_format($row['Payer'],2,',',' ') ?></td>
	            <td<?= $style ?> class="number"><?= number_format($row['Difference'],2,',',' ') ?></td>
            </tr>
            <?php } ?>
        </table>

        <div class="row" style="margin: 10px;">
            <h2 style="text-align:center;"><?= $title2 ?></h2>
        </div>
        <br><br>

        <table border="1">
            <tr>
                <th>Num Facture</th>
                <th>Date</th>
                <th>Client</th>
                <th>Montant</th>
                <th>Reste</th>
            </tr>
            <?php foreach ($table2 as $row) {
                $sum_facture += $row['Montant_Facture'];
                $sum_reste += $row['Reste_Facture'];
                ?>
            <tr>
                <td><?= $row['Num_Facture'] ?></td>
                <td><?= date_format(date_create($row['Date_Facture']),"d/m/Y") ?></td>
                <td><?= $row['RaisonSociale'] ?></td>
                <td class="number"><?= number_format($row['Montant_Facture'],2,',',' ') ?></td>
                <td class="number"><?= number_format($row['Reste_Facture'],2,',',' ') ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td></td>
                <td></td>
                <td>Total</td>
                <td class="number"><?= number_format($sum_facture,2,',',' ') ?></td>
                <td class="number"><?= number_format($sum_reste,2,',',' ') ?></td>
            </tr>
        </table>

        <table border="1">
            <tr>
                <th>Effets en Portefeulille</th>
                <th>Effets à Payer</th>
                <th>Factures Non-Réglées</th>
                <th>Diffrérence</th>
            </tr>
            <tr>
                <td class="number"><?= number_format($table1['total']['Portefeuille'],2,',',' ') ?></td>
                <td class="number"><?= number_format($table1['total']['Payer'],2,',',' ') ?></td>
                <td class="number"><?= number_format($sum_facture,2,',',' ') ?></td>
                <td class="number"><?= number_format($table1['total']['Difference'] + $sum_facture,2,',',' ') ?></td>
            </tr>
        </table>

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
