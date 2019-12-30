
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
        <link rel="stylesheet" href="assets/css/paper.min.css"/>
        <link rel="stylesheet" href="assets/css/print.css"/>
    </head>
    <body>
        <header>
            <div class="row">
                <div class="row_left">
                    <?= $company_name ?>
                </div>
                <div class="row_right">
                    <?= date('d/m/Y')?>
                </div>
            </div>

	        <br><br>

            <h2 class="title"><?= $title ?></h2>

            <br><br><br>

        </header>

        <table border="1">
            <tr>
                <th>MOIS</th>
                <th><?= $year ?></th>
            </tr>
            <?php for ($i = 1; $i <= 12; $i++) { ?>
            <tr>
                <?php 
                    $month = str_pad($i, 2, "0", STR_PAD_LEFT);;
                ?>
                <td><?= $i ?></td>
                <td class="number">
                    <?php
                       $filtered_array = array_filter($result ,function($var)  use($year,$month, $etats){
                            if($etats == 'dh') {
                                return $var->Mois == "$year$month";
                            } else {
                                return $var->Mois == "$month/$year";
                            }
                            
                        });
                    
                        if(!empty( $filtered_array )) {
                          if($etats == 'dh') {
                            echo  number_format(array_values($filtered_array)[0]->Montant,2,","," ");;
                          } else {
                            echo  number_format(array_values($filtered_array)[0]->Quantite,2,","," ");;
                          }
                        } else {
                            echo "--";
                        }
                    ?>
                </td>
            </tr>
            <?php
            }
            ?>
            <tr>
                <th>TOTAL</th>
                <th class="number">
                <?php  if($etats == 'kg') {
                   echo  number_format($total_quantity ,2,","," ");
                } else {
                  echo  number_format($total_amount,2,","," ");
                } ?>
                </th>
            </tr>
            <tr>
                <th>VALEUR / POIDS</th>
                <th class="number"><?= number_format($total_amount / max($total_quantity,1) ,2,","," "); ?></th>
            </tr>
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
