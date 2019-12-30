<?php
$montant_sum = 0;
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
            <br>
            <h2 class="title"><?= $title ?></h2>
            <br><br><br>
        </header>
        <table border="1">
            <tr>
                <th width="20">Num Effet / Credoc</th>
                <th>Echeance</th>
                <th>Banque</th>
                <th>Montant</th>
                <?php if (!isset($fournisseur_name)) { ?>
                <th>Fournisseur</th>
                <?php } ?>
            </tr>
            <?php for ($i = 0; $i < count($result); $i++) { ?>
            <tr>
                <td><?= $result[$i]['Num_Effet'] ?></td>
                <td><?= (new DateTime($result[$i]['Date_Echeance']))->format('d/m/Y') ?></td>
                <td><?= $result[$i]['Nom_Banque'] ?></td>
                <td class="number"><?= number_format($result[$i]['Montant_MAD'],2,","," ") ?></td>
                <?php if (!isset($fournisseur_name)) { ?>
                <td><?= $result[$i]['RaisonSociale'] ?></td>
                <?php } ?>
            </tr>
            <?php
            $montant_sum += $result[$i]['Montant_MAD'];
            }
            ?>
            <tr>
                <th colspan="2">NOMBRE DES EFFETS/CREDOCS: <?= count($result) ?></th>
                <th>TOTAL</th>
                <th class="number"><?= number_format($montant_sum,2,","," ") ?></th>
                <?php if (!isset($fournisseur_name)) { ?>
                <th></th>
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
