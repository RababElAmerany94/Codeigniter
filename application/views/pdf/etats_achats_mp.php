<?php
$montant_sum = 0;
$total_year_1 = [];
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?= $title_1 ?></title>
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
            <h2 class="title"><?= $title_1 ?> </h2>
            <br><br>
        </header>
        <table border="1">
            <tr>
                <th>ARTICLE</th>
                <th><?= $year - 3 ?></th>
                <th><?= $year - 2 ?></th>
                <th><?= $year - 1 ?></th>
                <th><?= $year ?></th>
            </tr>
            <?php foreach($articles as $article) { ?>
            <tr>
                <td><?= $article->description ?></td>
                <?php for ($i = $year-3 ; $i<=$year; $i++) { ?>
                    <td class="number">
                        <?php
                        
                            if(!array_key_exists($i, $total_year_1)) {
                                $total_year_1[$i] = 0;
                            }
                            $filtered_array = array_filter($result_1 ,function($var)  use($i,$article){
                                return $var->year == $i && $var->Description == $article->description;
                            });
                            if(!empty($filtered_array)) {
                                if( $etats == 'dh') {
                                    echo   number_format(array_values($filtered_array)[0]->montant,2,","," ");
                                    $total_year_1[$i] += array_values($filtered_array)[0]->montant;
                                } else {
                                    echo   number_format(array_values($filtered_array)[0]->poids,2,","," ");
                                    $total_year_1[$i] += array_values($filtered_array)[0]->poids;
                                }
                            } else {
                                echo '--';
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
                <th class="number"><?=  number_format($total_year_1[$year - 3],2,","," "); ?></th>
                <th class="number"><?=  number_format($total_year_1[$year - 2],2,","," "); ?></th>
                <th class="number"><?=  number_format($total_year_1[$year - 1],2,","," "); ?></th>
                <th class="number"><?=  number_format($total_year_1[$year],2,","," "); ?></th>
            </tr>
        </table>
<?php
$montant_sum = 0;
$total_year_1 = [];
?>
            <br>
            <br>
            <h2 class="title"><?= $title_2 ?> </h2>
            <br><br><br>
        <table border="1">
            <tr>
                <th>ARTICLE</th>
                <th><?= $year - 3 ?></th>
                <th><?= $year - 2?></th>
                <th><?= $year - 1?></th>
                <th><?= $year ?></th>
            </tr>
            <?php foreach($articles as $article) { ?>
            <tr>
                <td><?= $article->description ?></td>
                <?php for ($i = $year-3 ; $i<=$year; $i++) { ?>
                    <td class="number">
                        <?php
                            if(!array_key_exists($i, $total_year_1)) {
                                $total_year_1[$i] = 0;
                            }
                            $filtered_array = array_filter($result_2 ,function($var)  use($i,$article){
                                return $var->year == $i && $var->Description == $article->description;
                            });
                            if(!empty($filtered_array)) {
                                if( $etats == 'dh') {
                                    echo   number_format(array_values($filtered_array)[0]->montant,2,","," ");
                                    $total_year_1[$i] += array_values($filtered_array)[0]->montant;
                                } else {
                                    echo   number_format(array_values($filtered_array)[0]->poids,2,","," ");
                                    $total_year_1[$i] += array_values($filtered_array)[0]->poids;
                                }
                            } else {
                                echo '--';
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
                <th>MOYENNE</th>
                <th class="number"><?=  number_format(${"avg_" . 4},2,","," "); ?></th>
                <th class="number"><?=  number_format(${"avg_" . 3},2,","," "); ?></th>
                <th class="number"><?=  number_format(${"avg_" . 2},2,","," "); ?></th>
                <th class="number"><?=  number_format(${"avg_" . 1},2,","," "); ?></th>
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
