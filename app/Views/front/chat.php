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
        var page;
        var max_page;
        var last_message_date;
        //Ajout du SELECT2 à notre select destinataire (receiver)
        initAjaxSelect2("#receiver",{
            url: base_url + 'api/user/all',
            placeholder: "Choisir un destinataire",
            searchFields: ['username'],
            delay : 250
        });
        //Événement au choix d'un destinataire
        $('#receiver').on('select2:select', function(e){
            page = 1;
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
                    'page' : page
                },
                'success' : function(data_full){
                    var data = data_full.data;
                    max_page = data_full.max_page;
                    $('#messages').empty();
                    last_message_date = data[0].created_at;

                    for(var i = 0; i < data.length; i++) {
                        var color ='success';
                        var offset ='';
                        if(data[i].id_sender == sender) {
                            color = 'primary';
                            offset = 'offset-5';
                        }
                        addMessage(data[i].content,data[i].created_at, color, offset);
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
                    // console.log(data);
                    if(data.success) {
                        addMessage(data.data.content);
                        $('#message').val('');
                    }
                },
                'error' : function(data){
                    console.log(data);
                }
            })
        });
        //Événement au scroll de la zone de message
        $('#zone-message .card-body').on('scroll', function() {
            if($(this).scrollTop() == 0) {
                page++;
                if(page <= max_page) {
                    $.ajax({
                        'type': 'GET',
                        'url' : base_url + 'messagerie/conversation',
                        'data' : {
                            'id_1' : sender,
                            'id_2' : receiver,
                            'page' : page
                        },
                        'success' : function(data_full){
                            var data = data_full.data;
                            for(var i = 0; i < data.length; i++) {
                                var color ='success';
                                var offset ='';
                                if(data[i].id_sender == sender) {
                                    color = 'primary';
                                    offset = 'offset-5';
                                }
                                addMessage(data[i].content,data[i].created_at, color, offset, false);
                                var $container = $('#zone-message .card-body');
                                $container.scrollTop(150);
                            }
                        },
                        'error' : function(data){
                            console.log(data);
                        }
                    });
                } else {
                    $('#messages').prepend('<div class="col-md-12"><div class="alert alert-info">Fin de la conversation</div></div>');
                }
            }
        });
        //Execute à un interval régulier
        setInterval(checkNewMessage, 3000);

        function checkNewMessage() {
            console.log(last_message_date);
            $.ajax({
                'type': 'GET',
                'url' : base_url + 'messagerie/new-messages',
                'data' : {
                    'id_1' : sender,
                    'id_2' : receiver,
                    'date' : last_message_date
                },
                'success' : function(data){
                    console.log(data);
                },
                'error' : function(data){
                    console.log(data);
                }
            })
        }
        /**
         * Adds a message element to the DOM, appending it to the designated message container.
         *
         * @param {string} message - The text message to display.
         * @param {string} [color='primary'] - The Bootstrap alert color class to use for styling.
         * @param {string} [offset='offset-md-5'] - The optional offset class for positioning the message container.
         * @return {void} Does not return a value.
         */
        function addMessage(message,date, color = 'primary', offset = 'offset-5', scroll = true){
            var msg = `
                <div class="col-7 ${offset}">
                    <div class="alert alert-${color}">
                        ${message}
                    </div>
                    <span class="text-muted">${date}</span>
                </div>
            `;
            $('#messages').prepend(msg);
            if(!scroll) return;
            var $container = $('#zone-message .card-body');
            $container.scrollTop($container[0].scrollHeight);
        }
    });
</script>
<style>

</style>