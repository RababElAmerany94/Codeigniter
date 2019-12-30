<div class="x_panel">
    <div class="row">
        <div class="col-xs-12">
            <?= form_open('Etats_factures/generate_report') ?>
            <h4>Journal Mensuel des Ventes par Mois</h4>
            <div class="form-group form-inline">
                <label>Mois : </label>
                <input required name="month" value="<?= (new DateTime())->format('n') ?>" type="number" min="1" max="12"
                       class="form-control">
            </div>
            <div class="form-group form-inline">
                <label>Année: </label>
                <?= form_input(array('type' => 'number', 'name' => 'year', 'min' => '1900', 'max' => '2099', 'class' => 'form-control', 'value' => (new DateTime())->format('Y'))) ?>
                <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary', 'style' => 'margin-bottom:0')) ?>
            </div>
            <?= form_close() ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <?= form_open('Etats_factures/generate_report') ?>
            <h4>Journal Mensuel des Ventes par Carnet</h4>
            <div class="form-group form-inline">
                <label>Num Carnet : </label>
                <?= form_input(array('type' => 'number', 'min' => 1, 'name' => 'carnet', 'class' => 'form-control', 'style' => "width:100px", 'required' => "required")) ?>
            </div>
            <div class="form-group form-inline">
                <label>Année: </label>
                <?= form_input(array('type' => 'number', 'name' => 'year', 'min' => '1900', 'max' => '2099', 'class' => 'form-control', 'value' => (new DateTime())->format('Y'))) ?>
            </div>
            <div class="form-group">
                <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary', 'style' => 'margin-bottom:0')) ?>
            </div>
            <?= form_close() ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <?= form_open('Etats_factures/generate_report') ?>
            <h4>Liste de toutes les Factures </h4>
            <div class="form-group form-inline">
                <label>Client </label>
                <select id="client-select" class="form-control" name="client" id="">
                    <option value=0>Tous les Clients</option>
                    <?php foreach ($clients as $client) { ?>
                        <option value="<?php echo $client['Id_Client']; ?>"><?php echo $client['Id_Client'] . ' - ' . $client['RaisonSociale'] ?></option>;
                    <?php } ?>
                </select>
            </div>
            <div class="form-group form-inline">
                <input type="checkbox" id="toggle-year" style="transform: translateY(3px);">
                <label>Année: </label>
                <?= form_input(array('type' => 'number', 'disabled' => true, 'id' => 'year-list', 'name' => 'year', 'min' => '1900', 'max' => '2099', 'class' => 'form-control', 'value' => (new DateTime())->format('Y'))) ?>
            </div>
            <div class="form-group form-inline">
                <input type="checkbox" name="type" style="transform: translateY(3px);">
                <label style="font-weight:bolder" for="type">Factures Non-Réglées Seulement</label>

            </div>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary', 'style' => 'margin-bottom:0')) ?>

            <?= form_close() ?>
        </div>
    </div>
</div>