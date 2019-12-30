<?php
$total = 0;
$count = 0;
$totalCount = 0;
$controle = "";
$first = true;
$i = 0;
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
        <?php foreach ($result as $repartition_num => $repartition) {
            $subtotal = 0;
            $i++;
            if ($first || ($i-1) == count($result)) {
            $first = false;
            } else { ?>
        <div style="page-break-before: always;"></div>
            <?php } ?>
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
            <h3 class="title">REPARTITION NUM <?= $repartition_num ?></h3>
            <br>
        </header>
        <table border="1">
            <tr>
                <th>Num<br>Effet</th>
                <th>Echeance</th>
                <th>Montant</th>
                <th>Code<br>Client</th>
                <th>Tire<br>Endosseur</th>
                <th>Code<br>Banque</th>
                <th>Num<br>Facture</th>
                <th>Date<br>Facture</th>
            </tr>
            <?php foreach ($result[$repartition_num] as $row) { ?>
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
                $subtotal += $row["Montant"];
            }
            $count = count($result[$repartition_num]);
            $totalCount += $count;
            $montant = number_format($subtotal,2,","," ");
            ?>
            <tr>
                <th colspan="2">TOTAL</th>
                <td><b><?= $montant ?></b></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <th colspan="2">NOMBRE DES EFFETS: <?= $count ?></th>
                <td colspan="6"></td>
            </tr>
        </table>
        <?php
            $controle .= "<tr><td style='text-align:right;width:125px;'>REPARTITION NUM</td><td style=\"text-align:right;width:50px;\">{$repartition_num}</td><td style='width:50px;'>CONTIENT</td><td style=\"text-align:right;width:25px;\"><strong>{$count}</strong></td><td style='text-align:left;width:50px;'><strong>EFFET" . (($count > 1) ? "S" : "") . "</strong></td><td style='width:100px;'>AVEC UN TOTAL DE</td><td style=\"text-align:right;width:100px\"><strong>{$montant} DHS</strong></td></tr>";
            $total += $subtotal;
        } ?>
        <div>
            <h3><strong>CONTROLE</strong></h3>
            <br>
            <table border="0">
                <?= $controle ?>
                <tr><td colspan="7"><hr></td></tr>
                <tr>
                    <td colspan="2"><strong>TOTAL</strong></td>
                    <td></td>
                    <td style="text-align:right;width:25px;"><strong><?= $totalCount ?></strong></td>
                    <td style='text-align:left;width:50px;'><strong>EFFETS</strong></td>
                    <td colspan="2" style="text-align:right;"><strong><?= number_format($total,2,","," ") ?> DHS</strong></td>
                </tr>
            </table>
        </div>

        <script type="text/php">
            if (isset($pdf)) {
                $x = $pdf->get_width() - 85;
                $y = $pdf->get_height()-35;
                $text = "Page {PAGE_NUM}";
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
