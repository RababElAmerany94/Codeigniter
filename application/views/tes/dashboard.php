<div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel tile fixed_height_320">
            <div class="x_title">
                <h2>Cette Année</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" id="top_dispatchers">
                <h4>Ventes</h4>
                <strong>Factures:</strong> <?= $factures_year ?><br>
                <strong>Effets:</strong> <?= $effets_year ?><br>
                <strong>Cheques:</strong> <?= $cheques_year ?><br>
                <strong>Virements/Versements:</strong> <?= $virements_year ?><br>
                <br>
                <h4>Achats</h4>
                <strong>Engagements d'Importation:</strong> <?= $engagements_year ?><br>
                <strong>Déclarations DUM:</strong> <?= $import_year ?><br>
                <strong>Credocs:</strong> <?= $credocs_year ?><br>
                <strong>Effets Fournisseurs:</strong> <?= $fournisseurs_year ?><br>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel tile fixed_height_320 overflow_hidden">
            <div class="x_title">
                <h2>Ce Mois</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <h4>Ventes</h4>
                <strong>Factures:</strong> <?= $factures_month ?><br>
                <strong>Effets:</strong> <?= $effets_month ?><br>
                <strong>Cheques:</strong> <?= $cheques_month ?><br>
                <strong>Virements/Versements:</strong> <?= $virements_month ?><br>
                <br>
                <h4>Achats</h4>
                <strong>Engagements d'Importation:</strong> <?= $engagements_month ?><br>
                <strong>Déclarations DUM:</strong> <?= $import_month ?><br>
                <strong>Credocs:</strong> <?= $credocs_month ?><br>
                <strong>Effets Fournisseurs:</strong> <?= $fournisseurs_month ?><br>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="x_panel tile fixed_height_320 overflow_hidden">
            <div class="x_title">
                <h2>Ce Jour</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <h4>Ventes</h4>
                <strong>Factures:</strong> <?= $factures_day ?><br>
                <strong>Effets:</strong> <?= $effets_day ?><br>
                <strong>Cheques:</strong> <?= $cheques_day ?><br>
                <strong>Virements/Versements:</strong> <?= $virements_day ?><br>
                <br>
                <h4>Achats</h4>
                <strong>Engagements d'Importation:</strong> <?= $engagements_day ?><br>
                <strong>Déclarations DUM:</strong> <?= $import_day ?><br>
                <strong>Credocs:</strong> <?= $credocs_day ?><br>
                <strong>Effets Fournisseurs:</strong> <?= $fournisseurs_day ?><br>
            </div>
        </div>
    </div>
</div>
