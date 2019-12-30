<?php
$montant_sum = 0;
$montant_mixte_sum = 0;
$montant_facture_sum = [];
$effets_sum = 0;
$cheques_sum = 0;
$versements_sum = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" type="text/css" href="<?= __DIR__ ?>/../../../assets/css/print.css"/>
</head>
<body>
<header>
    <div class="row">
        <div class="row_left" style="float: left;">
            <?= $company_name ?>
        </div>
        <div class="row_right" style="float: right;">
            <?= date('d/m/Y') ?>
        </div>
    </div>

    <br><br><br><br>

    <h2 class="title"><?= $title ?></h2>

    <br><br>
</header>
<?php if (!empty($items)) : ?>
    <br>
    <h4>Contrôle de Règlement simple des Factures</h4>
    <br>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Code Client</th>
            <th>Nom Client</th>
            <th>Num Facture</th>
            <th>Date Facture</th>
            <th>Montant</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item) { ?>
            <tr>
                <td><?= $item['Id_Client'] ?></td>
                <td><?= $item['RaisonSociale'] ?></td>
                <td><?= $item['Num_Facture'] ?></td>
                <td><?= $item['Date_Facture'] ?></td>
                <td class="number"><?= number_format($item['Montant'], 2, ',', ' ') ?></td>
            </tr>
            <?php
            if ($item['table'] == 'effets') $effets_sum += $item['Montant'];
            if ($item['table'] == 'cheques') $cheques_sum += $item['Montant'];
            if ($item['table'] == 'versements') $versements_sum += $item['Montant'];
            $montant_sum += $item['Montant'];
            $montant_facture_sum[$item['Num_Facture']] = $item['Montant_Facture'];
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="2">NOMBRE DES FACTURES: <?= count($items) ?></th>
            <th></th>
            <th>TOTAL</th>
            <th class="number"><?= number_format($montant_sum, 2, ",", " ") ?></th>
        </tr>
        </tfoot>
    </table>
<?php endif ?>
<?php if (!empty($items_mixte)) : ?>
    <br>
    <h4>Contrôle de Règlement mixte des Factures</h4>
    <br>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Code Client</th>
            <th>Nom Client</th>
            <th>Num Facture</th>
            <th>Date Facture</th>
            <th>Montant</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items_mixte as $item) { ?>
            <tr>
                <td><?= $item['Id_Client'] ?></td>
                <td><?= $item['RaisonSociale'] ?></td>
                <td><?= $item['Num_Facture'] ?></td>
                <td><?= $item['Date_Facture'] ?></td>
                <td class="number"><?= number_format($item['Montant'], 2, ',', ' ') ?></td>
            </tr>
            <?php
            if ($item['table'] == 'effets') $effets_sum += $item['Montant'];
            if ($item['table'] == 'cheques') $cheques_sum += $item['Montant'];
            if ($item['table'] == 'versements') $versements_sum += $item['Montant'];
            $montant_mixte_sum += $item['Montant'];
            $montant_facture_sum[$item['Num_Facture']] = $item['Montant_Facture'];
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="2">NOMBRE DES FACTURES: <?= count($items_mixte) ?></th>
            <th></th>
            <th>TOTAL</th>
            <th class="number"><?= number_format($montant_mixte_sum, 2, ',', ' ') ?></th>
        </tr>
        </tfoot>
    </table>
<?php endif ?>

<?php $montant_facture_sum = is_array($montant_facture_sum) ? array_sum(array_values($montant_facture_sum)) : $montant_facture_sum ?>

<br>
<p>Total des Factures: <strong><?= number_format($montant_facture_sum, 2, ",", " ") ?></strong></p>
<p>Règlement par Effets Bordereau Num <?= $effets_Bordereau ?>: <strong><?= number_format($effets_sum, 2, ",", " ") ?></strong></p>
<p>Règlement par Chèques Bordereau Num <?= $cheques_Bordereau ?>: <strong><?= number_format($cheques_sum, 2, ",", " ") ?></strong></p>
<p>Règlement par Espèce: <strong><?= number_format($versements_sum, 2, ",", " ") ?></strong></p>
<p>Total des Règlements: <strong><?= number_format($cheques_sum + $effets_sum + $versements_sum, 2, ",", " ") ?></strong></p>
<hr>
<p>Différence: <strong><?= number_format($montant_facture_sum - ($cheques_sum + $effets_sum + $versements_sum), 2, ",", " ") ?></strong></p>

<?php
if (isset($pdf)) {
    $x = $pdf->get_width() - 85;
    $y = $pdf->get_height() - 35;
    $text = 'Page {PAGE_NUM} / {PAGE_COUNT}';
    $font = null;
    $size = 10;
    $color = array(0, 0, 0);
    $word_space = 0.0;  //  default
    $char_space = 0.0;  //  default
    $angle = 0.0;   //  default
    $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
}
?>
</body>
</html>
