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

                <div class="row_right"><?= date('d/m/Y')?></div>
            </div>

            <div class="row">
                <h2 class="title"><?= $title ?></h2>
            </div>

            <br><br>
        </header>
        <table border="1">
            <tr>
                <th></th>
                <th>QUANTITÉ</th>
            </tr>
            <tr>
                <td>TISSUS</td>
                <td><?= number_format($data['MT_Ventes'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>FILS</td>
                <td><?= number_format($data['MF_Ventes'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>DECHETS</td>
                <td><?= number_format($data['R_Ventes'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>MARCHANDISES</td>
                <td><?= number_format($data['F_Ventes'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>FACONNAGE</td>
                <td><?= number_format($data['T_Ventes'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <th>TOTAL 1</th>
                <td><?= number_format($data['totals']['ventes'], 2, ",", " ") ?></th>
            </tr>
            <tr>
                <td>MATIERE PREMIERE / KG</td>
                <td><?= number_format($data['MP_Achats'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>MARCHANDISES / KG</td>
                <td><?= number_format($data['MS_Achats'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>PRODUIT FINNISSAGE / KG</td>
                <td><?= number_format($data['PF_Achats'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>EMBALLAGES / KG</td>
                <td><?= number_format($data['EM_Achats'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>ELECTRICITE / KWH</td>
                <td><?= number_format($data['EL_Achats'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <td>EAUX / M<sub>3</sub></td>
                <td><?= number_format($data['EA_Achats'], 2, ",", " ") ?></td>
            </tr>
            <tr>
                <th>TOTAL 2</th>
                <td><?= number_format($data['totals']['achats'], 2, ",", " ") ?></th>
            </tr>
            <tr>
                <th>TOTAL 1 - TOTAL 2</th>
                <td><?= number_format($data['totals']['ventes'] - $data['totals']['achats'], 2, ",", " ") ?></th>
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
