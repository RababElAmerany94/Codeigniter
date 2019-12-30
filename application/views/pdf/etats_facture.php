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
        <title>Journal Mensuel des Ventes pour le Mois <?= $month ?> / <?= $year ?></title>
        <link rel="stylesheet" href="assets/css/print.css"/>
    </head>
    <body>
        <header>
            <div class="row ">
                <div class="row_left">
                    <?= $company_name ?>
                </div>
                <div class="row_right">
                    <?= date('d/m/Y') ?>
                </div>
            </div>
            <br>
            <br>
            <h2 class="title">Journal Mensuel des Ventes pour le Mois <?= $month ?> / <?= $year ?></h2>

            <br><br><br>
        </header>
        <table>
            <tr>
                <th>FACTURE N</th>
                <th>DATE</th>
                <th>CODE</th>
                <th style="width: 140px">CLIENT</th>
                <th style="width: 100px">H.T.</th>
                <th>TVA</th>
                <th>TTC</th>
            </tr>
            <?php for ($i = 0; $i < count($result); $i++) { ?>
                <tr>
                    <td><?= $result[$i]['Facture N'] ?></td>
                    <td><?= $result[$i]['Date Facture'] ?></td>
                    <td><?= $result[$i]['Code Client'] ?></td>
                    <td style="width: 140px"><?= $result[$i]['Client'] ?></td>
                    <td class="number" style="width: 140px"><?= number_format($result[$i]['H.T.'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['TVA'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['TTC'], 2, ",", " ") ?></td>
                </tr>
                <?php
                $ht_sum += $result[$i]['H.T.'];
                $tva_sum += $result[$i]['TVA'];
                $ttc_sum += $result[$i]['TTC'];
                $nbr_fact++;
            } ?>
            <tr>
                <th colspan="4" style="text-align:right">TOTAL</th>
                <th class="number"><?= number_format($ht_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($tva_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($ttc_sum, 2, ",", " ") ?></th>
            </tr>
            <tr>
                <th colspan='7' style='text-align:left'> NOMBRE DE FACTURES : <?= $nbr_fact ?> </th>
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
