<div class="row">
    <div class="col">
        <div class="position-relative">
            <?php if(isset($recipe['mea']['file_path'])) : ?>
                <img src="<?= base_url($recipe['mea']['file_path']); ?>" class="img-fluid recipe-img-mea">
            <?php endif;?>
            <div class="position-absolute top-0 start-0 bg-black w-100 h-100 opacity-25"></div>
            <div class="position-absolute top-50 start-50 translate-middle text-white text-center">
                <h1><?= isset($recipe['name']) ? $recipe['name'] : ''; ?></h1>
                Proposé par : <?= isset($recipe['user']) ? $recipe['user']->username : ''; ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="rating-stars" data-rating="0">
            <i class="far fa-star" data-value="1"></i>
            <i class="far fa-star" data-value="2"></i>
            <i class="far fa-star" data-value="3"></i>
            <i class="far fa-star" data-value="4"></i>
            <i class="far fa-star" data-value="5"></i>
        </div>
    </div>
    <div class="col">
        <!--TODO: Coeur de favoris qui sauvegarde au clique, sinon swal2 qui propose un lien vers /sign-in  -->
        <!--TODO: Liens de partage de la page vers les reseaux sociaux (facebook / twitter)  -->
    </div>
</div>
<div class="row">
    <div class="col">
        <!-- TODO: Liste des tag dans des badges bootstrap cliquable (url /recettes?tag=nomdutag -->
    </div>
</div>
<div class="row row-cols-3">
    <!--TODO: Liste des ingrédients sous la forme image - nom ingrédient - quantité - unité -->
</div>
<div class="row">
    <?php if(!empty($recipe['images'])) : ?>
        <div class="col-md-6">
            <div id="main-slider" class="splide mb-3">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php foreach($recipe['images'] as $image) : ?>
                            <li class="splide__slide">
                                <a href="<?= base_url($image['file_path']); ?>" data-lightbox="mainslider">
                                    <img class="img-fluid" src="<?= base_url($image['file_path']); ?>">
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div id="thumbnail-slider" class="splide mb-3">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php foreach($recipe['images'] as $image) : ?>
                            <li class="splide__slide">
                                <img class="img-thumbnail rounded" src="<?= base_url($image['file_path']); ?>">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="col">
        <div class="d-flex flex-column justify-content-center h-100 p-3">
            <?= $recipe['description']; ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var main = new Splide('#main-slider', {
            type       : 'fade',
            heightRatio: 0.5,
            pagination : false,
            arrows     : false,
            cover      : false, //désactiver "cover" pour éviter le crop
        });
        var thumbnails = new Splide('#thumbnail-slider', {
            rewind       : true,
            fixedWidth   : 80,
            fixedHeight  : 80,
            isNavigation : true,
            gap          : 10,
            focus        : 'center',
            pagination   : false,
            cover        : false,
            breakpoints : {
                640: {
                    fixedWidth  : 60,
                    fixedHeight : 60,
                },
            },
        });
        main.sync(thumbnails);
        main.mount();
        thumbnails.mount();

        // Système de notation par étoiles
        const ratingContainer = $('.rating-stars');
        let currentHoverRating = 0;

        // Fonction pour mettre à jour l'affichage des étoiles
        function updateStars(rating) {
            ratingContainer.find('.fa-star').each(function() {
                const starValue = $(this).data('value');
                if (starValue <= rating) {
                    $(this).removeClass('fa-regular').addClass('fa-solid');
                } else {
                    $(this).removeClass('fa-solid').addClass('fa-regular');
                }
            });
        }

        // Au survol du conteneur - détection de l'étoile survolée
        ratingContainer.on('mousemove', function(e) {
            const target = $(e.target).closest('.fa-star');
            if (target.length) {
                currentHoverRating = target.data('value');
                updateStars(currentHoverRating);
            }
        });

        // Quand on quitte la zone de notation, on revient à la note sauvegardée
        ratingContainer.on('mouseleave', function() {
            const savedRating = $(this).data('rating') || 0;
            currentHoverRating = 0;
            updateStars(savedRating);
        });

        // Au clic sur le conteneur, on valide la note en cours de survol
        ratingContainer.on('click', function(e) {
            console.log('Clic sur le conteneur:', currentHoverRating);
            if (currentHoverRating > 0) {
                <?php if(empty($session_user)): ?>
                Swal.fire({
                    title: 'Connexion requise',
                    text: 'Vous devez être connecté pour noter cette recette',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Se connecter',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= base_url('/sign-in'); ?>';
                    }
                });
                <?php else: ?>
                ratingContainer.data('rating', currentHoverRating);
                console.log('Note validée:', currentHoverRating);
                <?php endif; ?>
            }
        });
    })
</script>
<style>

</style>