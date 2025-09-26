<div class="row">
    <div class="col text-center">
        <h1>Liste des recettes</h1>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="d-flex align-items-end">
            <span>Trier par </span>
            <select class="form-select">
                <option>Nom (ASC)</option>
            </select>
            <?php echo form_open('recette', ['method' => 'get']); ?>
            <div class="btn-group">
                <button type="submit" name="per_page" value="8" class="btn btn-primary">8</button>
                <button type="submit" name="per_page" value="16" class="btn btn-secondary">16</button>
                <button type="submit" name="per_page" value="32" class="btn btn-secondary">24</button>
            </div>
        </div>
    </div>
</div>
<!--START: PAGE -->
<div class="row">
    <!--START: FILTRE -->
    <div class="col-md-2 ">
        <span class="h3">FILTRES</span>
    </div>
    <!--END: FILTRE -->
    <!--START: CONTENUS -->
    <div class="col p-4">
        <!--START: RECETTES -->
        <div class="row row-cols-2 row-cols-md-4">
            <?php foreach ($recipes as $recipe): ?>
                <div class="col mb-4">
                    <div class="card ls-recipe h-100">
                        <img class="card-img-top" src="<?= base_url($recipe['mea']);?>">
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
                <div class="d-flex justify-content-center">
                    <nav aria-label="...">
                        <ul class="pagination">
                            <li class="page-item"><a href="#" class="page-link">Previous</a></li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item active">
                                <a class="page-link" href="#" aria-current="page">2</a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!--END: PAGINATION -->
    </div>
    <!--END: CONTENUS -->
</div>
<!--END: PAGE -->