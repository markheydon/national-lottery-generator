<?php

ob_start();
?>
    <div class="row">
        <?php foreach ($games as $index => $game) : ?>
            <?php if ($index > 0 && $index % 3 === 0) : ?>
    </div>
    <div class="row">
            <?php endif; ?>
            <div class="col-md text-center">
                <a href="/game/<?= htmlspecialchars($game->getSlug(), ENT_QUOTES, 'UTF-8') ?>/generate">
                    <div class="card mx-auto mb-4" style="max-width: 18rem;">
                        <img class="card-img-top"
                             src="<?= htmlspecialchars(asset('img/' . $game->getGameLogo()), ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($game->getGameName(), ENT_QUOTES, 'UTF-8') ?>"/>
                        <div class="card-body">
                            <p class="btn btn-primary mb-0">Generate Numbers</p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php
$content = ob_get_clean();

include project_root() . '/templates/layout.php';
