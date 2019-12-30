<?php
$montant_sum = 0;
$montant_mixte_sum = 0;
$montant_facture_sum = [];
$effets_sum = 0;
$cheques_sum = 0;
$versements_sum = 0;
?>
<!-- First Section one Column -->
<div class="x_panel">
    <div class="row">
        <div class="col-xs-12">
            <!--            <h3>Contrôle de Règlement des Factures</h3>-->
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
                            <td><?= number_format($item['Montant'], 2, ',', ' ') ?></td>
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
                        <th><?= number_format($montant_sum, 2, ',', ' ') ?></th>
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
                            <td><?= number_format($item['Montant'], 2, ',', ' ') ?></td>
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
                        <th colspan="2">NOMBRE DES FACTURES: <?= count($items) ?></th>
                        <th></th>
                        <th>TOTAL</th>
                        <th><?= number_format($montant_mixte_sum, 2, ',', ' ') ?></th>
                    </tr>
                    </tfoot>
                </table>
            <?php endif ?>

            <?php $montant_facture_sum = is_array($montant_facture_sum) ? array_sum(array_values($montant_facture_sum)) : $montant_facture_sum ?>

            <p>Total des Factures: <strong><?= number_format($montant_facture_sum, 2, ",", ' ') ?></strong></p>
            <p>Règlement par Effets Bordereau Num <?= $effets_Bordereau ?>: <strong><?= number_format($effets_sum, 2, ",", ' ') ?></strong></p>
            <p>Règlement par Chèques Bordereau Num <?= $cheques_Bordereau ?>: <strong><?= number_format($cheques_sum, 2, ",", ' ') ?></strong></p>
            <p>Règlement par Espèce: <strong><?= number_format($versements_sum, 2, ",", " ") ?></strong></p>
            <p>Total des Règlements: <strong><?= number_format($cheques_sum + $effets_sum + $versements_sum, 2, ",", ' ') ?></strong></p>
            <hr>
            <p>Différence: <strong><?= number_format($montant_facture_sum - ($cheques_sum + $effets_sum + $versements_sum), 2, ',', " ") ?></strong></p>

            <br><br>
            <?= form_open('controle_reglement_factures/generate_report') ?>
            <?= form_submit(array('name' => 'submit', 'value' => 'Règler et Imprimer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
</div>
<!-- /First Section one Column -->