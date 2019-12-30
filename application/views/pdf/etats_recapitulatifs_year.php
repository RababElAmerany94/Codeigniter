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

            <br><br>

        </header>
        <table border="1">
            <tr>
                <td></td>
                <th>EN DH H.T.</th>
            </tr>
            <tr>
                <td>VENTES DES BIENS</td>
                <td><?= number_format(isset($data['P_Ventes']) ? $data['P_Ventes'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>MARCHANDISES</td>
                <td><?= number_format(isset($data['T_Ventes']) ? $data['T_Ventes'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>FACONS</td>
                <td><?= number_format(isset($data['M_Ventes']) ? $data['M_Ventes'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <th>TOTAL 1</th>
                <th><?= number_format($data['totals']['ventes'], 2, ",", " ") ?></th>
            </tr>

            <tr>
                <td>MATIERE PREMIERE</td>
                <td><?= number_format(isset($data['MP_Achats']) ? $data['MP_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>MARCHANDISES</td>
                <td><?= number_format(isset($data['MS_Achats']) ? $data['MS_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>PRODUIT FINNISSAGE</td>
                <td><?= number_format(isset($data['PF_Achats']) ? $data['PF_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>EMBALLAGES</td>
                <td><?= number_format(isset($data['EM_Achats']) ? $data['EM_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>PIECES RECHANGES</td>
                <td><?= number_format(isset($data['PR_Achats']) ? $data['PR_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>ELECTRICITE</td>
                <td><?= number_format(isset($data['EL_Achats']) ? $data['EL_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>EAUX</td>
                <td><?= number_format(isset($data['EA_Achats']) ? $data['EA_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>FUEL</td>
                <td><?= number_format(isset($data['FU_Achats']) ? $data['FU_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>GAZ</td>
                <td><?= number_format(isset($data['BU_Achats']) ? $data['BU_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>SALAIRES</td>
                <td><?= number_format(isset($data['SA_Achats']) ? $data['SA_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>SERVICES</td>
                <td><?= number_format(isset($data['SR_Achats']) ? $data['SR_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>AMORTISSEMENT</td>
                <td><?= number_format(isset($data['AM_Achats']) ? $data['AM_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>IMPOTS</td>
                <td><?= number_format(isset($data['PT_Achats']) ? $data['PT_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>DIVERS</td>
                <td><?= number_format(isset($data['DV_Achats']) ? $data['DV_Achats'] : 0, 2, ",", " ") ?></td>
            </tr>
            <tr>
                <th>TOTAL 2</th>
                <th><?= number_format($data['totals']['achats'], 2, ",", " ") ?></th>
            </tr>
            <tr>
                <th>TOTAL 1 - TOTAL 2</th>
                <th><?= number_format($data['totals']['ventes'] - $data['totals']['achats'], 2, ",", " ") ?></th>
            </tr>
        </table>

        <script type="text/php">
            if (isset($pdf)) {
                $x = $pdf->get_width() - 85;
                $y = $pdf->get_height() - 35;
                $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
                $font = null;
                $size = 10;
                $color = array(0,0,0);
                $word_space = 0.0;  //  default
                $char_space = 0.0;  //  default
                $angle = 0.0;       //  default
                $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
            }
        </script>
    </body>
</html>
