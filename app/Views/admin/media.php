
<div class="row">
    <div class="col-md-3">
        <div class="card h-100" id="filtre">
            <div class="card-header">
                <span class="card-title h3">Filtres</span>
            </div>
            <div class="card-body">
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
                    <?php foreach($medias as $media) : ?>
                        <div class="col position-relative media">
                            <div class="position-absolute h-100 w-100 img-thumbnail hover-media" style="background-color: rgba(0,0,0,0.3); display: none;">
                                Coucou
                            </div>
                            <img class="img-thumbnail" src="<?= $media->getUrl(); ?>">
                        </div>
                    <?php endforeach; ?>
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
    $(document).ready(function() {
        var page = 1;
        $('#medias').on('mouseenter','.media', function() {
            $(this).find('.hover-media').show();
        });
        $('#medias').on('mouseleave','.media', function() {
            $(this).find('.hover-media').hide();
        });
        $('#loadMore').click(function () {
            page++;
            $.ajax({
                url: base_url + '/admin/media/charger-plus',
                type : "GET",
                data : {
                    page : page
                },
                success : function(data) {
                    for(var i = 0; i < data.length; i++) {
                        var img = `
                            <div class="col position-relative media">
                                <div class="position-absolute h-100 w-100 img-thumbnail hover-media" style="background-color: rgba(0,0,0,0.3); display: none;">
                                    Coucou
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
        });
    });
</script>