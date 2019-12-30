<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
        <style>
            @page{size:A4}body{color:#000;background-color:#fff;font-family:Arial,sans-serif;font-size:12px}h1{font-size:2.4em;line-height:1.4em;font-weight:normal}table{width:100%;border-collapse:collapse;border-spacing:2px;margin-bottom:20px}th,td{text-align:center;border:solid 1px}th{padding:2px 10px;white-space:nowrap;font-weight:bold}td{padding:5px;text-align:center}footer{width:100%;padding:8px 0;text-align:center}.row{position:relative;width:100%}.row_left{display:inline-block;width:50%}.row_right{display:inline-block;width:50%;text-align:right;padding-right:5px}
        </style>
    </head>
    <body>
        <header>
            <div class="row">
                <div class="row_left" style="float: left;">
                    <?= $company_name ?>
                </div>
                <div class="row_right" style="float: right;">
                    <?= date('d/m/Y')?>
                </div>
            </div>
            <br>
            <br>
            <h2 class="title"><?= $title ?></h2>
            <br><br><br>
        </header>
        <table border="1">
            <tr>
                <th>Code</th>
                <th>Raison Sociale</th>
                <th>Ville</th>
                <th>Telephone 1</th>
                <th>IBAN 1</th>
            </tr>
            <?php for ($i = 0; $i < count($result); $i++) { ?>
            <tr>
                <td><?= $result[$i]['Id_Client'] ?></td>
                <td><?= $result[$i]['RaisonSociale'] ?></td>
                <td><?= $result[$i]['Ville'] ?></td>
                <td><?= $result[$i]['Telephone_1'] ?></td>
                <td><?= $result[$i]['IBAN_1'] ?></td>
            </tr>
            <?php } ?>
        </table>

        <div style="font-size:18px">Nombre de Clients: <strong><?= count($result) ?></strong></div>
    </body>
</html>
