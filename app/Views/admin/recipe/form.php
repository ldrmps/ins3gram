<div class="row mb-3">
    <div class="col">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="flex-fill me-3">
                    <input type="text" class="form-control" id="name" placeholder="Nom de la recette" name="name" value="<?= isset($recipe) ? $recipe['name'] : '' ?>">
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="switchActive" name="active">
                    <label class="form-check-label" for="switchActive">Active</label>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!--START: COLONNE PRINCIPALE -->
    <div class="col-md-10">
        <div class="card h-100">
            <div class="card-body">
                <!--START: TABS-LINKS -->
                <ul class="nav nav-tabs" id="tabsRecipe">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#general-tab-pane">Général</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ingredient-tab-pane">Ingrédients</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#keyword-tab-pane">Mots Clés</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#step-tab-pane">Étapes</button>
                    </li>
                    <?php if(isset($recipe)) : ?>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#comment-tab-pane">Commentaires</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fav-tab-pane">Favoris</button>
                        </li>
                    <?php endif; ?>
                </ul>
                <!--END: TABS-LINKS -->
                <!--START: TABS-PANE -->
                <div class="tab-content p-3 border border-1 border-top-0 rounded rounded-top-0" id="tabsRecipeContent">
                    <!--START: GENERAL -->
                    <div class="tab-pane fade show active" id="general-tab-pane" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label">Description globale de la recette</label>
                            <textarea class="form-control" rows="3" id="description" name="description"></textarea>
                        </div>
                        <div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="switchAlcool" name="alcool">
                                <label class="form-check-label" for="switchAlcool">Avec Alcool</label>
                            </div>
                        </div>
                    </div>
                    <!--END:GENERAL -->
                    <!--START: INGREDIENTS -->
                    <div class="tab-pane fade" id="ingredient-tab-pane" role="tabpanel">
                        INGREDIENT
                    </div>
                    <!--END: INGREDIENTS -->
                    <!--START: MOTS CLÉS -->
                    <div class="tab-pane fade" id="keyword-tab-pane" role="tabpanel">
                        MOTS CLES
                    </div>
                    <!--END: MOTS CLÉS -->
                    <!--START: ÉTAPES -->
                    <div class="tab-pane fade" id="step-tab-pane" role="tabpanel">
                        ETAPES
                    </div>
                    <!--END: ÉTAPES -->
                    <?php if(isset($recipe)) : ?>
                        <!--START: COMMENTAIRES -->
                        <div class="tab-pane fade" id="comment-tab-pane" role="tabpanel">
                            COMMENTAIRES
                        </div>
                        <!--END: COMMENTAIRES -->
                        <!--START: FAVORIS -->
                        <div class="tab-pane fade" id="fav-tab-pane" role="tabpanel">
                            FAVORIS
                        </div>
                        <!--END: FAVORIS -->
                    <?php endif; ?>
                </div>
                <!--END: TABS-PANE -->
            </div>
        </div>
    </div>
    <!--END: COLONNE PRINCIPALE -->
    <!--START: COLONNE ACTIONS -->
    <div class="col">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">Valider</button>
                </div>
                <?php if (isset($recipe)) :  ?>
                    <div class="ms-2">
                        <div>
                            <span class="fw-bold">Créée le: </span>
                        </div>
                        <div>
                            <span class="fw-bold">Modifiée le: </span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!--END: COLONNE ACTIONS -->
</div>
<script>
    $(document).ready(function () {
        tinymce.init({
            selector: '#description',
            height : "200",
            language: 'fr_FR',
            menubar: false,
            plugins: [
                'preview', 'code', 'fullscreen','wordcount', 'link','lists',
            ],
            skin: 'oxide',
            content_encoding: 'text',
            toolbar: 'undo redo | formatselect | ' +
                'bold italic link forecolor backcolor removeformat | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +' fullscreen  preview code'
        });
    });
</script>