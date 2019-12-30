<?php
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
        </header>
        <table border="1">
            <tr>
                <th>Num<br>Effet</th>
                <th>Echeance</th>
                <th>Montant</th>
                <th>Code<br>Client</th>
                <th>Tire /<br>Endosseur</th>
                <th>Code<br>Banque</th>
                <th>Num<br>Facture</th>
                <th>Date<br>Facture</th>
            </tr>
            <?php foreach ($result as $row) { ?>
                <tr>
                    <td><?= $row["Num_Effet"] ?></td>
                    <td><?= $row["Date_Echeance"] ?></td>
                    <td class="number"><?= number_format($row["Montant"],2,","," ") ?></td>
                    <td><?= $row["Id_Client"] ?></td>
                    <td><?= $row["Tire"] ?><br><?= $row["Endosseur"] ?></td>
                    <td><?= $row["Code_Banque"] ?></td>
                    <td><?= $row["Num_Facture"] ?></td>
                    <td><?= $row["Date_Facture"] ?></td>
                </tr>
                <?php
                $total += $row["Montant"];
            } ?>
            <tr>
                <th colspan="2">TOTAL</th>
                <td class="number"><b><?= number_format($total,2,","," ") ?></b></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <th colspan="2">NOMBRE DES EFFETS: <?= count($result) ?></th>
                <td colspan="6"></td>
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
