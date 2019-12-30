<!-- First Section one Column -->
<div class="x_panel">
    <div class="row">
        <div class="col-xs-12">
            <h4>Liste Détaillée des Effets à Payer</h4>
            <?= form_open('etats_effets_fournisseurs/liste_effets_payer') ?>
            <div class="form-group form-inline">
                <label>Fournisseur: </label>
                <select id="fournisseur-select" class="form-control" name="fournisseur">
                    <option value="0" selected>Tous les Fournisseurs</option>
                    <?php foreach ($fournisseurs as $fournisseur) { ?>
                        <option value="<?php echo $fournisseur['Id_Fournisseur']; ?>"><?php echo $fournisseur['Id_Fournisseur'] . ' - ' . $fournisseur['RaisonSociale'] ?></option>;
                    <?php } ?>
                </select>
            </div>
            <?= form_submit(array('name' => 'submit', 'value' => 'Générer', 'class' => 'btn btn-primary')) ?>
            <?= form_close() ?>
        </div>
    </div>
</div>
<!-- /First Section one Column -->
