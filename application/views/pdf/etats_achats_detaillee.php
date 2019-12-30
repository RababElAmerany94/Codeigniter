<?php
$total_poids = 0;
$total_montant = 0;
$total_pu = 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title><?= $title ?></title>
	<link rel="stylesheet" href="assets/css/paper.min.css"/>
	<link rel="stylesheet" href="assets/css/print.css"/>
	<style>
		.table {
			width: 100% !important;
		}

		table .header {
			font-size: 9px;
		}

		.page_break {
			page-break-before: always;
		}

	</style>
</head>

<body>
<header>
	<div class="row">
		<div class="row_left">
			<?= $company_name ?>
		</div>
		<div class="row_right">
			<?= date('d/m/Y') ?>
		</div>
	</div>

	<br><br>

	<h2 class="title"><?= $title ?> POUR <?= $year ?></h2>

	<br><br><br>
</header>

<table border="1">
	<tr class="header">
		<th>N FACT/DUM</th>
		<th style="width: 100px">DATE FACTURE</th>
		<th>FOURNISSEUR</th>
		<th>MARCHANDISE</th>
		<th>POIDS</th>
		<th>MONTANT H.T</th>
		<th>PRIX UNITAIRE</th>
	</tr>
	<?php for ($i = 0; $i < count($result); $i ++) { ?>
		<tr>
			<td style="width: 100px"><?= $result[$i]['NUM_DUM_NUM_FACTURE'] ?></td>
			<td style="width: 100px"><?= (new DateTime($result[$i]['Date_Facture']))->format('d/m/Y') ?></td>
			<td><?= $result[$i]['RaisonSociale'] ?></td>
			<td><?= $result[$i]['Marchandises'] ?></td>
			<td class="number"><?= number_format($result[$i]['poids'], 2, ",", " ") ?></td>
			<td class="number"><?= number_format($result[$i]['montant'], 2, ",", " ") ?></td>
			<td class="number"><?= number_format($result[$i]['PU'], 2, ",", " ") ?></td>
		</tr>
		<?php
		$total_poids += $result[$i]['poids'];
		$total_montant += $result[$i]['montant'];
		$total_pu += $result[$i]['PU'];
		// $tva_sum += $result[$i]['TVA'];
		// $ttc_sum += $result[$i]['TTC'];
		// $nbr_fact++;
	}
	?>
	<tr>
		<th colspan=3></th>
		<th>TOTAL</th>
		<th class="number"><?= number_format($total_poids, 2, ",", " "); ?></th>
		<th class="number"><?= number_format($total_montant, 2, ",", " "); ?></th>
		<th class="number"><?= number_format($total_pu, 2, ",", " "); ?></th>
	</tr>
</table>

<h3 style="page-break-inside: avoid;"> NBR FACTURES : <span> <?= $nbr_facture ?> </span></h3>
<h3> NBR DUM : <span> <?= $nbr_DUM ?> </span></h3>

<table border="1" style="page-break-inside: avoid;">
	<tr class="header">
		<th></th>
		<th>DH H.T.</th>
		<th>KGS.</th>
		<th>PRIX/KG</th>
	</tr>
	<tr>
		<th>IMPORTATION</th>
		<td class="number"><?= number_format($importation['montant'], 2, ",", " ");; ?> </td>
		<td class="number"><?= number_format($importation['poids'], 2, ",", " "); ?> </td>
		<td class="number"><?= number_format($importation['PU'], 2, ",", " "); ?> </td>
	</tr>
	<tr>
		<th>MARCHE LOCAL</th>
		<td class="number"><?= number_format($marche_local['montant'], 2, ",", " ");; ?> </td>
		<td class="number"><?= number_format($marche_local['poids'], 2, ",", " "); ?> </td>
		<td class="number"><?= number_format($marche_local['PU'], 2, ",", " "); ?> </td>
	</tr>
	<tr>
		<th>TOTAL GENERAL</th>
		<td class="number"><?= number_format($marche_local['montant'] + $importation['montant'], 2, ",", " ");; ?> </td>
		<td class="number"><?= number_format($marche_local['poids'] + $importation['poids'], 2, ",", " ");; ?> </td>
		<td class="number"><?= number_format($marche_local['PU'] + $importation['PU'], 2, ",", " "); ?> </td>
	</tr>
</table>

