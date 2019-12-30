<!-- First Section one Column -->
<div class="x_panel">
    <div class="row">
        <div class="col-xs-12">
            <h4>Liste des Effets Reçus</h4>
            <?= form_open('etats_effets/liste_effets_recus') ?>
            <div class="form-group form-inline">
                <label>Date Début: </label>
                <?= form_input(array('type' => 'text', 'name' => 'date_debut', 'id' => 'date_debut1', 'placeholder' => 'dd/mm/yyyy', 'class' => 'form-control date_debut', 'value' => '01/01/' . (new DateTime())->format('Y'))) ?>
                <label>Date Fin: </label>
                <?= form_input(array('type' => 'text', 'name' => 'date_fin', 'id' => 'date_fin1', 'placeholder' => 'dd/mm/yyyy', 'class' => 'date_fin form-control', 'value' => (new DateTime())->format('d/m/Y'))) ?>
            </div>
            <div class="form-group form-inline">
                <label>Client: </label>
                <select id="client-select1" class="form-control" name="client">
                    <option value="0" selected>Tous les Clients</option>
                    <?php foreach ($clients as $client) { ?>
                        <option value="<?php echo $client['Id_Client']; ?>"><?php echo $client['Id_Client'] . ' - ' . $client['RaisonSociale'] ?></option>;
                    <?php } ?>
                </select>
            </div>
            <div class="form-group form-inline">
                <div class="radio-inline">
                    <label><?= form_checkbox('non_echus', 'non_echus') ?> Effets Non-Echus</label>
                </div>

                <div class="radio-inline">
                    <label><?= form_checkbox('portefeuille', 'portefeuille') ?> Effets en Portefeuille</label>
                </div>
            </div>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <h4>Résumé des Effets Reçus par période</h4>
            <?= form_open('etats_effets/effets_recus_periode') ?>
            <div class="form-group form-inline">
                <label>Date Début: </label>
                <?= form_input(array('type' => 'text', 'name' => 'date_debut', 'placeholder' => 'dd/mm/yyyy', 'id' => 'date_debut2', 'class' => 'form-control', 'value' => '01/' . (new DateTime())->modify('-2 months')->format('m/Y'))) ?>
                <label>Date Fin: </label>
                <?= form_input(array('type' => 'text', 'name' => 'date_fin', 'placeholder' => 'dd/mm/yyyy', 'id' => 'date_fin2', 'class' => 'date_fin form-control', 'value' => (new DateTime())->modify('last day of this month')->format('d/m/Y'))) ?>
            </div>
            <div class="form-group form-inline">
                <label>Client: </label>
                <select id="client-select2" class="form-control" name="client">
                    <option value="0" selected>Tous les Clients</option>
                    <?php foreach ($clients as $client) { ?>
                        <option value="<?php echo $client['Id_Client']; ?>"><?php echo $client['Id_Client'] . ' - ' . $client['RaisonSociale'] ?></option>;
                    <?php } ?>
                </select>
            </div>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <h4>Effets à Recevoir</h4>
            <?= form_open('etats_effets/effets_recevoir') ?>
            <div class="form-group">
                <label for="">Bordereau Numéro</label>
                <?= form_input(array('type' => 'number', 'name' => 'bordereau', 'placeholder' => 'Bordereau Numéro', 'value' => $bordereau)) ?>
            </div>

            <div class="form-group form-inline">
                <div class="radio-inline">
                    <label><?= form_checkbox('répartitions', 'répartitions') ?> Afficher sous forme de répartitions mensuelles</label>
                </div>
            </div>
                <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
</div>
<!-- /First Section one Column -->
