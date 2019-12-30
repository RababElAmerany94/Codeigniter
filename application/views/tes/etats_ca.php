<!-- First Section one Column -->
<div class="x_panel">
    <div class="row">
        <div class="col-xs-12">
            <h4>Chiffre d'Affaires Annuel</h4>
            <?= form_open('etats_ca/generate_report') ?>
            <div class="form-group form-inline">
                <label>Année: </label>
                <?= form_input(array('type' => 'number', 'name' => 'year', 'min' => '1900', 'max' => '2099', 'class' => 'form-control', 'value' => (new DateTime())->format('Y'))) ?>
            </div>
            <div class="form-group form-inline">
                <label>Type:</label>
                <div class="radio-inline">
                    <label><?= form_radio('report', 'ca_a') ?> CA Annuel</label>
                    <label style="margin-left: 25px;"><?= form_radio('report', 'ca_a_n', TRUE) ?> CA Annuel par
                        Nature</label>
                </div>
            </div>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <h4>Récapitulation du C.A. Annuel (4 ans)</h4>
            <?= form_open('etats_ca/generate_report') ?>
            <div class="form-group form-inline">
                <label>Année: </label>
                <?= form_input(array('type' => 'number', 'name' => 'year', 'min' => '1900', 'max' => '2099', 'class' => 'form-control', 'value' => (new DateTime())->format('Y'))) ?>
            </div>
            <div class="form-group form-inline">
                <label>Type:</label>
                <div class="radio-inline">
                    <label><?= form_radio('recapitulation', 'dirham', TRUE) ?> En Dirham</label>
                    <label style="margin-left: 25px;"><?= form_radio('recapitulation', 'quantite') ?> En Quantité (Kg)</label>
                </div>
            </div>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
</div>
<!-- /First Section one Column -->