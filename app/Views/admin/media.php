<div class="row mb-3">
    <div class="col">
        <div class="card">
            <div class="card-header"><span class="card-title h3">Medias</span></div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col">
        <div class="card" id="filtre">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <?php
                    $entity_types = ['user' => "Utilisateurs", 'recipe' => "Recettes", 'ingredient' => 'Ingrédients', 'brand' => "Marques"];
                    ?>
                    <label for="entity-filter form-label me-3">Filtrer par</label>
                    <select class="form-select" id="entity-filter" name="entity-filter" onchange="applyFilter(this.value)">
                        <option value="all" selected>Aucun filtre</option>
                        <?php foreach($entity_types as $entity_type => $entity_name) : ?>
                            <option value="<?= $entity_type; ?>"><?= $entity_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card" id="liste">
            <div class="card-body">
                <div class="row row-cols-2 row-cols-md-6 g-3 mb-3" id="medias">
                </div>
                <div class="row">
                    <div class="col text-center">
                        <span class="btn btn-outline-primary btn-sm" id="loadMore"></span>
                    </div>
                </div>
            </div>
            <div class="card-footer">

            </div>
        </div>
    </div>
</div>
<script>
    var page = 1;
    var entity_type = null;
    $(document).ready(function() {
        load();
        $('#medias').on('mouseenter','.media', function() {
            $(this).find('.hover-media').show();
        });
        $('#medias').on('mouseleave','.media', function() {
            $(this).find('.hover-media').hide();
        });
        $('#loadMore').click(function () {
            page++;
            load();
        });
        $('#medias').on('click', '.delete-media', function() {

            Swal.fire({
                title: "Supprimer l'image ? ",
                text: "Il n'y aura pas de retour possible !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "var(--cui-primary)",
                cancelButtonColor: "var(--cui-danger)",
                confirmButtonText: "Oui, supprime !",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    let id = $(this).data('id');
                    const $media = $(this).closest('.media');
                    $.ajax({
                        url: base_url + '/admin/media/delete',
                        type: 'POST',
                        data : {
                            id : id
                        },
                        success : function(data) {
                            if (data.success) {
                                $media.remove();
                                Swal.fire({
                                    icon : 'success',
                                    title : 'Succès',
                                    text: data.message,
                                    timer: 1500,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Erreur",
                                    text: data.message,
                                    timer: 1500,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                });
                            }
                        },
                        error : function(xhr, status, error) {
                            console.log(error);
                        }
                    });
                }
            });
        });
    });

    function load() {
        $.ajax({
            url: base_url + '/admin/media/load',
            type : "GET",
            data : {
                page : page,
                entity_type: entity_type
            },
            success : function(data) {
                if (data[1] == page) {
                    $('#loadMore').text('Plus de medias à charger');
                    $('#loadMore').addClass('disabled');
                } else {
                    $('#loadMore').text('Charger plus');
                    $('#loadMore').removeClass('disabled');
                }
                data = data[0];
                for(var i = 0; i < data.length; i++) {
                    let entity_type = data[i].entity_type;
                    if (data[i].entity_type.indexOf('_') !== -1) {
                        entity_type = data[i].entity_type.substring(0, data[i].entity_type.indexOf('_'));
                    }
                    console.log(data[i]);
                    var img = `
                            <div class="col media">
                                <div class="position-relative">
                                    <div class="position-absolute img-thumbnail w-100 h-100 hover-media" style="background-color: rgba(0,0,0,0.3); display: none;">
                                        <div class="d-flex flex-column justify-content-center align-items-stretch h-100 p-2">
                                            <a href="${base_url+data[i].file_path}" data-lightbox="mainslider" class="btn btn-success text-light mb-2">
                                            <i class="fa fa-eye"></i> Afficher </a>
                                            <a href="${base_url + '/admin/' + entity_type + '/' + data[i].entity_id}" class="btn btn-primary text-light mb-2">
                                                <i class="fa fa-pencil"></i> Editer l'original
                                            </a>
                                            <span class="delete-media btn btn-danger text-light" data-id="${data[i].id}">
                                                <i class="fa fa-trash"></i> Supprimer
                                            </span>
                                        </div>
                                    </div>
                                    <img class="img-thumbnail" src="${base_url+data[i].file_path}" alt="...">
                                </div>
                            </div>
                        `;
                    $('#medias').append(img);
                }
            },
            error : function(xhr, status, error) {
                console.log(error);

            }
        })
    }

    function applyFilter(value) {
        page = 1;
        entity_type = value;
        if (value == 'all') {
            entity_type = null;
        }
        $('#medias').empty();
        load();
    }
</script>
<style>
    .delete-media {
        cursor: pointer;
    }

    #medias img {
        object-fit: cover;
        width: 100%;
        height : 200px;
        margin : 0 auto;
        display: block;
    }


</style>