<!-- First Section one Column -->
<div class="x_panel">
    <div class="row">
        <div class="col-xs-12">
            <h4>Etats des Carnets par Année</h4>
            <?= form_open('etats_carnets/generate_report') ?>
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
