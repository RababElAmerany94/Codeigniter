<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title><?= $title ?></title>
		<link rel="stylesheet" href="assets/css/print.css"/>
	</head>

	<body>
		<header>
			<div class="row">
				<div class="row_left"><?= $company_name ?></div>

				<div class="row_right"><?= date('d/m/Y') ?></div>
			</div>

			<div class="row">
				<h2 class="title"><?= $title ?></h2>
			</div>
		</header>

		<br><br>

		<table border="1">
			<tr>
				<th rowspan="2">MOIS</th>
				<th colspan="4">VENTES EN DH H.T.</th>
			</tr>

			<tr>
				<th>VENTES DES BIENS</th>
				<th>FACONS</th>
				<th>MARCHANDISES</th>
				<th>TOTAL</th>
			</tr>
		    <?php foreach ($table1 as $mois => $row) {
		        $totals['ventes'][$mois] += $row['P_Ventes'] + $row['T_Ventes'] + $row['M_Ventes'];
		        ?>
			<tr>
				<td><?= $mois ?></td>
				<td class="number"><?= number_format($row['P_Ventes'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['T_Ventes'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['M_Ventes'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($totals['ventes'][$mois], 2, ",", " ") ?></td>
			</tr>
		    <?php } ?>
		</table>

		<br><br>

		<table border="1">
			<tr>
				<th rowspan="2">MOIS</th>
				<th colspan="7">ACHATS EN DH H.T.</th>
			</tr>

			<tr>
				<th>MATIERE<br>PREMIERE</th>
				<th>MARCHANDISES</th>
				<th>PRODUIT<br>FINNISSAGE</th>
				<th>EMBALLAGES</th>
				<th>PIECES<br>RECHANGES</th>
				<th>ELECTRICITE</th>
				<th>EAUX</th>
			</tr>
		    <?php foreach ($table2 as $mois => $row) {
		        $totals['achats'][$mois] += $row['MP_Achats'] + $row['MS_Achats'] + $row['PF_Achats'] + $row['EM_Achats'] + $row['PR_Achats'] + $row['EL_Achats'] + $row['EA_Achats'];
		        ?>
			<tr>
				<td><?= $mois ?></td>
				<td class="number"><?= number_format($row['MP_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['MS_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['PF_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['EM_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['PR_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['EL_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['EA_Achats'], 2, ",", " ") ?></td>
			</tr>
		    <?php } ?>
		</table>

		<div style="page-break-after:always;"></div>

		<table border="1">
			<tr>
				<th rowspan="2">MOIS</th>
				<th colspan="7">ACHATS EN DH H.T.</th>
			</tr>

			<tr>
				<th>FUEL</th>
				<th>GAZ</th>
				<th>SALAIRES</th>
				<th>SERVICES</th>
				<th>AMORTISSEM<br>ENT</th>
				<th>IMPOTS</th>
				<th>DIVERS</th>
			</tr>
		    <?php foreach ($table3 as $mois => $row) {
		        $totals['achats'][$mois] += $row['FU_Achats'] + $row['BU_Achats'] + $row['SA_Achats'] + $row['SR_Achats'] + $row['AM_Achats'] + $row['PT_Achats'] + $row['DV_Achats'];
		        ?>
			<tr>
				<td><?= $mois ?></td>
				<td class="number"><?= number_format($row['FU_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['BU_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['SA_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['SR_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['AM_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['PT_Achats'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($row['DV_Achats'], 2, ",", " ") ?></td>
			</tr>
		    <?php } ?>
		</table>

		<br><br><br>

		<table border="1">
			<tr>
				<th>MOIS</th>
				<th>VENTES</th>
				<th>ACHATS</th>
				<th>SOLDE</th>
			</tr>
		    <?php foreach ($table3 as $mois => $row) { ?>
				<tr>
					<td class="number"><?= $mois ?></td>
					<td class="number"><?= number_format($totals['ventes'][$mois], 2, ",", " ") ?></td>
					<td class="number"><?= number_format($totals['achats'][$mois], 2, ",", " ") ?></td>
					<td class="number"><?= number_format($totals['ventes'][$mois] - $totals['achats'][$mois], 2, ",", " ") ?></td>
				</tr>
		    <?php } ?>
		</table>

		<script type="text/php">
            if (isset($pdf)) {
                $x = $pdf->get_width() - 85;
                $y = $pdf->get_height()-35;
                $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
                $font = null;
                $size = 10;
                $color = array(0,0,0);
                $word_space = 0.0;  //  default
                $char_space = 0.0;  //  default
                $angle = 0.0;   //  default
                $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
            }
		</script>
	</body>
</html>
