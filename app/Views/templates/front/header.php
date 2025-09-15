<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img class="img-fluid" src="<?= base_url('assets/img/logo-32.png'); ?>" alt="logo"> Ins3gram
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto">
                <?php
                foreach ($menus as $km => $menu) {
                    if (!isset($menu['subs'])) { ?>
                        <li class="nav-item <?= ($localmenu === $km ? 'active' : '') ?>"
                            id="menu_<?= $km ?>">
                            <a class="nav-link" href="<?= base_url($menu['url']) ?>">
                                <?php if (isset($menu['icon'])) { echo $menu['icon']; }
                                else { ?><svg class="nav-icon"><span class="bullet bullet-dot"></svg><?php } ?>
                                <?= $menu['title'] ?>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= (isset($menu['icon'])) ? $menu['icon'] : ""; ?>
                                <?= $menu['title'] ?></a>
                            <ul class="dropdown-menu">
                                <?php
                                foreach($menu['subs'] as $ksm => $smenu) {

                                    if (isset($smenu['admin']) ) { continue; }
                                    if (isset($smenu['require'])) { continue; } ?>
                                    <li class="dropdown-item" id="menu_<?= $ksm ?>">
                                        <a class="nav-link" href="<?= base_url($smenu['url']); ?>"><?php if (isset($smenu['icon'])) echo $smenu['icon']; ?>
                                            <?= $smenu['title'] ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php }} ?>


            </ul>
        </div></div></nav>