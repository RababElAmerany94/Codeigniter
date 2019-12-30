<!-- First Section one Column -->
<div class="x_panel">
    <div class="row">
        <div class="col-xs-12">
            <h4>Liste des Fournisseurs</h4>
            <?= form_open('etats_fournisseurs/generate_report') ?>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
</div>
<!-- /First Section one Column -->
