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
            <br><br>
            <h2 class="title"><?= $title ?></h2>
            <br>
            <div style="font-size:16px;vertical-align:bottom;"><strong>Date Début: </strong><?= $date_debut ?></div>
            <br>
            <div style="font-size:16px;vertical-align:bottom;"><strong>Date Fin: </strong><?= $date_fin ?></div>
            <br><br>
        </header>
        <?php foreach ($result as $mois => $table) {
            $total = 0;
        ?>
        <br>
        <div style="font-size:16px;vertical-align:bottom;"><strong>MOIS: </strong><?= $mois ?></div>
        <table border="1">
            <tr>
                <th>PÉRIODE</th>
                <th>MONTANT</th>
            </tr>
            <?php foreach ($table as $période => $montant) { ?>
                <tr>
                    <td><?= $période ?></td>
                    <td class="number"><?= number_format($montant,2,","," ") ?></td>
                </tr>
                <?php
                $total += $montant;
                } ?>
            <tr>
                <th>TOTAL</th>
                <td class="number"><?= number_format($total,2,","," ") ?></td>
            </tr>
        </table>
        <?php } ?>

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
