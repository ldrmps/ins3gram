<?php
if(!isset($ingredient)) :
    echo form_open('/admin/ingredient/insert');
else:
    echo form_open('/admin/ingredient/update'); ?>
    <input type="hidden" name="id_ingredient" value="<?= $ingredient['id']; ?>">
<?php
endif;
?>
<div class="row mb-3">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h3>
                <?php if(isset($ingredient)) : ?>
                    Modification d'un ingrédient
                <?php else : ?>
                    Création d'un ingrédient
                <?php endif;?>
                </h3>
            </div>
            <div class="card-body d-flex align-items-center justify-content-between">
                <div class="flex-fill me-3">
                    <input type="text" class="form-control" id="name" placeholder="Nom de l'ingrédient" name="name" value="<?= isset($ingredient) ? $ingredient['name'] : '' ?>" required>
                </div>
                <div class="flex-fill me-3">
                    <input type="text" class="form-control" id="description" placeholder="Description de l'ingrédient" name="ingredient" value="<?= isset($ingredient) ? $ingredient['description'] : '' ?>" required>
                </div>
                <div class="flex-fill me-3">
                    <select class="form-select flex-fill select-brand">

                    </select>
                </div>
                <div class="flex-fill me-3">
                    <select class="form-select flex-fill select-category">

                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        initAjaxSelect2('.select-brand', {
            url: baseUrl + 'admin/brand/search',
            placeholder: 'Rechercher un marque...',
            searchFields: 'name',
            showDescription: true,
            delay: 250
        });
        initAjaxSelect2('.select-category', {
            url: baseUrl + 'admin/category-ingredient/search',
            placeholder: 'Rechercher une catégorie...',
            searchFields: 'name',
            delay: 250
        });

    });
</script>