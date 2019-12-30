<?php
$produits_finis_sum = 0;
$dechets_sum = 0;
$fils_sum = 0;
$faconnage_sum = 0;
$total_sum = 0;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Chiffre d'Affaires Annuel H.T. / Nature</title>
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

	        <h2 class="title">Chiffre d'Affaires Annuel H.T. / Nature</h2>
            <br><br><br>
        </header>
        <table border="1">
            <tr>
                <th>MOIS</th>
                <th>PRODUITS FINIS</th>
                <th>DECHETS</th>
                <th>FILS</th>
                <th>FACONNAGE</th>
                <th>TOTAL</th>
            </tr>
            <?php for ($i = 0; $i < 12; $i++) { ?>
                <?php $produitsFinis = isset($result[$i]) ? $result[$i]['Produits Finis'] : 0; ?>
                <?php $dechets = isset($result[$i]) ? $result[$i]['Dechets'] : 0; ?>
                <?php $fils = isset($result[$i]) ? $result[$i]['Fils'] : 0; ?>
                <?php $faconnage = isset($result[$i]) ? $result[$i]['Faconnage'] : 0; ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td class="number"><?= number_format($produitsFinis, 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($dechets, 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($fils, 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($faconnage, 2, ",", " ") ?></td>
                    <td class="number"><?= number_format($produitsFinis + $fils + $dechets + $faconnage, 2, ",", " ") ?></td>
                </tr>
                <?php
                $produits_finis_sum += $produitsFinis;
                $dechets_sum += $dechets;
                $fils_sum += $fils;
                $faconnage_sum += $faconnage;
                $total_sum = $produits_finis_sum + $fils_sum/*+$dechets_sum+$faconnage_sum*/
                ;
            }
            ?>
            <tr>
                <th>TOTAL</th>
                <th class="number"><?= number_format($produits_finis_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($dechets_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($fils_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($faconnage_sum, 2, ",", " ") ?></th>
                <th class="number"><?= number_format($total_sum, 2, ",", " ") ?></th>
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
