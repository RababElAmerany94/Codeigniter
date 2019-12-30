<?php 

if($extra['state'] == 'add' || $extra['state'] == 'edit') {
    ?>
    <style>
        .col-btn {
            float:left;
            margin-top: 83px;
        }
        .col-btn button {
            display:block;
        }
        select[multiple], select[size] {
            height: 200px !important;
        }
        .row {
            margin-top : 20px;
        }
        button.submit {
            padding: 10px 20px;
        }
        option:disabled{
            color: #687d90;
            cursor: not-allowed;
        }
        .alert-success {
            margin:20px 0;
            width:80%;
            display:none;
        }
    </style>
    <form>
        <div class="row">
            <div class="col-md-3">
                <h5>Factures</h5>
                <select id="factures_data"  class="form-control" multiple="multiple">
                    <?php foreach($extra['factures'] as $fact)  {?>
                        <option data-num="<?= $fact['Num_Facture'] ?>" 
                                data-year="<?=  date('Y', strtotime($fact['Date_Facture']))  ?>" 
                                data-id="<?= $fact['Id_Facture'] ?>"
                                value="<?php echo $fact['Id_Facture'] . '-' . date('Y', strtotime($fact['Date_Facture']))  ?>"> 
                                <?php echo $fact['Num_Facture'] .' / ' .  date('Y', strtotime($fact['Date_Facture'])) ?> 
                        </option>
                    <?php  } ?>
                </select>
            </div>
            <div class="col-btn">
                    <button type="button"  onclick="add('factures',true)" >>></button>
                    <button type="button"  onclick="remove('factures')"><<</button>
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
                <select  id="virements_versements_select" name="virements_versements_select" class="form-control" multiple="multiple"></select>
            </div>
        </div>

    </form>

    <?php if($extra['state'] == 'add')  {?> 
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
            if($extra['state'] == 'edit'){ ?>
            let cheques_selected = <?=  json_encode($extra['cheques_selected'],true); ?> 
            let effets_selected = <?=  json_encode($extra['effets_selected'],true); ?> 
            let virement_selected = <?=  json_encode($extra['virement_selected'],true); ?> 
            let facture_selected = [];
            let date_created = <?=  json_encode($extra['date_created'],true); ?> ;
        
            window.onload = function(){
                cheques_selected.forEach(function(e) {
                        if(!facture_selected.includes(e.Id_Facture)) {
                            facture_selected.push({id : e.Id_Facture , year : e.year});
                        }
                        $('#cheques_select').append('<option value="'+e.Id_Cheque+'" data-facture="'+e.Id_Facture+"-"+e.year+'" >'+e.Num_Cheque+'</option>');
                })

                effets_selected.forEach(function(e) {
                        if(!facture_selected.includes(e.Id_Facture)) {
                            facture_selected.push({id : e.Id_Facture , year : e.year});
                        }
                        $('#effets_select').append('<option value="'+e.Id_Effet+'" data-facture="'+e.Id_Facture+"-"+e.year+'" >'+e.Num_Effet+'</option>');
                })

                 virement_selected.forEach(function(e) {
                        if(!facture_selected.includes(e.Id_Facture)) {
                            facture_selected.push({id : e.Id_Facture , year : e.year});
                        }
                        $('#virements_versements_select').append('<option value="'+e.Id_Operation+'" data-facture="'+e.Id_Facture+"-"+e.year+'" >'+e.Num_Operation+'</option>');
                })
                
                facture_selected.forEach(function(e) {
                    let $facture_option = $("#factures_data option[value="+e.id+"-"+e.year+"]");
                    if($facture_option){
                        $facture_option.prop('hidden',true);
                        var id_facture = $facture_option.data('id');
                        var num_facture = $facture_option.data('num');
                        var year_facture = $facture_option.data('year');
                        console.log(e);
                        console.log(`${num_facture} / ${year_facture}`);
                        $('#factures_select').append(`<option value="${id_facture}-${year_facture}">${num_facture} / ${year_facture}</option>`);
                        
                    }
                })
            }

        <?php } ?>

         function add(target,fetch = false) {

            $('#'+target+'_data option:selected').prop('hidden',true);
            var $data =  $('#'+target+'_data option:selected');
            $('#'+target+'_data option:selected').prop("selected", false);

            if($data.length) {
                Array.from($data).forEach(function(e) {
                    if(target == 'factures') {
                        var id_facture = $(e).data('id');
                        var num_facture = $(e).data('num');
                        var year_facture = $(e).data('year');
                        $('#factures_select').append(`<option value="${id_facture}-${year_facture}">${num_facture} / ${year_facture}</option>`);
                    } else {
                        var id = $(e).data('id');
                        var num = $(e).data('num');
                        var data_facture = $(e).data('facture');
                        $(`#${target}_select`).append(`<option data-facture="${data_facture}" value="${id}"> ${num} </option>`);
                    }
                });

                if(fetch) {
                    console.log("sdds");
                    fetch_data();
                }
            }

        }

        function remove(target) {
         $data = $('#'+target+'_select option:selected');

           if($data.length) {
                Array.from($data).forEach(function(e) {
                    if(target == 'factures') {
                        $("#factures_data option[value='"+$(e).val()+"']").removeAttr('hidden');
                        //remove all related cheques/effets/virement
                        $("select[name*='select'] option[data-facture='"+$(e).val()+"']").remove()
                        $(e).remove();
                    } else {
                        $("#"+target+"_data option[data-id='"+$(e).val()+"']").removeAttr('hidden');
                        $(e).remove();
                    }
                    // If target is factures remove all related cheques/effet...
                    if(target =='factures') {
                        $('option[data-facture="'+e+'"]').remove();
                    }
                }) 
           }
        }


        function sendData() {
            $(".alert-success").hide();
            var formData = new FormData();
            formData.append("effets", $.map($('#effets_select option'), function(e) { return e.value; }));
            formData.append("cheques", $.map($('#cheques_select option'), function(e) { return e.value; }));
            formData.append("virements", $.map($('#virements_versements_select option'), function(e) { return e.value; }));
            formData.append("factures", $.map($('#factures_select option'), function(e) { return e.value; }));
            
            let $montant_total = 0;
               $montant_total +=   $.map($('#virements_versements_select option'), function(e) {  return $(e).data('montant') }).reduce(function(a,b){  return parseFloat(a) + parseFloat (b) },0);
               $montant_total +=   $.map($('#cheques_select option'), function(e) {  return $(e).data('montant') }).reduce(function(a,b){  return parseFloat(a) + parseFloat (b) },0);
               $montant_total +=   $.map($('#effets_select option'), function(e) {  return $(e).data('montant') }).reduce(function(a,b){  return parseFloat(a) + parseFloat (b) },0);
            formData.append("montant",$montant_total);


            <?php if($extra['state'] == 'edit') { ?>
                formData.append("date_created" , date_created);
            <?php } ?>


            var $url = "<?php echo $extra['base_url']?>Reglement_mixte/index/insert";
            $.ajax({
                type: "POST",
                url: $url,
                data : formData,
                processData: false,
                contentType: false,
                dataType: "json" ,
                success :function () {
                    window.location.href ="<?php echo $extra['base_url']?>Reglement_mixte/index";
                    $(".alert-success ").show();
                    //remove all existing values
                    $('#factures_select').find('option').remove();
                    $('#cheques_select').find('option').remove();
                    $('#effets_select').find('option').remove();
                    $('#virements_versements_select').find('option').remove();
                        
                }
            });
        }

        function fetch_data() {
               
                var formData = new FormData();
                var factures = $.map($('#factures_select option'), function(e) { return  {id_facture : e.value.split('-')[0], year :e.value.split('-')[1] } });
                factures.forEach((f)=> {
                    formData.append("factures[]",JSON.stringify(f));
                })
                
                var $url = "<?php echo $extra['base_url']?>Reglement_mixte/get_data";
                $.ajax({
                    type: "POST",
                    url: $url,
                    data : formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success :  function (response) {
                        response.effets.forEach(function(e) { 
                            if($("#effets_select option[value='"+e.Id_Effet+"']").length == 0) {
                                $('#effets_select').append('<option value="'+e.Id_Effet+`" data-facture="${e.Id_Facture}-${e.year}" data-montant="${e.Montant}" disabled>`+e.Num_Effet+'</option>'); }
                            } 
                        );
                        response.cheques.forEach(function(e) {  
                            if($("#cheques_select option[value='"+e.Id_Cheque+"']").length == 0) { 
                            $('#cheques_select').append('<option value="'+e.Id_Cheque+`" data-facture="${e.Id_Facture}-${e.year}" data-montant="${e.Montant_Cheque}" disabled>`+e.Num_Cheque+'</option>'); } 
                           }
                        );
                        response.virements.forEach(function(e) {   
                            if($("#virements_versements_select option[value='"+e.Id_Operation+"']").length == 0) {
                            $('#virements_versements_select').append('<option value="'+e.Id_Operation+`" data-facture="${e.Id_Facture}-${e.year}" data-montant="${e.Montant}" disabled>`+e.Num_Operation+'</option>'); } 
                           }
                        );
                    }
                });
        }
    </script>

    <?php
} 

else if ($extra['state'] == 'read') {?>
    <div class="panel panel-primary">
        <div class="panel-header">
            <h3>Date Règlement</h3>
        </div>
        <div class="panel-body">
           <?php 
                echo "<h5>".$extra['reglements']['date']."</h5>";
           ?>
        </div>
    </div>
    <div class="panel panel-primary">
        <div class="panel-header">
            <h3>Factures</h3>
        </div>
        <div class="panel-body">
           <?php 
                echo "<h5>".$extra['reglements']['factures']."</h5>";
           ?>
        </div>
        </div>
    <?php if(!empty($extra['reglements']['effets'])) { ?>
    <div class="panel panel-primary">
        <div class="panel-header">
            <h3>Effets</h3>
        </div>
        <div class="panel-body">
           <?php 
                echo "<h5>".$extra['reglements']['effets']."</h5>";
           ?>
        </div>
        </div>
        <?php } ?>
    <?php if(!empty($extra['reglements']['cheques'])) { ?>
    <div class="panel panel-primary">
        <div class="panel-header">
            <h3>Cheques</h3>
        </div>
        <div class="panel-body">
           <?php 
                echo "<h5>".$extra['reglements']['cheques']."</h5>";
           ?>
        </div>
        <?php } ?>
    <<?php if(!empty($extra['reglements']['virements'])) { ?>/div>
    <div class="panel panel-primary">
        <div class="panel-header">
            <h3>Virement/Versement</h3>
        </div>
        <div class="panel-body">
           <?php 
                echo "<h5>".$extra['reglements']['virements']."</h5>";
           ?>
        </div>
    <?php } ?>
    </div>

 <?php   
}
else {
    ?>
    <style>
        .flexigrid div.bDiv td{
            white-space: normal !important; 
        }
    </style>
    <?php
    echo $output;
}

?>