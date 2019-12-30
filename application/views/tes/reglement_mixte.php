<?php if ('index' == $data['state']) { ?>
    <div id='list-report-error' class='report-div error'></div>
    <div id='list-report-success' class='report-div success report-list'></div>
    <div class="flexigrid" style='width: 100%;' data-unique-hash="1004f0b6dc4844a2933eb6c94b90d795">
        <div id="hidden-operations" class="hidden-operations"></div>
        <div class="mDiv">
            <div class="ftitle">&nbsp;</div>

            <div title="Réduire/Agrandir" class="ptogtitle">
                <span></span>
            </div>
        </div>

        <div id='main-table-box' class="main-table-box">
            <div class="tDiv">
                <div class="tDiv2">
                    <a href='Reglement_mixte/add' title='Ajouter Règlement Mixte'
                       class='add-anchor add_button'>
                        <div class="fbutton">
                            <div>
                                <span class="add">Ajouter Règlement Mixte</span>
                            </div>
                        </div>
                    </a>
                    <div class="btnseparator">
                    </div>
                </div>

                <div class="tDiv3">
                    <a class="export-anchor" href="Reglement_mixte/export"
                       download>
                        <div class="fbutton">
                            <div>
                                <span class="export">Exporter</span>
                            </div>
                        </div>
                    </a>
                    <div class="btnseparator"></div>
                    <a class="print-anchor" data-url="Reglement_mixte/print">
                        <div class="fbutton">
                            <div>
                                <span class="print">Imprimer</span>
                            </div>
                        </div>
                    </a>
                    <div class="btnseparator"></div>
                </div>

                <div class='clear'></div>
            </div>

            <div id='ajax_list' class="ajax_list">
                <div class="bDiv">
                    <table cellspacing="0" cellpadding="0" border="0" id="flex1">
                        <thead>
                        <tr class='hDiv'>
                            <th width='16%'>
                                <div class="text-left field-sorting" rel='Date Règlement'>ID Règlement</div>
                            </th>
                            <th width='16%'>
                                <div class="text-left field-sorting" rel='Factures'>Factures</div>
                            </th>
                            <th width='16%'>
                                <div class="text-left field-sorting" rel='Effets'>Effets</div>
                            </th>
                            <th width='16%'>
                                <div class="text-left field-sorting" rel='Chèques'>Chèques</div>
                            </th>
                            <th width='16%'>
                                <div class="text-left field-sorting" rel='Virements et Versements'>Virements et
                                    Versements
                                </div>
                            </th>
                            <th align="left" abbr="tools" axis="col1" class="" width='20%'>
                                <div class="text-right">Actions</div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
						<?php $loop = 0; ?>
						<?php foreach ($data['reglements'] as $key => $row) { ?>
                            <tr<?= ($loop % 2 == 1) ? ' class="erow"' : '' ?>>
                                <td width="16%"><?= $key ?></td>
                                <td width="16%"><?= $row['factures_string'] ?></td>
                                <td width="16%"><?= $row['effets_string'] ?></td>
                                <td width="16%"><?= $row['cheques_string'] ?></td>
                                <td width="16%"><?= $row['virements_string'] ?></td>
                                <td width="20%">
                                    <div class="tools">
                                        <a href="Reglement_mixte/delete/<?= $key ?>"
                                           title="Supprimer Règlement Mixte" class="delete-row"
                                           onclick="return confirm('Are you sure you want to delete this item?');">
                                            <span class="delete-icon"></span>
                                        </a>
                                        <a href="Reglement_mixte/show/<?= $key ?>"
                                           title="Voir Règlement Mixte" class="edit_button"><span
                                                    class="read-icon"></span></a>

                                        <!--										<div class="clear"></div>-->
                                    </div>
                                </td>
                            </tr>
							<?php $loop++; ?>
						<?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <form action="Reglement_mixte/ajax_list" method="post"
                  id="filtering_form"
                  class="filtering_form" autocomplete="off"
                  data-ajax-list-info-url="Reglement_mixte/ajax_list_info"
                  accept-charset="utf-8">

                <div class="pDiv">
                    <div class="pDiv2">
                        <div class="pGroup">
						<span class="pcontrol">
							Afficher
							<select name="per_page" id='per_page' class="per_page">
								<option value="10" selected="selected">10&nbsp;&nbsp;</option>
								<option value="25">25&nbsp;&nbsp;</option>
								<option value="50">50&nbsp;&nbsp;</option>
								<option value="100">100&nbsp;&nbsp;</option>
							</select>
							entrées
						</span>
                        </div>

                        <div class="btnseparator"></div>

                        <div class="pGroup">
                            <div class="pFirst pButton first-button">
                                <span></span>
                            </div>
                            <div class="pPrev pButton prev-button">
                                <span></span>
                            </div>
                        </div>

                        <div class="btnseparator"></div>

                        <div class="pGroup">
						<span class="pcontrol">Page <input name='page' type="text" value="1" size="4" id='crud_page'
                                                           class="crud_page"> de <span id='last-page-number'
                                                                                       class="last-page-number">1</span></span>
                        </div>

                        <div class="btnseparator"></div>

                        <div class="pGroup">
                            <div class="pNext pButton next-button">
                                <span></span>
                            </div>
                            <div class="pLast pButton last-button">
                                <span></span>
                            </div>
                        </div>

                        <div class="btnseparator"></div>

                        <div class="pGroup">
                            <div class="pReload pButton ajax_refresh_and_loading" id='ajax_refresh_and_loading'>
                                <span></span>
                            </div>
                        </div>

                        <div class="btnseparator"></div>

                        <div class="pGroup">
						<span class="pPageStat">Affichage de <span id='page-starts-from'
                                                                   class='page-starts-from'>1</span> à <span
                                    id='page-ends-to'
                                    class='page-ends-to'><?= ceil(count($data['reglements']) / 10) ?></span> de <span
                                    id='total_items'
                                    class='total_items'><?= count($data['reglements']) ?></span> éléments</span>
                        </div>
                    </div>

                    <div style="clear: both;">
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } elseif ('add' == $data['state'] || 'edit' == $data['state']) { ?>
    <style>
        .col-btn {
            float: left;
            margin-top: 83px;
        }

        .col-btn button {
            display: block;
        }

        select[multiple], select[size] {
            height: 200px !important;
        }

        .row {
            margin-top: 20px;
        }

        button.submit {
            padding: 10px 20px;
        }

        option:disabled {
            color: #687d90;
            cursor: not-allowed;
        }

        .alert-success {
            margin: 20px 0;
            width: 80%;
            display: none;
        }
    </style>
    <form>
        <div class="row">
            <div class="col-md-3">
                <h5>Factures</h5>
                <select id="factures_data" class="form-control" multiple="multiple">
					<?php foreach ($data['factures'] as $facture) { ?>
                        <option data-num="<?= $facture->Num_Facture ?>"
                                data-year="<?= date('Y', strtotime($facture->Date_Facture)) ?>"
                                data-id="<?= $facture->Id_Facture ?>"
                                value="<?= $facture->Id_Facture ?>">
							<?= $facture->Num_Facture . ' / ' . date('Y', strtotime($facture->Date_Facture)) ?>
                        </option>
					<?php } ?>
                </select>
            </div>
            <div class="col-btn">
                <button type="button" onclick="add('factures', true)">>></button>
                <button type="button" onclick="remove('factures')"><<</button>
            </div>
            <div class="col-md-3">
                <h5>Listes des Factures sélectionnées</h5>
                <select id="factures_select" name="factures_select" class="form-control" multiple="multiple"></select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <h5>Chèques</h5>
                <select id="cheques_select" name="cheques_select" class="form-control" multiple="multiple"></select>
            </div>
            <div class="col-md-3">
                <h5>Effets</h5>
                <select id="effets_select" name="effets_select" class="form-control" multiple="multiple"></select>
            </div>
            <div class="col-md-3">
                <h5>Virements/Versements</h5>
                <select id="virements_versements_select" name="virements_versements_select" class="form-control"
                        multiple="multiple"></select>
            </div>
        </div>
    </form>

	<?php if ($data['state'] == 'add') { ?>
        <button class="btn btn-primary submit" style="margin:20px 0" onclick="sendData()">
            Règler les Factures
        </button>
	<?php } else { ?>
        <button class="btn btn-primary submit" onclick="sendData()">
            Mettre à jour et retourner à la liste
        </button>
	<?php } ?>

    <div class="alert alert-success">
        <p>Success</p>
    </div>

    <script>
		<?php
		if($data['state'] == 'edit') { ?>
        let cheques_selected = <?=  json_encode($data['cheques_selected'], true); ?>;
        let effets_selected = <?=  json_encode($data['effets_selected'], true); ?>;
        let virement_selected = <?=  json_encode($data['virement_selected'], true); ?>;
        let facture_selected = [];
        let date_created = <?=  json_encode($data['date_created'], true); ?>;

        window.onload = function () {
            cheques_selected.forEach(function (e) {
                if (!facture_selected.includes(e.Id_Facture)) {
                    facture_selected.push({id: e.Id_Facture});
                }
                $('#cheques_select').append('<option value="' + e.Id_Cheque + '" data-facture="' + e.Id_Facture + '" >' + e.Num_Cheque + '</option>');
            });

            effets_selected.forEach(function (e) {
                if (!facture_selected.includes(e.Id_Facture)) {
                    facture_selected.push({id: e.Id_Facture});
                }
                $('#effets_select').append('<option value="' + e.Id_Effet + '" data-facture="' + e.Id_Facture + '" >' + e.Num_Effet + '</option>');
            });

            virement_selected.forEach(function (e) {
                if (!facture_selected.includes(e.Id_Facture)) {
                    facture_selected.push({id: e.Id_Facture});
                }
                $('#virements_versements_select').append('<option value="' + e.Id_Operation + '" data-facture="' + e.Id_Facture + '" >' + e.Num_Operation + '</option>');
            });

            facture_selected.forEach(function (e) {
                let $facture_option = $("#factures_data option[value=" + e.id + "]");
                if ($facture_option) {
                    $facture_option.prop('hidden', true);
                    var id_facture = $facture_option.data('id');
                    var num_facture = $facture_option.data('num');
                    var year_facture = $facture_option.data('year');
                    console.log(e);
                    console.log(`${num_facture} / ${year_facture}`);
                    $('#factures_select').append(`<option value="${id_facture}">${num_facture} / ${year_facture}</option>`);

                }
            })
        };
		<?php } ?>

        function add(target, fetch = false) {
            $('#' + target + '_data option:selected').prop('hidden', true);
            var $data = $('#' + target + '_data option:selected');
            $('#' + target + '_data option:selected').prop("selected", false);

            if ($data.length) {
                Array.from($data).forEach(function (e) {
                    if (target == 'factures') {
                        var id_facture = $(e).data('id');
                        var num_facture = $(e).data('num');
                        var year_facture = $(e).data('year');
                        $('#factures_select').append(`<option value="${id_facture}">${num_facture} / ${year_facture}</option>`);
                    } else {
                        var id = $(e).data('id');
                        var num = $(e).data('num');
                        var data_facture = $(e).data('facture');
                        $(`#${target}_select`).append(`<option data-facture="${data_facture}" value="${id}"> ${num} </option>`);
                    }
                });

                if (fetch) {
                    fetch_data();
                }
            }
        }

        function remove(target) {
            $data = $('#' + target + '_select option:selected');

            if ($data.length) {
                Array.from($data).forEach(function (e) {
                    if (target == 'factures') {
                        $("#factures_data option[value='" + $(e).val() + "']").removeAttr('hidden');
                        //remove all related cheques/effets/virement
                        $("select[name*='select'] option[data-facture='" + $(e).val() + "']").remove();
                        $(e).remove();
                    } else {
                        $("#" + target + "_data option[data-id='" + $(e).val() + "']").removeAttr('hidden');
                        $(e).remove();
                    }
                    // If target is factures remove all related cheques/effet...
                    if (target == 'factures') {
                        $('option[data-facture="' + e + '"]').remove();
                    }
                });
            }
        }

        function sendData() {
            $(".alert-success").hide();
            var formData = new FormData();
            formData.append("effets", $.map($('#effets_select option'), function (e) {
                return e.value;
            }));

            formData.append("cheques", $.map($('#cheques_select option'), function (e) {
                return e.value;
            }));

            formData.append("virements", $.map($('#virements_versements_select option'), function (e) {
                return e.value;
            }));

            formData.append("factures", $.map($('#factures_select option'), function (e) {
                return e.value;
            }));

			<?php if($data['state'] == 'edit') { ?>
            formData.append("date_created", date_created);
			<?php } ?>

            var $url = "add";
            $.ajax({
                type: "POST",
                url: $url,
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function () {
                    $(".alert-success ").show();
                    //remove all existing values
                    $('#factures_select').find('option').remove();
                    $('#cheques_select').find('option').remove();
                    $('#effets_select').find('option').remove();
                    $('#virements_versements_select').find('option').remove();

                    window.location.href = "<?= base_url(); ?>Reglement_mixte";
                }
            });
        }

        function fetch_data() {
            var formData = new FormData();
            var factures = $.map($('#factures_select option'), function (e) {
                return {id_facture: e.value.split('-')[0], year: e.value.split('-')[1]}
            });

            factures.forEach((f) => {
                formData.append("factures[]", JSON.stringify(f));
            });

            var $url = "get_data";
            $.ajax({
                type: "POST",
                url: $url,
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function (response) {
                    response.effets.forEach(function (e) {
                            if ($("#effets_select option[value='" + e.Id_Effet + "']").length == 0) {
                                $('#effets_select').append('<option value="' + e.Id_Effet + `" data-facture="${e.Id_Facture}" disabled>` + e.Num_Effet + '</option>');
                            }
                        }
                    );

                    response.cheques.forEach(function (e) {
                            if ($("#cheques_select option[value='" + e.Id_Cheque + "']").length == 0) {
                                $('#cheques_select').append('<option value="' + e.Id_Cheque + `" data-facture="${e.Id_Facture}" disabled>` + e.Num_Cheque + '</option>');
                            }
                        }
                    );

                    response.virements.forEach(function (e) {
                            if ($("#virements_versements_select option[value='" + e.Id_Operation + "']").length == 0) {
                                $('#virements_versements_select').append('<option value="' + e.Id_Operation + `" data-facture="${e.Id_Facture}" disabled>` + e.Num_Operation + '</option>');
                            }
                        }
                    );
                }
            });
        }
    </script>
	<?php
} elseif ('show' == $data['state']) {
	$total_general = 0;
	$total_factures = 0;
	$order = 0;
	$montant_effet = 0;
	$montant_cheques = 0;
	$montant_virements = 0;
	?>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3 class="table-title"><?= $data['reglements_id'] ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3 class="table-title">Factures</h3>
                </div>

                <div class="x_content">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th width="20%">NUM FACTURE</th>
                            <th width="20%">DATE</th>
                            <th>MONTANT</th>
                        </tr>
                        </thead>

                        <tbody>
						<?php
						for ($i = 0; $i < count($data['factures']); $i++) {
							$facture = $data['factures'][$i];
							?>
                            <tr>
                                <td><?= $facture['Num_Facture'] ?></td>
                                <td><?= (new DateTime($facture['Date_Created']))->format('d/m/Y') ?></td>
                                <td style="width: 80px"><?= number_format($facture['Montant_Facture'], 2, ',', ' ') ?></td>
                            </tr>
							<?php
							$total_factures += $facture['Montant_Facture'];
						} ?>

                        <tr style="padding:20px 0;">
                            <th colspan="2">TOTAL</th>
                            <th style="width: 80px"><?= number_format($total_factures, 2, ',', ' ') ?></th>
                        </tr>

                        <tr>
                            <th colspan="3" style="text-align:left">NOMBRE DES
                                FACTURES: <?= count($data['factures']) ?></th>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

		<?php
		if (0 < count($data['effets'])) {
			$order++;
			?>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h3 class="table-title"><?= $order ?>. Effets</h3>
                    </div>

                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>NUM EFFET</th>
                                <th>ECHEANCE</th>
                                <th width="10%">MONTANT</th>
                                <th>TIRE</th>
                                <th>ENDOSSEUR</th>
                                <th>DOMICILIATION</th>
                            </tr>
                            </thead>

                            <tbody>
							<?php
							for ($j = 0; $j < count($data['effets']); $j++) {
								$effet = $data['effets'][$j];
								?>
                                <tr>
                                    <td><?= $effet['Num_Effet'] ?></td>
                                    <td><?= (new DateTime($effet['ECHEANCE']))->format('d/m/Y') ?></td>
                                    <td style="text-align:right"><?= number_format($effet['MONTANT'], 2, ",", " ") ?></td>
                                    <td><?= $effet['TIRE'] ?></td>
                                    <td><?= $effet['ENDOSSEUR'] ?></td>
                                    <td><?= $effet['DOMICILIATION'] ?></td>
                                </tr>
								<?php
								$montant_effet += $effet['MONTANT'];
								$total_general += $effet['MONTANT'];
							}
							?>
                            <tr style="padding:20px 0;">
                                <th colspan="2">TOTAL 1</th>
                                <th style="text-align:right"><?= number_format($montant_effet, 2, ",", " ") ?></th>
                                <th colspan="3"></th>
                            </tr>

                            <tr>
                                <th colspan="6" style="text-align:left">NOMBRE DES
                                    EFFETS: <?= count($data['effets']) ?></th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
			<?php
		}
		if (0 < count($data['cheques'])) {
			$order++;
			?>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h3 class="table-title"><?= $order ?>. Chèques</h3>
                    </div>

                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr class="cheque_title">
                                <th>NUM CHEQUE</th>
                                <th width="10%">MONTANT</th>
                                <th>TIRE</th>
                                <th>ENDOSSEUR</th>
                                <th>DOMICILIATION</th>
                                <th>VILLE</th>
                                <th>NUM REMISE</th>
                                <th>DATE REMISE</th>
                            </tr>
                            </thead>

                            <tbody>
							<?php
							for ($j = 0; $j < count($data['cheques']); $j++) {
								$cheque = $data['cheques'][$j];
								?>
                                <tr>
                                    <td><?= $cheque['NUM CHEQUE'] ?></td>
                                    <td style="text-align:right"><?= number_format($cheque['MONTANT'], 2, ",", " ") ?></td>
                                    <td><?= $cheque['TIRE'] ?></td>
                                    <td><?= $cheque['ENDOSSEUR'] ?></td>
                                    <td><?= $cheque['DOMICILIATION'] ?></td>
                                    <td><?= $cheque['VILLE'] ?></td>
                                    <td><?= $cheque['NUM REMISE'] ?></td>
                                    <td><?= (new DateTime($cheque['DATE REMISE']))->format('d/m/Y') ?></td>
                                </tr>
								<?php
								$montant_cheques += $cheque['MONTANT'];
								$total_general += $cheque['MONTANT'];
							}
							?>

                            <tr>
                                <th colspan="1">TOTAL 2</th>
                                <th style="text-align:right"><?= number_format($montant_cheques, 2, ",", " ") ?></th>
                                <th colspan="6"></th>
                            </tr>

                            <tr>
                                <th colspan="8" style="text-align:left">NOMBRE DES
                                    CHEQUES: <?= count($data['cheques']) ?></th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
			<?php
		}
		if (0 < count($data['virements'])) {
			$order++;
			?>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h3 class="table-title"><?= $order ?>. Espèces / Virement</h3>
                    </div>

                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>NUM VERSEMENT / VIREMENT</th>
                                <th width="10%">MONTANT</th>
                                <th>DATE VERSEMENT / VIREMENT</th>
                            </tr>
                            </thead>

                            <tbody>
							<?php
							for ($i = 0; $i < count($data['virements']); $i++) {
								$virement = $data['virements'][$j];
								?>
                                <tr>
                                    <td><?= $virement['NUM VERSEMENT'] ?></td>
                                    <td style="width: 120px"><?= number_format($virement['MONTANT'], 2, ",", " ") ?></td>
                                    <td><?= (new DateTime($virement['DATE VERSEMENT']))->format('d/m/Y') ?></td>
                                </tr>
								<?php
								$montant_virements += $virement['MONTANT'];
								$total_general += $virement['MONTANT'];
							}
							?>

                            <tr style="padding:20px 0;">
                                <th colspan="1">TOTAL 3</th>
                                <th style="text-align:right"><?= number_format($montant_virements, 2, ",", " ") ?></th>
                                <th colspan="1"></th>
                            </tr>

                            <tr>
                                <th colspan="3" style="text-align:left">NOMBRE
                                    D'OPERATIONS: <?= count($data['virements']) ?></th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
		<?php } ?>
    </div>
<?php } ?>

