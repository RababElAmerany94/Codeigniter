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
                <h2 class="title"><?= $title ?></h2>
            </div>

            <br><br>
        </header>
        <table border="1">
            <tr>
                <th rowspan="2">MOIS</th>
                <th colspan="6">VENTES / KG</th>
            </tr>
            <tr>
                <th>TISSUS</th>
                <th>FILS</th>
                <th>DECHETS</th>
                <th>MARCHANDISES</th>
                <th>FACONNAGE</th>
                <th>TOTAL</th>
            </tr>
            <?php foreach ($table1 as $mois => $row) {
                $totals['ventes'][$mois] += $row['MT_Ventes'] + $row['MF_Ventes'] + $row['R_Ventes'] + $row['F_Ventes'] + $row['T_Ventes'];
                ?>
            <tr>
                <td><?= $mois ?></td>
                <td class="number"><?= number_format($row['MT_Ventes'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['MF_Ventes'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['R_Ventes'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['F_Ventes'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['T_Ventes'],2,","," ") ?></td>
                <td class="number"><?= number_format($totals['ventes'][$mois],2,","," ") ?></td>
            </tr>
            <?php } ?>
        </table>

        <table border="1">
            <tr>
                <th rowspan="2">MOIS</th>
                <th colspan="6">ACHATS</th>
            </tr>
            <tr>
                <th>MATIERE<br>PREMIERE / KG</th>
                <th>MARCHANDISES /<br>KG</th>
                <th>PRODUIT<br>FINNISSAGE / KG</th>
                <th>EMBALLAGES<br> / KG</th>
                <th>ELECTRICITE /<br>KWH</th>
                <th>EAUX / M<sub>3</sub></th>
            </tr>
            <?php foreach ($table2 as $mois => $row) {
                $totals['achats'][$mois] += $row['MP_Achats'] + $row['MS_Achats'] + $row['PF_Achats'] + $row['EM_Achats'] + $row['EL_Achats'] + $row['EA_Achats'];
                ?>
            <tr>
                <td><?= $mois ?></td>
                <td class="number"><?= number_format($row['MP_Achats'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['MS_Achats'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['PF_Achats'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['EM_Achats'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['EL_Achats'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['EA_Achats'],2,","," ") ?></td>
            </tr>
            <?php } ?>
        </table>

        <table border="1" style="page-break-before:always;">
            <tr>
                <th rowspan="2">MOIS</th>
                <th colspan="2">ACHATS EN DH H.T.</th>
            </tr>
            <tr>
                <th>FUEL / KG</th>
                <th>GAZ / KG</th>
            </tr>
            <?php foreach ($table3 as $mois => $row) {
                $totals['achats'][$mois] += $row['FU_Achats'] + $row['BU_Achats'];
                ?>
            <tr>
                <td><?= $mois ?></td>
                <td class="number"><?= number_format($row['FU_Achats'],2,","," ") ?></td>
                <td class="number"><?= number_format($row['BU_Achats'],2,","," ") ?></td>
            </tr>
            <?php } ?>
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
