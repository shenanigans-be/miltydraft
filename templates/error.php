<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TI4 - Milty Draft | Something went wrong</title>
    <link rel="stylesheet" href="<?= url('css/style.css?v=' . $_ENV['VERSION']) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,300;0,600;1,300&family=Staatliches&display=swap" rel="stylesheet">


    <meta property="og:image" content="<?= url('og.png') ?>" />

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#1a2266">
    <meta name="msapplication-TileColor" content="#fdfcf8">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <div class="container">

        <h1>Milty Draft</h1>

        <div id="tabs">
            <nav>
                <div class="content-wrap">
                    <div class="left">
                        <a class="active" href="#draft">Draft</a>
                    </div>
                    <div class="right">
                        <a href="#faq">FAQ</a>
                    </div>
                </div>
            </nav>
            <div class="tab active" id="draft">
                <div class="content-wrap">
                    <h2>Draft not found. (or something else went wrong)</h2>
                </div>
            </div>
            <?php require_once 'templates/faq.php'; ?>
        </div>
    </div>

    <script src="<?= url('js/vendor.js?v=' . $_ENV['VERSION']) ?>"></script>
    <script src="<?= url('js/draft.js?v=' . $_ENV['VERSION']) ?>"></script>
</body>

</html>
