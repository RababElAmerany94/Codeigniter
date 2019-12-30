<!-- First Section one Column -->
<div class="x_panel">
    <div class="row">
        <div class="col-xs-12">
            <h4>Etat Général Récapitulatif</h4>
            <?= form_open('etats_recapitulatifs/generate_report') ?>
            <div class="form-group form-inline">
                <label>Année: </label>
                <?= form_input(array('type' => 'number', 'name' => 'year', 'min' => '1900', 'max' => '2099', 'class' => 'form-control', 'value' => (new DateTime())->format('Y'))) ?>
            </div>
            <div class="form-group form-inline">
                <label>Affichage:</label>
                <div class="radio-inline">
                    <label><?= form_radio('period', 'mois', TRUE) ?> Par mois</label>
                    <label style="margin-left: 25px;"><?= form_radio('period', 'an') ?> Par an</label>
                </div>
            </div>
            <div class="form-group form-inline">
                <label>Type:</label>
                <div class="radio-inline">
                    <label><?= form_radio('type', 'dirhams', TRUE) ?> En Dirhams</label>
                    <label style="margin-left: 25px;"><?= form_radio('type', 'quantité') ?> En Quantité</label>
                </div>
            </div>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <h4>Effets en Portefeuille <strong style="color:black;">-</strong> Effets à Payer <strong style="color:black;">+</strong> Factures Non-Réglées</h4>
            <?= form_open('etats_recapitulatifs/generate_report_portefeuille') ?>
            <div class="form-group form-inline">
                <label>Année: </label>
                <?= form_input(array('type' => 'number', 'name' => 'year', 'min' => '1900', 'max' => '2099', 'class' => 'form-control', 'value' => (new DateTime())->format('Y'))) ?>
            </div>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
</div>
<!-- /First Section one Column -->
