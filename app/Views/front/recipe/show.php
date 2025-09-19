<div class="row">
    <div class="col">
        <?php if (isset($recipe['mea']['file_path'])) : ?>
            <img src="<?= base_url($recipe['mea']['file_path']) ?>" class="img-fluid recipe-img-mea">
        <?php endif;?>
    </div>
</div>
