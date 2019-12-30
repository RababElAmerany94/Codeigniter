<!-- First Section one Column -->
<div class="x_panel">
	<div class="row">
		<div class="col-xs-12">
			<h4>Evolution Annuelle des Achats Totaux</h4>

			<?= form_open('etats_achats/generate_report') ?>

			<div class="form-group form-inline">
				<label for="">Type</label>
				<input type="radio" name="etats" value="kg" checked>
				<label>En Quantité </label>
				<input type="radio" name="etats" value="dh">
				<label>En Dirhams H.T.</label>
			</div>

			<div class="form-group form-inline">
				<label for="">Année</label>
				<?= form_input(array('type'        => 'number',
				                     'name'        => 'year',
				                     'style'       => 'width:100px',
				                     'class'       => 'form-control',
				                     'placeholder' => 'Année',
				                     'value'       => (new DateTime())->format("Y")
				)) ?>
			</div>

			<div class="form-group form-inline">
				<label for="">Nature</label>
				<select name="code_nature" class="form-control">
					<?php
					foreach ($code_natures as $nature)
					{
						echo "<option value='$nature->Code-$nature->Description'>$nature->Code_Nature - $nature->Description </option>";
					}
					?>
				</select>
			</div>

			<?= form_submit(array('name'  => 'submit',
			                      'value' => 'Générer',
			                      'class' => 'btn btn-primary'
			)) ?>
			<?= form_close() ?>
		</div>
	</div>
	<!-- /div -->

	<div class="row">
		<div class="col-xs-12">
			<h4>Résumé des Achats</h4>

			<?= form_open('etats_achats/generate_report_4') ?>

			<div class="form-group form-inline">
				<label for="">Type</label>
				<input type="radio" name="etats" value="kg" checked>
				<label>En Quantité </label>
				<input type="radio" name="etats" value="dh">
				<label>En Dirhams H.T</label>
			</div>

			<div class="form-group form-inline">
				<label for="">Année</label>

				<?= form_input(array('type'        => 'number',
				                     'name'        => 'year',
				                     'style'       => 'width:100px',
				                     'class'       => 'form-control',
				                     'placeholder' => 'Année',
				                     'value'       => (new DateTime())->format('Y')
				)) ?>
			</div>

			<div class="form-group form-inline">
				<label for="">Nature</label>

				<select name="code_nature" class="form-control">
					<?php
					foreach ($code_natures as $nature)
					{
						echo "<option value='$nature->Code-$nature->Description'>$nature->Code_Nature - $nature->Description </option>";
					}
					?>
				</select>
			</div>

			<?= form_submit(array('name'  => 'submit',
			                      'value' => 'Générer',
			                      'class' => 'btn btn-primary'
			)) ?>
			<?= form_close() ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<h4>Liste Détaillée des Achats</h4>

			<?= form_open('etats_achats/generate_report_3') ?>

			<div class="form-group form-inline">
				<label for="">Année</label>
				<?= form_input(array('type'        => 'number',
				                     'name'        => 'year',
				                     'style'       => 'width:100px',
				                     'class'       => 'form-control',
				                     'placeholder' => 'Année',
				                     'value'       => (new DateTime())->format("Y")
				)) ?>
			</div>

			<div class="form-group form-inline">
				<label for="">Nature</label>

				<select name="code_nature" class="form-control">
					<?php
					foreach ($code_natures as $nature)
					{
						echo "<option value='$nature->Code-$nature->Description'>$nature->Code_Nature - $nature->Description </option>";
					}
					?>
				</select>
			</div>

			<?= form_submit(array('name'  => 'submit',
			                      'value' => 'Générer',
			                      'class' => 'btn btn-primary'
			)) ?>
			<?= form_close() ?>
		</div>
	</div>
</div>
<!-- /First Section one Column -->