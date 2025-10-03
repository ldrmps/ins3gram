<div class="row">
    <div class="col">
        <h1>Ma messagerie</h1>
    </div>
</div>
<div class="row">
    <!--START: HISTORIQUE -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="">
                    <select name="receiver" id="receiver" class="form-select">
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!--END: HISTORIQUE -->
    <!--START: ZONE MESSAGE -->
    <div class="col">
        <div class="card" id="zone-message">
            <div class="card-header">
            </div>
            <div class="card-body">

            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-9">
                        <textarea name="message" id="message" class="form-control"></textarea>
                    </div>
                    <div class="col d-grid align-items-center">
                        <span class="btn btn-primary" id="send-message">Envoyer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--END: ZONE MESSAGE -->
</div>
<script>
    $(document).ready(function() {
        const base_url = "<?= base_url() ?>";
        var receiver = null;
        //Ajout du SELECT2 à notre select destinataire (receiver)
        initAjaxSelect2("#receiver",{
            url: base_url + 'api/user/all',
            placeholder: "Choisir un destinataire",
            searchFields: ['username'],
            delay : 250
        });
        //Événement au choix d'un destinataire
        $('#receiver').on('select2:select', function(e){
            var data = e.params.data;
            reciver = data.id;
            $('#zone-message .card-header').html(data.text);
        });
        //Événement au clic de l'envoi du message
        $('#send-message').on('click', function(){
            console.log(receiver);
            console.log($('#message').value);
        });
    });
</script>