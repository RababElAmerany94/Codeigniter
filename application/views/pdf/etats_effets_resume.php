<?php
$somme = 0;
$totals = array();
$total = 0;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
        <link rel="stylesheet" href="assets/css/paper.min.css"/>
        <link rel="stylesheet" href="assets/css/print.css"/>
    </head>
    <body>
        <header>
            <div class="row">
                <div class="row_left">
                    <?= $company_name ?>
                </div>
                <div class="row_right">
                    <?= date('d/m/Y')?>
                </div>
            </div>
            <br>
            <h2 class="title"><?= $title ?></h2>
            <br>
            <div style="font-size:16px;vertical-align:bottom;"><strong>Date Début: </strong><?= $date_debut ?></div>
            <div style="font-size:16px;vertical-align:bottom;"><strong>Date Fin: </strong><?= $date_fin ?></div>
            <br><br>
        </header>

        <table border="1">
            <tr>
                <th>NOM CLIENT</th>
                <?php foreach ($months as $month) {
                    $totals[$month] = 0;
                    ?>
                    <th>MOIS <?= $month ?></th>
                <?php } ?>
                <th>TOTAL</th>
            </tr>
            <?php foreach ($result as $name => $client) { ?>
                <tr>
                    <td><?= $name ?></td>
                    <?php foreach ($months as $month) { ?>
                        <td class="number"><?= number_format(isset($client[$month]) ? $client[$month] : 0,2,","," ") ?></td>
                        <?php
                        $somme += isset($client[$month]) ? $client[$month] : 0;
                        $totals[$month] += isset($client[$month]) ? $client[$month] : 0;
                    }
                    ?>
                    <td class="number"><?= number_format($somme,2,","," ") ?></td>
                </tr>
                <?php
                $somme = 0;
            } ?>
            <tr>
                <th>TOTAL</th>
                <?php foreach ($months as $month) { ?>
                    <td class="number"><?= number_format($totals[$month],2,","," ") ?></td>
                    <?php
                    $total += $totals[$month];
                } ?>
                <td class="number"><?= number_format($total,2,","," ") ?></td>
            </tr>
            <tr>
                <th>NOMBRE DES EFFETS:</th>
                <th colspan="<?= count($months) + 1 ?>"><?= count($result) ?></th>
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
