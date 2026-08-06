<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="color-scheme" content="light dark">

    <link href="<?= htmlspecialchars(asset('css/app.css'), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">

    <title>Lottery Generator - <?= htmlspecialchars((string) ($title ?? ''), ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
<div class="jumbotron">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="jumbotron-content">
                <h1 class="display-4">
                    <a href="/" class="text-white text-decoration-none">Lottery Generator</a>
                </h1>
                <p class="mb-0"><em>Just for fun, makes an attempt at 'guessing' the Lotto numbers using a half-arsed bit of logic.</em></p>
            </div>
            <?= $navigation ?? '' ?>
        </div>
    </div>
</div>

<div class="container">
    <?= $content ?>
</div>

<script src="<?= htmlspecialchars(asset('js/app.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
