<!-- First Section one Column -->
<div class="x_panel">
    <div class="row">
        <div class="col-xs-12">
            <h4>Chèques à Encaisser</h4>
            <?= form_open('etats_cheques/generate_report') ?>
            <div class="form-group">
                <label for="">Bordereau Numéro</label>
                <?= form_input(array('type' => 'number', 'name' => 'bordereau', 'placeholder' => 'Bordereau Numéro', 'value' => $bordereau)) ?>
            </div>
            <div class="form-group">
                <label for="">Année</label>
                <?= form_input(array('type' => 'number', 'name' => 'year', 'placeholder' => 'Année', 'value' => (new DateTime())->format("Y"))) ?>
            </div>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
</div>
<!-- /First Section one Column -->