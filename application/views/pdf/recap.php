<?php
for ($i = 3; $i >= 0; $i--) {
    $totals[$year - $i] = 0;
}
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
            <div class="row ">
                <div class="row_left">
                    <?= $company_name ?>
                </div>
                <div class="row_right">
                    <?= date('d/m/Y') ?>
                </div>
            </div>

	        <br><br>

            <h2 class="title"><?= $title ?></h2>

            <br><br><br>
        </header>
        <table border="1">
            <tr>
                <th>MOIS / ANNEE</th>
                <?php for ($i = 3; $i >= 0; $i--) { ?>
                    <th><?= $year - $i ?></th>
                <?php } ?>
            </tr>
            <?php foreach ($result as $month => $rows) { ?>
                <tr>
                    <td><?= $month ?></td>
                    <?php for ($i = 3; $i >= 0; $i--) { ?>
                        <?php $total = isset($rows[$year - $i]) ? $rows[$year - $i] : 0; ?>
                        <td class="number"><?= number_format($total, 2, ",", " ") ?></td>
                        <?php $totals[$year - $i] += $total; ?>
                    <?php } ?>
                </tr>
            <?php } ?>
            <tr>
                <th>TOTAL</th>
                <?php for ($i = 3; $i >= 0; $i--) { ?>
                    <th class="number"><?= number_format($totals[$year - $i], 2, ",", " ") ?></th>
                <?php } ?>
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
