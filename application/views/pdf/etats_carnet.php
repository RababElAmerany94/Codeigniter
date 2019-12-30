<?php
$ca_ht_sum = 0;
$metrage_sum = 0;
$tissus_sum = 0;
$fils_sum = 0;
$dechets_sum = 0;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Etats des Carnets par Année</title>
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

            <h2 class="title">Etats des Carnets pour <?= $year ?></h2>

            <br><br><br>

        </header>
        <table border="1">
            <tr>
                <th>CARNET N</th>
                <th>C.A. H.T.</th>
                <th>METRAGE</th>
                <th>TISSUS</th>
                <th>FILS</th>
                <th>DECHETS</th>
            </tr>
            <?php for ($i = 0; $i < count($result); $i++) { ?>
                <tr>
                    <td class="number"><?= $result[$i]['Num_Carnet'] ?></td>
                    <td class="number"><?= number_format($result[$i]['C.A. H.T.'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['Metrage'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['Tissus'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['Fils'], 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($result[$i]['Dechets'], 2, ",", " ") ?></td>
                </tr>
                <?php
                $ca_ht_sum += $result[$i]['C.A. H.T.'];
                $metrage_sum += $result[$i]['Metrage'];
                $tissus_sum += $result[$i]['Tissus'];
                $fils_sum += $result[$i]['Fils'];
                $dechets_sum += $result[$i]['Dechets'];
            }
            ?>
            <tr>
                <th>TOTAL</th>
                <th class="number"><?= number_format($ca_ht_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($metrage_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($tissus_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($fils_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($dechets_sum, 2, ",", " ") ?></th>
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
