<?php

require_once 'boot.php';

if (!isset($_GET['id'])) {
    $draft = null;
} else {
    $draft = \App\Draft::load($_GET['id']);
}

$faction_data = json_decode(file_get_contents('data/factions.json'), true);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $draft ? $draft->name() . ' | ' : '' ?>TI4 - Milty Draft</title>
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

        <?php if ($draft) : ?>
            <h1><?= $draft->name() ?></h1>
            <h2>Milty Draft</h2>
        <?php else : ?>
            <h1>Milty Draft</h1>
        <?php endif; ?>

        <div id="tabs">
            <nav>
                <div class="content-wrap">
                    <div class="left">
                        <a class="active" href="#draft">Draft</a>
                        <a href="#regen">Regenerate</a>
                        <a href="#map">Map</a>
                        <a href="#log">Log</a>
                        <a href="#config">Config</a>
                        <a href="#session">Session</a>
                    </div>
                    <div class="right">
                        <a href="#faq">FAQ</a>
                    </div>
                </div>
            </nav>
            <div class="tab active" id="draft">
                <div class="content-wrap">
                    <?php if ($draft == null || $draft == false) : ?>
                        <h2 class="error">Draft not found. (or something else went wrong)</h2>
                    <?php else : ?>
                        <div class="status" id="turn">
                            <p>It's <span id="current-name">x's</span> turn to draft something. <span id="admin-msg">You are the admin so you can do this for them.</span></p>
                        </div>
                        <div class="status" id="done">
                            <p>This draft is over. <a class="view-map" href="#map">View map</a></p>
                        </div>

                        <?php if (empty($draft->log())) : ?>
                            <p class="regen-help">
                                Something not quite right? Untill the first draft-pick is done you can <a class="tabnav" href="#regen">regenerate the draft options</a>.
                            </p>
                        <?php endif; ?>

                        <div class="players">
                            <?php foreach (array_values($draft->players()) as $i => $player) : ?>
                                <div id="player-<?= $player['id'] ?>" class="player">
                                    <h3><span><?= $i + 1 ?></span> <?= $player['name'] ?> <?= ($player['team'] ?? false) ? '<span class="team team_' . $player['team'] . '">[Team ' . $player['team'] . ']</span>' : '' ?></h3>

                                    <span class="you" data-id="<?= $player['id'] ?>">you</span>
                                    <p>
                                        Slice: <span class="chosen-slice">?</span><br />
                                        Faction: <span class="chosen-faction">?</span><br />
                                        Position: <span class="chosen-position">?</span>
                                    </p>
                                    <p class="center">
                                        <button class="claim" data-id="<?= $player['id'] ?>">Claim</button>
                                        <button class="unclaim" data-id="<?= $player['id'] ?>">Unclaim</button>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="factions draft-options">
                            <h3>Factions</h3>
                            <div class="options">
                                <?php foreach ($draft->factions() as $f) : ?>
                                    <?php $faction = $faction_data[$f]; ?>
                                    <?php $homesystem = ($faction['set'] == 'discordant' || $faction['set'] == 'discordantexp') ? 'DS_' . $faction['id'] : $faction['homesystem']; ?>

                                    <div class="faction option" data-homesystem="<?= $homesystem ?>" data-faction="<?= $faction['name'] ?>">
                                        <div>
                                            <img src="<?= url('img/factions/ti_' . $faction['id'] . '.png') ?>" /><br />

                                            <span><?= $faction['name'] ?></span><br />
                                            <a href="#" data-id="<?= $faction['id'] ?>" class="open-reference">[reference]</a> <a target="_blank" href="<?= $faction['wiki'] ?>" class="more">[wiki]</a><br />
                                            <button class="draft" data-category="faction" data-value="<?= $faction['name'] ?>">Draft</button>
                                            <span class="drafted-by" data-category="faction" data-value="<?= $faction['name'] ?>"></span>
                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="slices draft-options">
                            <h3>Slices</h3>
                            <div class="options">
                                <?php foreach ($draft->slices() as $slice_id => $slice) : ?>
                                    <div class="slice option" data-slice="<?= $slice_id ?>">
                                        <div class="slice-graph">
                                            <div class="wrap">
                                                <?php foreach ($slice['tiles'] as $i => $tile) : ?>
                                                    <img class="tile-<?= $i ?>" src="<?= url('img/tiles/ST_' . $tile . '.png') ?>" />
                                                    <img class="zoom tile-<?= $i ?>" src="<?= url('img/tiles/ST_' . $tile . '.png') ?>" />
                                                <?php endforeach; ?>
                                                <img class="tile-h" src="<?= url('img/tiles/ST_0.png') ?>" />
                                            </div>
                                        </div>

                                        <div class="slice-info">
                                            <h4>Slice <?= $slice_id + 1 ?></h4>

                                            <div class="info">
                                                <?php foreach ($slice['specialties'] as $s) : ?>
                                                    <img class="tech-specialty" title="<?= $s ?>" src="<?= url('img/tech/' . $s . '.webp') ?>" alt="<?= $s ?>" />
                                                <?php endforeach; ?>


                                                <?php foreach ($slice['legendaries'] as $l) : ?>
                                                    <abbr class="legendary" title="<?= $l ?>"><img src="<?= url('img/legendary.webp') ?>"></abbr>
                                                <?php endforeach; ?>

                                                <?php foreach ($slice['wormholes'] as $w) : ?>
                                                    <?php if ($w == 'alpha') : ?>
                                                        <abbr class="wormhole" title="<?= $w ?>">&alpha;</abbr>
                                                    <?php elseif ($w == 'beta') : ?>
                                                        <abbr class="wormhole" title="<?= $w ?>">&beta;</abbr>
                                                    <?php elseif ($w == 'alpha-beta') : ?>
                                                        <abbr class="wormhole" title="alpha">&alpha;</abbr>
                                                        <abbr class="wormhole" title="beta">&beta;</abbr>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>

                                            <p class="resource-count">
                                                Total:
                                                <abbr title="resources" class="resources"><?= $slice['total_resources'] ?></abbr>
                                                <abbr title="influence" class="influence"><?= $slice['total_influence'] ?></abbr>
                                            </p>

                                            <p class="resource-count">
                                                Optimal:
                                                <abbr title="resources" class="resources"><?= $slice['optimal_resources'] ?></abbr>
                                                <abbr title="influence" class="influence"><?= $slice['optimal_influence'] ?></abbr>
                                            </p>

                                            <p class="center">
                                                <button class="draft" data-category="slice" data-value="<?= $slice_id ?>">Draft</button>
                                                <span class="drafted-by" data-category="slice" data-value="<?= $slice_id ?>"></span>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>



                        <div class="positions draft-options">
                            <h3>Positions</h3>
                            <div class="options">
                                <?php for ($i = 0; $i < count($draft->players()); $i++) : ?>
                                    <div class="position option" data-position="<?= $i ?>">
                                        <span>
                                            <?php if ($i == 0) : ?>
                                                SPEAKER
                                            <?php else : ?>
                                                <?= ordinal($i + 1); ?>
                                            <?php endif; ?>
                                        </span>

                                        <button class="draft" data-category="position" data-value="<?= $i ?>">Draft</button>
                                        <span class="drafted-by" data-category="position" data-value="<?= $i ?>"></span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <script>
                            window.draft = <?= $draft; ?>;
                        </script>

                    <?php endif; ?>
                </div>
            </div>

            <?php $config = $draft->config(); ?>
            <div class="tab" id="regen">
                <div class="content-wrap">
                    <?php if (empty($draft->log())) : ?>
                        <p id="regen-options">
                            <label for="shuffle_slices"><input type="checkbox" checked id="shuffle_slices" name="shuffle_slices" /> New Slices</label>
                            <label for="shuffle_factions"><input type="checkbox" checked id="shuffle_factions" name="shuffle_factions" /> New Factions</label>
                            <label for="shuffle_order"><input type="checkbox" id="shuffle_order" name="shuffle_order" /> New <?= (($config->alliance["alliance_teams"] ?? "") == 'random') ? 'teams and ' : '' ?>player order</label>
                            <button id="regenerate" class="btn">Regenerate</button>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab" id="config">
                <div class="content-wrap">
                    <h3>Configuration used</h3>

                    <p>
                        <label>Number of Players:</label> <strong><?= count($draft->players()) ?></strong>
                    </p>
                    <p>
                        <label>Use preset Draft Order:</label> <strong><?= e($config->preset_draft_order == true, 'yes', 'no') ?></strong>
                    </p>
                    <p>
                        <label>Number of Slices:</label> <strong><?= $config->num_slices ?></strong>
                    </p>
                    <p>
                        <label>Number of Factions:</label> <strong><?= $config->num_factions ?></strong>
                    </p>
                    <p>
                        <label>Include PoK:</label> <strong><?= e($config->include_pok, 'yes', 'no') ?></strong>
                    </p>
                    <p>
                        <label>Include DS Tiles:</label> <strong><?= e($config->include_ds_tiles, 'yes', 'no') ?></strong>
                    </p>
                    <p>
                        <label>Include Base Game Factions:</label> <strong><?= e($config->include_base_factions, 'yes', 'no') ?></strong>
                    </p>
                    <p>
                        <label>Include POK Factions:</label> <strong><?= e($config->include_pok_factions, 'yes', 'no') ?></strong>
                    </p>
                    <p>
                        <label>Include Keleres:</label> <strong><?= e($config->include_keleres, 'yes', 'no') ?></strong>
                    </p>
                    <p>
                        <label>Include Discordant Stars:</label> <strong><?= e($config->include_discordant, 'yes', 'no') ?></strong>
                    </p>
                    <p>
                        <label>Include Discordant Stars expansion:</label> <strong><?= e($config->include_discordantexp, 'yes', 'no') ?></strong>
                    </p>
                    <?php if (isset($config->min_legendaries)) : ?>
                        <p>
                            <label>Minimum wormholes:</label> <strong><?= $config->min_wormholes ?></strong>
                        </p>
                        <p>
                            <label>Minimum legendaries:</label> <strong><?= $config->min_legendaries ?></strong>
                        </p>
                    <?php endif; ?>
                    <p>
                        <label>Max. 1 wormhole per slice:</label> <strong><?= e(isset($config->max_1_wormhole) && $config->max_1_wormhole, 'yes', 'no') ?></strong>
                    </p>
                    <hr />
                    <p>
                        <label>Minimum Optimal Influence:</label> <strong><?= $config->minimum_optimal_influence ?></strong>
                    </p>
                    <p>
                        <label>Minimum Optimal Resources:</label> <strong><?= $config->minimum_optimal_resources ?></strong>
                    </p>

                    <p>
                        <label>Minimum Optimal Total:</label> <strong><?= $config->minimum_optimal_total ?></strong>
                    </p>

                    <p>
                        <label>Maximum Optimal Total:</label> <strong><?= $config->maximum_optimal_total ?></strong>
                    </p>

                    <p>
                        <label>Custom Factions:</label> <strong>
                            <?php if ($config->custom_factions != null) : ?>
                                <?= implode(', ', $config->custom_factions) ?>
                            <?php else : ?>
                                no
                            <?php endif; ?>
                        </strong>
                    </p>
                    <p>
                        <label>Custom Slices:</label> <strong>
                            <?php if ($config->custom_slices != null) : ?>
                                <?php foreach ($config->custom_slices as $slice) : ?>
                                    <?= implode(',', $slice) ?><br />
                                <?php endforeach; ?>
                            <?php else : ?>
                                no
                            <?php endif; ?>
                        </strong>
                    </p>
                    <p>
                        <label>Slices Generated:</label>
                        <strong>
                            <?php foreach ($draft->slices() as $slice_id => $slice) : ?>
                                <?= implode(',', $slice['tiles']); ?><br />
                            <?php endforeach; ?>
                        </strong>
                    </p>
                    <?php if ($config->alliance) : ?>
                        <hr />
                        <h3>Alliance Configuration</h3>
                        <p>
                            <label>Team Creation:</label> <strong><?= ucfirst($config->alliance["alliance_teams"] ?? "") ?></strong>
                        </p>
                        <p>
                            <label>Force Teammates Position:</label> <strong><?= ucfirst($config->alliance["alliance_teams_position"] ?? "") ?></strong>
                        </p>
                        <p>
                            <label>Force Team Double Picks:</label> <strong><?= e($config->alliance["force_double_picks"], 'Yes', 'No') ?></strong>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab" id="map">
                <div class="content-wrap">
                    <button class="print-button" onClick="window.print()">Print</button>
                    <p class="map-layout-selector">
                        Select map layout:
                        <select id="change-mapview">
                            <option value="hyperlane">Hyperlanes (default)</option>
                            <option value="slices">Individual Slices</option>
                        </select>
                    </p>


                    <div class="mapview current" id="mapview-hyperlane">
                        <h3 class="map-layout-title">Hyperlane Map</h3>
                        <div class="map-container">
                            <div class="map-offset">
                                <div id="map-wrap" class="map">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mapview" id="mapview-slices">
                        <h3 class="map-layout-title">Slices</h3>
                        <div class="slice-container">
                            <p>Slices are shown in speaker order</p>

                            <div id="mapslices-wrap"></div>
                        </div>
                    </div>

                    <h3>Tiles</h3>
                    <p id="tile-gather"></p>
                    <h3 class="tts-map-string-title">TTS Map String*</h3>
                    <p id="tts-string"></p>
                    <span class="help">*This feature is in development and hasn't been rigorously tested, so some maps might give odd results. If you find something wrong, please <a href="https://twitter.com/samtubbax" target="_blank">reach out</a> and let me know!</span>
                </div>
            </div>
            <div class="tab" id="log">
                <div class="content-wrap">
                    <h3>Log</h3>
                    <div id="log-content"></div>
                    <br>
                    <button class="undo-last-action">Undo last action</button>
                </div>
            </div>
            <div class="tab" id="session">
                <div class="content-wrap">
                  <div id="current-session">
                      <h3>Session</h3>
                      <p>Make sure to save your passkey so you can restore your session if it is lost (e.g., cleared cache). The passkey is also useful if you want to draft on another device.</p>
                      <p id="current-session-admin">
                          <label>Admin Passkey:</label><strong></strong>
                      </p>
                      <p id="current-session-player">
                          <label>Passkey:</label><strong></strong>
                      </p>
                      <br>
                  </div>
                  <div>
                      <h3>Restore Session</h3>
                      <form id="secret-form" action="restore.php" method="post">
                          <div class="secret_input_section">
                              <p class="secret_label">Restore a session to be able to draft in this device.</p>
                              <div class="input secret">
                                  <input type="text" placeholder="Passkey" name="secret" />
                              </div>
                          </div>
                          <p>
                              <button type="submit" id="submit">Restore</button>
                          </p>
                      </form>
                  </div>
                </div>
            </div>
            <?php require_once 'faq.php'; ?>
        </div>
    </div>

    <div class="popup" id="confirm-popup">
        <div class="content">
            <p>
                Are you sure you wish to draft the following <span id="confirm-category"></span>: <span id="confirm-value"></span>.<br />
                This can only be undone by the creator of your draft.
            </p>
            <p>
                <button id="confirm">Confirm</button>
                <button id="confirm-cancel">Cancel</button>
            </p>
        </div>
    </div>

    <div class="popup" id="reference-popup">
        <a class="btn close-reference invert">&times;</a>
        <img data-base="<?= url('img/reference/r_') ?>" src="" />
    </div>

    <div class="popup" id="error-popup">
        <div class="content">
            <p>
                Something went wrong. Maybe you left this tab open too long and the data is outdated. Try refreshing and trying again.
            </p>
            <p>Error: <span id="error-message"></span></p>
            <p>
                <button id="close-error">Ok</button>
            </p>

        </div>
    </div>


    <div class="overlay" id="loading">
        Loading. Please wait.<br />
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;display:block;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
            <circle cx="50" cy="50" r="0" fill="none" stroke="#fefcf8" stroke-width="2">
                <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="0s"></animate>
                <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="0s"></animate>
            </circle>
            <circle cx="50" cy="50" r="0" fill="none" stroke="#fefcf8" stroke-width="2">
                <animate attributeName="r" repeatCount="indefinite" dur="1s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="-0.5s"></animate>
                <animate attributeName="opacity" repeatCount="indefinite" dur="1s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="-0.5s"></animate>
            </circle>
        </svg>
    </div>

    <div class="popup" id="session-popup">
        <div class="content">
            <a class="btn invert close-popup">&times;</a>
            <p id="admin">Your admin passkey is <strong id="popup-admin-passkey">SOME PASSKEY</strong></p>
            <p id="user">Your passkey is <strong id="popup-passkey">SOME PASSKEY</strong></p>
            <p>Write this down somewhere. You can read more about passkeys in the SESSION tab</p>
        </div>
    </div>

    <script>
        window.routes = {
            "claim": "<?= url('claim.php') ?>",
            "pick": "<?= url('pick.php') ?>",
            "regenerate": "<?= url('generate.php') ?>",
            "tile_images": "<?= url('img/tiles') ?>",
            "data": "<?= url('data.php') ?>",
            "undo": "<?= url('undo.php') ?>",
            "restore": "<?= url('restore.php') ?>"
        }
    </script>
    <script src="<?= url('js/vendor.js?v=' . $_ENV['VERSION']) ?>"></script>
    <script src="<?= url('js/draft.js?v=' . $_ENV['VERSION']) ?>"></script>
    <script src="<?= url('js/generate-map.js?v=' . $_ENV['VERSION']) ?>"></script>
</body>

</html>
