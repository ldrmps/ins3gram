<div class="row">
    <div class="col">
        <h1>Ma messagerie</h1>
    </div>
</div>
<div style="height: 80vh !important;">
    <div class="row h-100">
        <!--START: HISTORIQUE -->
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body overflow-auto">
                    <div class="">
                        <select name="receiver" id="receiver" class="form-select">
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!--END: HISTORIQUE -->
        <!--START: ZONE MESSAGE -->
        <div class="col h-100">
            <div class="card h-100" id="zone-message">
                <div class="card-header">
                </div>
                <div class="card-body overflow-auto">
                    <div class="row" id="messages">

                    </div>
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
</div>
<script>
    $(document).ready(function() {
        const base_url = "<?= base_url() ?>";
        var sender = <?= $session_user->id; ?>;
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
            // console.log(data);
            receiver = data.id;
            $('#zone-message .card-header').html(data.text);
            $.ajax({
                'type': 'GET',
                'url' : base_url + 'messagerie/conversation',
                'data' : {
                    'id_1' : sender,
                    'id_2' : receiver,
                },
                'success' : function(data){
                    $('#messages').empty();
                    for(var i = 0; i < data.length; i++) {
                        var color ='';
                        var offset ='';
                        if(data[i].id_sender == sender) {
                            color = 'primary';
                            offset = 'offset-md-5';
                        } else {
                            color = 'success';
                            offset = '';
                        }
                        var msg = `
                            <div class="col-md-7 ${offset}">
                                <div class="alert alert-${color}">
                                    ${data[i].content}
                                </div>
                            </div>
                        `;
                        $('#messages').append(msg);
                    }
                },
                'error' : function(data){
                    console.log(data);
                }
            });
        });
        //Événement au clic de l'envoi du message
        $('#send-message').on('click', function(){
            var message = $('#message').val();
            // console.log("sender : " + sender);
            // console.log("receiver : " + receiver);
            // console.log("message : " + message);
            $.ajax({
                'type': 'POST',
                'url' : base_url + 'messagerie/send',
                'data' : {
                    id_sender : sender,
                    id_receiver : receiver,
                    content : message
                },
                'success' : function(data){
                    console.log(data);
                    if(data.success) {
                        var msg = `
                            <div class="col-md-7 offset-md-5">
                                <div class="alert alert-primary">
                                    ${data.data.content}
                                </div>
                            </div>
                        `;
                        $('#messages').append(msg);
                        $('#message').val('');
                    }
                },
                'error' : function(data){
                    console.log(data);
                }
            })
        });
    });
</script>
<style>

</style>