<div class="row">
    <div class="col text-center">
        <h1>Liste des recettes</h1>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="d-flex align-items-end">
            <span>Trier par </span>
            <select name="sort" class="form-select" onchange="window.location.href=this.value" >
                <option value="<?= build_filter_url(['sort' => 'name_asc']) ?>" <?= is_filter_active('sort', 'name_asc') ? 'selected' : '' ?>>Nom (A-Z)</option>
                <option  value="<?= build_filter_url(['sort' => 'name_desc']) ?>" <?= is_filter_active('sort', 'name_desc') ? 'selected' : '' ?>>Nom (Z-A)</option>
                <option  value="<?= build_filter_url(['sort' => 'score_desc']) ?>" <?= is_filter_active('sort', 'score_desc') ? 'selected' : '' ?>>Meilleure note</option>
            </select>

            <div class="btn-group">
                <div class="btn-group">
                    <a href="<?= build_filter_url(['per_page' => 8]) ?>"
                       class="btn <?= is_filter_active('per_page', 8) || ($per_page == 8) ? 'btn-primary' : 'btn-secondary' ?>">8</a>
                    <a href="<?= build_filter_url(['per_page' => 16]) ?>"
                       class="btn <?= is_filter_active('per_page', 16)|| ($per_page == 16) ? 'btn-primary' : 'btn-secondary' ?>">16</a>
                    <a href="<?= build_filter_url(['per_page' => 24]) ?>"
                       class="btn <?= is_filter_active('per_page', 24)|| ($per_page == 24) ? 'btn-primary' : 'btn-secondary' ?>">24</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--START: PAGE -->
<div class="row">
    <!--START: FILTRE -->
    <div class="col-md-2 ">
        <span class="h3">FILTRES</span>
        <?php echo form_open(build_filter_url(), ['method' => 'get']); ?>
        <div class="form-check">
            <input type="checkbox" name="alcool" value="1" class="form-check-input" id="alcool"
                    <?= is_filter_active('alcool', 1) ? 'checked' : '' ?>>

            <label class="form-check-label" for="alcool">Avec alcool</label>
        </div>
        <div class="mb-3">
                            <span class="btn btn-primary" id="add-ingredient">
                                <i class="fas fa-plus"></i> Ajouter un ingrédient
                            </span>
        </div>
        <div id="zone-ingredients">
        </div>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <?php echo form_close(); ?>

    </div>
    <!--END: FILTRE -->
    <!--START: CONTENUS -->
    <div class="col p-4">
        <!--START: RECETTES -->
        <div class="row row-cols-2 row-cols-md-4 all-recipes">
            <?php foreach ($recipes as $recipe): ?>
                <div class="col mb-4">
                    <div class="card ls-recipe h-100">
                        <a href="<?= base_url('recette/'.$recipe['slug']); ?>">
                            <img class="card-img-top img-fluid" src="<?= base_url($recipe['mea']);?>">
                        </a>
                        <div class="card-body">
                            <div class="card-title h5">
                                <?= $recipe['name']; ?>
                            </div>
                            <div>
                                <?= $recipe['score']; ?>
                            </div>
                            <div class="d-grid">
                                <a href="<?= base_url('recette/'.$recipe['slug']); ?>" class="btn btn-primary"><i class="fas fa-eye"></i> Voir la recette</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!--END: RECETTES -->
        <!--START: PAGINATION -->
        <div class="row">
            <div class="col">
                <?php if ($pager): ?>
                    <div class="d-flex justify-content-center">
                        <?= $pager->links('default', 'bootstrap_full') ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <!--END: PAGINATION -->
    </div>
    <!--END: CONTENUS -->
</div>
<!--END: PAGE -->
<script>
    $(document).ready(function () {
        baseUrl = "<?= base_url(); ?>";
        $('#add-ingredient').on('click', function () {
            let row = `
                <div class="row mb-3 row-ingredient">
                    <div class="col">
                        <div class="input-group">
                            <select class="form-select flex-fill select-ingredient" name="ingredients[]">
                            </select>
                        </div>
                    </div>
                </div>
            `;
            $('#zone-ingredients').append(row);
            initAjaxSelect2('#zone-ingredients .row-ingredient:last-child .select-ingredient', {
                url: baseUrl + 'admin/ingredient/search',
                placeholder: 'Rechercher un ingrédient...',
                searchFields: 'name',
                showDescription: false,
                delay: 250
            });
        });
    })
</script>