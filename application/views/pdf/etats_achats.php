<?php
$montant_sum = 0;
$total_year_1 =0;
$total_year_2 =0;
$total_year_3 =0;
$total_year_4 =0;

$total_poids_1 =0;
$total_poids_2 =0;
$total_poids_3 =0;
$total_poids_4 =0;
?>
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
            <br>
            <br>
            <h2 class="title"><?= $title ?></h2>
            <br><br><br>
        </header>
        <table border="1">
            <tr>
                <th>MOIS</th>
                <th><?= $year - 3 ?></th>
                <th><?= $year - 2?></th>
                <th><?= $year - 1?></th>
                <th><?= $year ?></th>
            </tr>
            <?php for ($i = 1; $i <= 12; $i++) { ?>
            <tr>
                <?php 
                    $month = str_pad($i, 2, "0", STR_PAD_LEFT);;
                ?>
                <td><?= $i ?></td>
                <?php for ($j = 3; $j >=0; $j--) { ?>
                    <td class="number">
                    <?php
                        if($etats == 'kg') {
                            $filtered_array = array_filter(${"poids_" . ($j + 1)} ,function($var)  use($year,$month,$j){
                                return $var->Mois == "$month/". ($year-$j);
                            });
                            if(!empty( $filtered_array )) {
                                echo  number_format(array_values($filtered_array)[0]->Quantite,2,","," ");;
                                ${"total_year_" . ($j + 1)} +=  array_values($filtered_array)[0]->Quantite;
                                ${"total_poids_" . ($j + 1)} +=  array_values($filtered_array)[0]->Quantite;
                            } else {
                                echo "--";
                            }
                        } else {
                            $filtered_array = array_filter(${"montant_" . ($j + 1)} ,function($var)  use($year,$month,$j){
                                return $var->Mois == ($year-$j)."$month";
                            });
      
                            if(!empty( $filtered_array )) {
                                echo  number_format(array_values($filtered_array)[0]->Montant,2,","," ");;
                                ${"total_year_" . ($j + 1)} +=  array_values($filtered_array)[0]->Montant;
                                
                                $filtered_array = array_filter(${"poids_" . ($j + 1)} ,function($var)  use($year,$month,$j){
                                    return $var->Mois == "$month/". ($year-$j);
                                });
                                if(!empty( $filtered_array )) { 
                                    ${"total_poids_" . ($j + 1)} +=  array_values($filtered_array)[0]->Quantite;
                                }
                            } else {
                                echo "--";
                            }
                        }
                        
                    ?>
                </td>
                <?php }?>
                
            </tr>
            <?php
            $montant_sum += 0;
            }
            ?>
            <tr>
                <th>TOTAL</th>
                <th class="number"><?=  number_format($total_year_4,2,","," "); ?></th>
                <th class="number"><?=  number_format($total_year_3,2,","," "); ?></th>
                <th class="number"><?=  number_format($total_year_2,2,","," ");  ?></th>
                <th class="number"><?=   number_format($total_year_1,2,","," "); ?></th>
            </tr>
            <tr>
                <th>VALEUR / POIDS</th>
                <th class="number"><?=  number_format($montant_year_4 / max($total_poids_4,1),2,","," "); ?></th>
                <th class="number"><?= number_format($montant_year_3 / max($total_poids_3,1),2,","," "); ?></th>
                <th class="number"><?= number_format($montant_year_2 / max($total_poids_2,1),2,","," "); ?></th>
                <th class="number"><?= number_format($montant_year_1 / max($total_poids_1,1),2,","," "); ?></th>
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
