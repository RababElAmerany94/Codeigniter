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
            <?php if (isset($client_name)) { ?>
            <div style="font-size:18px;vertical-align:bottom;"><strong>Client: </strong><?= $client_name ?></div>
            <?php } ?>

	        <br><br><br>

        </header>
        <table border="1">
            <tr>
                <th width="20">Num<br>Chèque</th>
                <th>Banque</th>
                <th>Montant</th>
                <th>Code<br>Client</th>
                <th>Tire /<br>Endosseur</th>
                <th>Num<br>Facture</th>
                <th>Date<br>Facture</th>
            </tr>
            <?php for ($i = 0; $i < count($result); $i++) { ?>
            <tr>
                <td><?= $result[$i]['Num_Cheque'] ?></td>
                <td><?= $result[$i]['Nom_Banque'] ?></td>
                <td class="number"><?= number_format($result[$i]['Montant_Cheque'],2,","," ") ?></td>
                <td><?= $result[$i]['Id_Client'] ?></td>
                <td><?= $result[$i]['Tire'] ?><br><?= $result[$i]['Endosseur'] ?></td>
                <td><?= $result[$i]['Num_Facture'] ?></td>
                <td><?= (new DateTime($result[$i]['Date_Facture']))->format('d/m/Y') ?></td>
            </tr>
            <?php
            $montant_sum += $result[$i]['Montant_Cheque'];
            }
            ?>
            <tr>
                <th>TOTAL</th>
                <th></th>
                <th class="number"><?= number_format($montant_sum,2,","," ") ?></th>
                <th colspan="4"></th>
            </tr>
            <tr>
                <th colspan="2">NOMBRE DES CHÈQUES: <?= count($result) ?></th>
                <th colspan="5"></th>
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
