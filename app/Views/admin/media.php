
<div class="row">
    <div class="col-md-3">
        <div class="card h-100" id="filtre">
            <div class="card-header">
                <span class="card-title h3">Filtres</span>
            </div>
            <div class="card-body">
                <?php
                $entity_types = ['user' => "Utilisateurs", 'recipe' => "Recettes", 'ingredient' => 'Ingrédients', 'brand' => "Marques"];
                ?>
                <select class="form-select" id="entity-filter" name="entity-filter" onchange="applyFilter(this.value)">
                    <option value="all">Toutes les images</option>
                    <?php foreach($entity_types as $entity_type => $entity_name) : ?>
                        <option value="<?= $entity_type; ?>"><?= $entity_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="card-footer">

            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card" id="liste">
            <div class="card-header">
                <span class="card-title h3">Liste des medias</span>
            </div>
            <div class="card-body">
                <div class="row row-cols-2 row-cols-md-6 g-1" id="medias">
                </div>
                <div class="row">
                    <div class="col text-center">
                        <span class="btn btn-outline-primary btn-sm" id="loadMore">Charger plus</span>
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
                for(var i = 0; i < data.length; i++) {
                    let entity_type = data[i].entity_type;
                    if (data[i].entity_type.indexOf('_') !== -1) {
                        entity_type = data[i].entity_type.substring(0, data[i].entity_type.indexOf('_'));
                    }
                    var img = `
                            <div class="col position-relative media">
                                <div class="position-absolute h-100 w-100 img-thumbnail hover-media" style="background-color: rgba(0,0,0,0.3); display: none;">
                                    <div class="d-flex flex-column justify-content-center align-items-center h-100">
                                    <a href="${base_url + '/admin/' + entity_type + '/' + data[i].entity_id}" class="text-white mb-3" data-bs-toggle="tooltip" data-bs-title="Editer l'original">
                                        <i class="fa fa-eye fa-2xl"></i>
                                    </a>
                                    <span class="delete-media" data-bs-toggle="tooltip" data-bs-title="Supprimer" data-id="${data[i].id}">
                                        <i class="fa fa-trash fa-2xl text-danger"></i>
                                    </span>
                                </div>
                                </div>
                                <img class="img-thumbnail" src="${base_url+data[i].file_path}" alt="...">
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
</style>