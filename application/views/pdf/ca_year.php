<?php
$ca_ht_sum = 0;
$tva_sum = 0;
$ca_ttc_sum = 0;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Chiffre d'Affaires Annuel</title>
        <link rel="stylesheet" href="assets/css/paper.min.css"/>
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

            <br><br>

            <h2 class="title">Chiffre d'Affaires Annuel</h2>

            <br><br><br>
        </header>

        <table border="1">
            <tr>
                <th>MOIS</th>
                <th>C.A. H.T.</th>
                <th>TVA</th>
                <th>C.A. T.T.C</th>
            </tr>
            <?php for ($i = 0; $i < count($result); $i++) { ?>
                <tr>
                    <td><?= $result[$i]['Mois'] ?></td>
                    <td class="number"><?= number_format($result[$i]['C.A. H.T.'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['TVA'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['C.A. TTC'], 2, ",", " ") ?></td>
                </tr>
                <?php
                $ca_ht_sum += $result[$i]['C.A. H.T.'];
                $tva_sum += $result[$i]['TVA'];
                $ca_ttc_sum += $result[$i]['C.A. TTC'];
            }
            ?>
            <tr>
                <th>TOTAL</th>
                <th class="number"><?= number_format($ca_ht_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($tva_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($ca_ttc_sum, 2, ",", " ") ?></th>
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