<?php if (isset($result_2))
{
	$total_montant = 0;
	$total_pu = 0;
	$poids_percentage = 0;
	?>
	<table border="1" style="page-break-inside: avoid;">
		<tr class="header">
			<th>CODE</th>
			<th>MARCHENDISES</th>
			<th>VALEUR H.T</th>
			<th>KG</th>
			<th>%</th>
			<th>VALEUR/KG</th>
		</tr>
		<?php for ($i = 0; $i < count($result_2); $i ++) { ?>
			<tr>
				<td><?= $result_2[$i]['Code'] ?></td>
				<td><?= $result_2[$i]['Description'] ?></td>
				<td class="number"><?= number_format($result_2[$i]['montant'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($result_2[$i]['poids'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format(($result_2[$i]['poids'] * 100) / $totalPoids, 2, ",", " ") ?>%
				</td>
				<td class="number"><?= number_format($result_2[$i]['PU'], 2, ",", " ") ?></td>
			</tr>
			<?php
			$total_montant += $result_2[$i]['montant'];
			$total_pu += $result_2[$i]['PU'];
			$poids_percentage += ($result_2[$i]['poids'] * 100) / $totalPoids;
		}
		?>
		<tr>
			<th colspan=1></th>
			<th>TOTAL</th>
			<th><?= number_format($total_montant, 2, ",", " "); ?></th>
			<th><?= number_format($totalPoids, 2, ",", " "); ?></th>
			<th><?= number_format($poids_percentage, 2, ",", " "); ?> %</th>
			<th><?= number_format($total_pu, 2, ",", " "); ?></th>
		</tr>
	</table>
<?php } ?>

<?php if (isset($fibres))
{
	$total_montant = 0;
	$total_pu = 0;
	$poids_percentage = 0;
	?>
	<h3>FIBRES</h3>
	<table border="1">
		<tr class="header">
			<th>CODE</th>
			<th>MARCHENDISES</th>
			<th>VALEUR H.T</th>
			<th>KG</th>
			<th>%</th>
			<th>VALEUR/KG</th>
		</tr>
		<?php for ($i = 0; $i < count($fibres); $i ++) { ?>
			<tr>
				<td><?= $fibres[$i]['Code'] ?></td>
				<td><?= $fibres[$i]['Description'] ?></td>
				<td class="number"><?= number_format($fibres[$i]['montant'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($fibres[$i]['poids'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format(($fibres[$i]['poids'] * 100) / $totalPoids_fibres, 2, ",", " ") ?>
					%
				</td>
				<td class="number"><?= number_format($fibres[$i]['PU'], 2, ",", " ") ?></td>
			</tr>
			<?php
			$total_montant += $fibres[$i]['montant'];
			$total_pu += $fibres[$i]['PU'];
			$poids_percentage += ($fibres[$i]['poids'] * 100) / $totalPoids_fibres;
		}
		?>
		<tr>
			<th colspan=1></th>
			<th>TOTAL</th>
			<th class="number"><?= number_format($total_montant, 2, ",", " "); ?></th>
			<th class="number"><?= number_format($totalPoids_fibres, 2, ",", " "); ?></th>
			<th class="number"><?= number_format($poids_percentage, 2, ",", " "); ?> %</th>
			<th class="number"><?= number_format($total_pu, 2, ",", " "); ?></th>
		</tr>
	</table>
<?php } ?>

<?php if (isset($fils))
{
	$total_montant = 0;
	$total_pu = 0;
	$poids_percentage = 0;
	?>
	<h3>FILS</h3>
	<table border="1">
		<tr class="header">
			<th>CODE</th>
			<th>MARCHENDISES</th>
			<th>VALEUR H.T</th>
			<th>KG</th>
			<th>%</th>
			<th>VALEUR/KG</th>
		</tr>
		<?php for ($i = 0; $i < count($fils); $i ++) { ?>
			<tr>
				<td><?= $fils[$i]['Code'] ?></td>
				<td><?= $fils[$i]['Description'] ?></td>
				<td class="number"><?= number_format($fils[$i]['montant'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format($fils[$i]['poids'], 2, ",", " ") ?></td>
				<td class="number"><?= number_format(($fils[$i]['poids'] * 100) / $totalPoids_fils, 2, ",", " ") ?>%
				</td>
				<td class="number"><?= number_format($fils[$i]['PU'], 2, ",", " ") ?></td>
			</tr>
			<?php
			$total_montant += $fils[$i]['montant'];
			$total_pu += $fils[$i]['PU'];
			$poids_percentage += ($fils[$i]['poids'] * 100) / $totalPoids_fils;
		}
		?>
		<tr>
			<th colspan=1></th>
			<th>TOTAL</th>
			<th class="number"><?= number_format($total_montant, 2, ",", " "); ?></th>
			<th class="number"><?= number_format($totalPoids_fils, 2, ",", " "); ?></th>
			<th class="number"><?= number_format($poids_percentage, 2, ",", " "); ?> %</th>
			<th class="number"><?= number_format($total_pu, 2, ",", " "); ?></th>
		</tr>
	</table>
<?php } ?>

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
