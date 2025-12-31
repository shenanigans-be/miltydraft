<?php
    /** @var \App\Draft\Draft $draft */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $draft->settings->name ?> | TI4 - Milty Draft</title>
    <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,300;0,600;1,300&family=Staatliches&display=swap" rel="stylesheet">


    <meta property="og:image" content="<?= asset_url('og.png') ?>" />

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
        <h1><?= $draft->settings->name ?></h1>
        <h2>Milty Draft</h2>

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
                    <div class="status" id="turn">
                        <p>It's <span id="current-name">x's</span> turn to draft something. <span id="admin-msg">You are the admin so you can do this for them.</span></p>
                    </div>
                    <div class="status" id="done">
                        <p>This draft is over. <a class="view-map" href="#map">View map</a></p>
                    </div>

                    <?php if ($draft->canRegenerate()) : ?>
                        <p class="regen-help">
                            Something not quite right? Untill the first draft-pick is done you can <a class="tabnav" href="#regen">regenerate the draft options</a>.
                        </p>
                    <?php endif; ?>

                    <div class="players">
                        <?php foreach (array_values($draft->players) as $i => $player) : ?>
                            <div id="player-<?= $player->id ?>" class="player">
                                <h3><span><?= $i + 1 ?></span> <?= $player->name ?> <?= $player->team ? '<span class="team team_' . $player->team . '">[Team ' . $player->team . ']</span>' : '' ?></h3>

                                <span class="you" data-id="<?= $player->id ?>">you</span>
                                <p>
                                    Slice: <span class="chosen-slice">?</span><br />
                                    Faction: <span class="chosen-faction">?</span><br />
                                    Position: <span class="chosen-position">?</span>
                                </p>
                                <p class="center">
                                    <button class="claim" data-id="<?= $player->id ?>">Claim</button>
                                    <button class="unclaim" data-id="<?= $player->id ?>">Unclaim</button>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="factions draft-options">
                        <h3>Factions</h3>
                        <div class="options">
                            <?php foreach ($draft->factionPool as $faction) : ?>
                                <div class="faction option" data-homesystem="<?= $faction->homesystem() ?>" data-faction="<?= $faction->name ?>">
                                    <div>
                                        <img src="<?= url('img/factions/ti_' . $faction->id . '.png') ?>" /><br />

                                        <span><?= $faction->name ?></span><br />
                                        <a href="#" data-id="<?= $faction->id ?>" class="open-reference">[reference]</a>
                                        <a target="_blank" href="<?= $faction->linkToWiki ?>" class="more">[wiki]</a><br />
                                        <button class="draft" data-category="faction" data-value="<?= $faction->name ?>">Draft</button>
                                        <span class="drafted-by" data-category="faction" data-value="<?= $faction->name ?>"></span>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="slices draft-options">
                        <h3>Slices</h3>
                        <div class="options">
                            <?php foreach ($draft->slicePool as $sliceId => $slice) : ?>
                                <div class="slice option" data-slice="<?= $sliceId ?>">
                                    <div class="slice-graph">
                                        <div class="wrap">
                                            <?php foreach ($slice->tiles as $i => $tile) : ?>
                                                <img class="tile-<?= $i ?>" src="<?= url('img/tiles/ST_' . $tile->id . '.png') ?>" />
                                                <img class="zoom tile-<?= $i ?>" src="<?= url('img/tiles/ST_' . $tile->id . '.png') ?>" />
                                            <?php endforeach; ?>
                                            <img class="tile-h" src="<?= url('img/tiles/ST_0.png') ?>" />
                                        </div>
                                    </div>

                                    <div class="slice-info">
                                        <h4>Slice <?= $sliceId + 1 ?></h4>

                                        <div class="info">
                                            <?php foreach ($slice->specialties as $s) : ?>
                                                <img class="tech-specialty" title="<?= $s->value ?>" src="<?= url('img/tech/' . $s->value . '.webp') ?>" alt="<?= $s->value ?>" />
                                            <?php endforeach; ?>


                                            <?php foreach ($slice->legendaryPlanets as $l) : ?>
                                                <abbr class="legendary" title="<?= $l ?>"><img src="<?= url('img/legendary.webp') ?>"></abbr>
                                            <?php endforeach; ?>

                                            <?php foreach ($slice->wormholes as $w) : ?>
                                                <abbr class="wormhole" title="<?= $w->name ?>"><?= $w->symbol() ?></abbr>
                                            <?php endforeach; ?>
                                        </div>

                                        <p class="resource-count">
                                            Total:
                                            <abbr title="resources" class="resources"><?= $slice->totalResources ?></abbr>
                                            <abbr title="influence" class="influence"><?= $slice->totalInfluence ?></abbr>
                                        </p>

                                        <p class="resource-count">
                                            Optimal:
                                            <abbr title="resources" class="resources"><?= $slice->optimalResources ?></abbr>
                                            <abbr title="influence" class="influence"><?= $slice->optimalInfluence ?></abbr>
                                        </p>

                                        <p class="center">
                                            <button class="draft" data-category="slice" data-value="<?= $sliceId ?>">Draft</button>
                                            <span class="drafted-by" data-category="slice" data-value="<?= $sliceId ?>"></span>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>



                    <div class="positions draft-options">
                        <h3>Positions</h3>
                        <div class="options">
                            <?php for ($i = 0; $i < count($draft->players); $i++) : ?>
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
                        window.draft = <?= json_encode($draft->toArray(false)); ?>;
                    </script>
                </div>
            </div>

            <div class="tab" id="regen">
                <div class="content-wrap">
                    <?php if ($draft->canRegenerate()) : ?>
                        <p id="regen-options">
                            <label for="shuffle_slices"><input type="checkbox" checked id="shuffle_slices" name="shuffle_slices" /> New Slices</label>
                            <label for="shuffle_factions"><input type="checkbox" checked id="shuffle_factions" name="shuffle_factions" /> New Factions</label>
                            <label for="shuffle_order"><input type="checkbox" id="shuffle_order" name="shuffle_order" /> New <?= ($draft->settings->allianceTeamMode == \App\TwilightImperium\AllianceTeamMode::RANDOM) ? 'teams and ' : '' ?>player order</label>
                            <button id="regenerate" class="btn">Regenerate</button>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab" id="config">
                <div class="content-wrap">
                    <h3>Configuration used</h3>

                    <p>
                        <label>Number of Players:</label> <strong><?= count($draft->players) ?></strong>
                    </p>
                    <p>
                        <label>Use preset Draft Order:</label> <strong><?= yesno($draft->settings->presetDraftOrder) ?></strong>
                    </p>
                    <p>
                        <label>Number of Slices:</label> <strong><?= $draft->settings->numberOfSlices ?></strong>
                    </p>
                    <p>
                        <label>Number of Factions:</label> <strong><?= $draft->settings->numberOfFactions ?></strong>
                    </p>
                    <p>
                        <label>Faction sets included:</label> <strong><?= implode("<br />", $draft->settings->factionSetNames()) ?></strong>
                    </p>
                    <p>
                        <label>Tile sets included:</label> <strong><?= implode("<br />", $draft->settings->tileSetNames()) ?></strong>
                    </p>
                    <p>
                        <label>Include Council Keleres:</label> <strong><?= yesno($draft->settings->includeCouncilKeleresFaction) ?></strong>
                    </p>
                    <p>
                        <label>Minimum 2 alpha & beta wormholes:</label> <strong><?= yesno($draft->settings->minimumTwoAlphaAndBetaWormholes) ?></strong>
                    </p>
                    <p>
                        <label>Minimum amount of legendary planets:</label> <strong><?= $draft->settings->minimumLegendaryPlanets ?></strong>
                    </p>
                    <p>
                        <label>Max. 1 wormhole per slice:</label> <strong><?= yesno($draft->settings->maxOneWormholesPerSlice) ?></strong>
                    </p>
                    <hr />
                    <p>
                        <label>Minimum Optimal Influence:</label> <strong><?= $draft->settings->minimumOptimalInfluence ?></strong>
                    </p>
                    <p>
                        <label>Minimum Optimal Resources:</label> <strong><?= $draft->settings->minimumOptimalResources ?></strong>
                    </p>

                    <p>
                        <label>Minimum Optimal Total:</label> <strong><?= $draft->settings->minimumOptimalTotal ?></strong>
                    </p>

                    <p>
                        <label>Maximum Optimal Total:</label> <strong><?= $draft->settings->maximumOptimalTotal  ?></strong>
                    </p>

                    <p>
                        <label>Custom Factions:</label> <strong>
                            <?php if ($draft->settings->customFactions != null) : ?>
                                <?= implode(', ', $draft->settings->customFactions) ?>
                            <?php else : ?>
                                no
                            <?php endif; ?>
                        </strong>
                    </p>
                    <p>
                        <label>Custom Slices:</label> <strong>
                            <?php if ($draft->settings->customSlices != null) : ?>
                                <?php foreach ($draft->settings->customSlices as $slice) : ?>
                                    <?= implode(',', $slice) ?><br />
                                <?php endforeach; ?>
                            <?php else : ?>
                                no
                            <?php endif; ?>
                        </strong>
                    </p>
                    <p>
                        <label>Seed:</label> <strong><?= $draft->settings->seed->getValue() ?></strong>
                    </p>
                    <p>
                        <label>Slices Generated:</label>
                        <strong>
                            <?php foreach ($draft->slicePool as $slice_id => $slice) : ?>
                                <?= implode(',', $slice->tileIds()); ?><br />
                            <?php endforeach; ?>
                        </strong>
                    </p>
                    <?php if ($draft->settings->allianceMode) : ?>
                        <hr />
                        <h3>Alliance Configuration</h3>
                        <p>
                            <label>Team Creation:</label> <strong><?= ucfirst($draft->settings->allianceTeamMode->value) ?></strong>
                        </p>
                        <p>
                            <label>Force Teammates Position:</label> <strong><?= ucfirst($draft->settings->allianceTeamPosition->value) ?></strong>
                        </p>
                        <p>
                            <label>Force Team Double Picks:</label> <strong><?= yesno($draft->settings->allianceForceDoublePicks) ?></strong>
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
                        <div class="rotate-map">
                            <button title="Rotate counter-clockwise" class="rotate-map-left">⟲</button>
                            <button title="Rotate clockwise" class="rotate-map-right">⟳</button>
                        </div>
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
            <?php require_once 'templates/faq.php'; ?>
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
            "claim": "<?= url('api/claim') ?>",
            "pick": "<?= url('api/pick') ?>",
            "regenerate": "<?= url('api/regenerate') ?>",
            "tile_images": "<?= url('img/tiles') ?>",
            "data": "<?= url('api/draft/' . $draft->id) ?>",
            "undo": "<?= url('api/undo') ?>",
            "restore": "<?= url('api/restore') ?>"
        }
    </script>
    <script src="<?= asset_url('js/vendor.js') ?>"></script>
    <script src="<?= asset_url('js/draft.js') ?>"></script>
    <script src="<?= asset_url('js/generate-map.js') ?>"></script>
</body>

</html>
