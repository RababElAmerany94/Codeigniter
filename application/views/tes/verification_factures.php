<style>
    .alert-notfound {
        background: #337ab7;
        color: white;
        width: fit-content;
    }
</style>
<div class="row">
    <div class="x_panel">
        <div class="col-xs-12">
            <?= form_open('Verification_factures/generate_report', array('id' => "form_facture")) ?>
            <h4> Vérification de Factures</h4>
            <div class="form-group form-inline">
                <label>Num Facture : </label>
                <input required name="num_facture" type="number" style=" width: 100px;" class="form-control">
            </div>
            <div class="form-group form-inline">
                <label>Année: </label>
                <?= form_input(array('type' => 'number', 'name' => 'year', 'min' => '1900', 'max' => '2099', 'class' => 'form-control', 'value' => (new DateTime())->format('Y'))) ?>
                <?= form_submit(array('name' => 'button', 'value' => 'Générer', 'class' => 'btn btn-primary btn-submit', 'style' => 'margin-bottom:0')) ?>
            </div>
            <p class="loading" style="display: none;">Recherche en cours...</p>
            <p class="alert alert-primary alert-notfound" style="display: none;"></p>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    window.onload = function () {
        $('#form_facture .btn-submit').click(function (e) {
            e.preventDefault();
            $.ajax({
                beforeSend: function () {
                    $(".loading").show();
                    $(".alert-notfound").hide();
                },
                type: 'POST',
                data: $("#form_facture").serialize(),
                url: "Verification_factures/check_record",
                success: function (response) {
                    $(".loading").hide();
                    $(".alert-notfound").hide();

                    if (!response.success) {
                        $(".alert-notfound").show();
                        $num_facture = $("input[name='num_facture']").val();
                        $year = $("input[name='year']").val();
                        $(".alert-notfound").html(`Facture ${$num_facture}/${$year} introuvable.`)
                    } else {
                        $('#form_facture').submit();
                    }
                }
            });
        });
    }
</script>