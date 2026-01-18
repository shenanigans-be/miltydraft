<?php
    $prefillNames =  get('debug', false) == true;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TI4 - Milty Draft</title>
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
    <div class="content-wrap">
        <h1>Milty Draft Generator</h1>
        <h2>for Twilight Imperium 4th edition</h2>
    </div>

    <div id="tabs">
        <nav>
            <div class="content-wrap">
                <div class="left">
                    <a class="active" href="#generator">Generator</a>
                </div>
                <div class="right">
                    <a href="#faq">FAQ</a>
                </div>
            </div>
        </nav>

        <div class="tab active" id="generator">
            <form id="generate-form" action="generate.php" method="post">
                <div class="section">
                    <div class="content-wrap">
                        <div class="header">
                            <div>
                                <h3>Players</h3>
                                <button id="enable_alliance_mode" type="button">
                                    Switch to Alliance Mode
                                </button>

                                <button class="alliance_only" id="disable_alliance_mode" type="button">
                                    Switch to Regular Mode
                                </button>
                            </div>
                            <p class="help">
                                Choose the number of players and fill in their names. Draft order will be randomised unless otherwise specified (in the advanced settings below).
                            </p>


                        </div>
                        <div class="content">

                            <div class="input">
                                <label for="num_players">
                                    Number of players
                                </label>
                                <input type="number" name="num_players" id="num_players" value="6" min="3" max="8" required />

                                <p class="error" id="even_player_number_error">
                                    You must use an equal number of players in alliance mode
                                </p>
                            </div>

                            <div class="players_inputs">
                                <div class="alliance_team team_a">
                                    <p class="team_label">Team 1</p>
                                    <div class="input player">
                                        <input type="text" placeholder="Player Name" name="player[]" value="<?php e($prefillNames, "Amy") ?>" />
                                    </div>
                                    <div class="input player">
                                        <input type="text" placeholder="Player Name" name="player[]" value="<?php e($prefillNames, "Ben") ?>" />
                                    </div>
                                </div>
                                <div class="alliance_team team_b">
                                    <p class="team_label">Team 2</p>
                                    <div class="input player">
                                        <input type="text" placeholder="Player Name" name="player[]" value="<?php e($prefillNames, "Charlie") ?>" />
                                    </div>
                                    <div class="input player">
                                        <input type="text" placeholder="Player Name" name="player[]" value="<?php e($prefillNames, "Desmond") ?>" />
                                    </div>
                                </div>
                                <div class="alliance_team team_c">
                                    <p class="team_label">Team 3</p>
                                    <div class="input player">
                                        <input type="text" placeholder="Player Name" name="player[]" value="<?php e($prefillNames, "Esther") ?>" />
                                    </div>
                                    <div class="input player">
                                        <input type="text" placeholder="Player Name" name="player[]" value="<?php e($prefillNames, "Frank") ?>" />
                                    </div>
                                </div>
                                <div class="alliance_team team_d">
                                    <p class="team_label">Team 4</p>
                                    <div class="input player">
                                        <input type="text" placeholder="Player Name" name="player[]" />
                                    </div>
                                    <div class="input player">
                                        <input type="text" placeholder="Player Name" name="player[]" />
                                    </div>
                                </div>
                            </div>

                            <a class="btn small" href="#" id="add-player" title="Add Player">+</a>
                        </div>
                    </div>


                </div>

                <div class="section">
                    <div class="content-wrap">
                        <div class="header">
                            <h3>Alliance Game Variant</h3>
                            <p class="help">
                                The "Alliance" Variant is an Optional play mode that was introduced in Codex II (Affinity)<br />
                                <a target="_blank" href="https://twilight-imperium.fandom.com/wiki/Alliance_Game_Variant">Learn more</a>
                            </p>
                        </div>
                        <div class="content">
                            <label for="alliance_toggle" class="check">
                                <input type="checkbox" name="alliance_on" id="alliance_toggle" value="1" /> Enabled
                            </label>
                            <div class="alliance_only alliance_settings">
                                <h4>Team creation</h4>
                                <label class="check">
                                    <input type="radio" name="alliance_teams" value="preset" checked />
                                    Preset teams
                                </label>
                                <label class="check">
                                    <input type="radio" name="alliance_teams" value="random" />
                                    Random teams
                                </label>
                                <br>
                                <h4>Force teammates position</h4>
                                <label class="check">
                                    <input type="radio" name="alliance_teams_position" value="none" checked />
                                    None
                                </label>
                                <label class="check">
                                    <input type="radio" name="alliance_teams_position" value="neighbors" />
                                    Neighbors
                                </label>
                                <label class="check">
                                    <input type="radio" name="alliance_teams_position" value="opposites" />
                                    Opposites
                                </label>
                                <br>
                                <h4>Force team double picks</h4>
                                <label class="check">
                                    <input type="radio" name="force_double_picks" value="false" checked />
                                    No
                                </label>
                                <label class="check">
                                    <input type="radio" name="force_double_picks" value="true" />
                                    Yes
                                </label>
                                <p class="help">Choose yes if you want both teammates to choose within the same category at once.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section">

                    <div class="content-wrap">
                        <div class="header">
                            <h3>Settings</h3>
                        </div>
                        <div class="content">
                            <div class="input">
                                <label for="num_slices">
                                    Number of Slices
                                </label>

                                <input type="number" id="num_slices" name="num_slices" value="7" max="15" />
                                <span class="help">
                                        Note: The slices are random and not necessarily balanced (more on that below), so increasing this number makes it more relaxed for players to choose.<br />
                                        Number of players + 1 is generally recommended. Maximum number of slices possible is 15 with PoK, TE, DS, DS+ selected.
                                    </span>
                            </div>

                            <div class="input">
                                <label for="num_slices">
                                    Number of Factions
                                </label>

                                <input type="number" id="num_factions" name="num_factions" value="9" />
                                <span class="help">
                                        Note: Less options means more competitive drafting.<br />
                                        Number of players + 3 is kind of recommended, but this is all personal preference.
                                    </span>
                            </div>


                            <h4>Expansions to use:</h4>
                            <span class="help">
                                You can seperately choose which expansions to use for tiles and factions later.
                            </span>
                            <div class="input">
                                <label for="expansion_pok" class="check">
                                    <input type="checkbox" data-toggle-expansion="PoK" id="expansion_pok" checked class="auto-enable" />Prophecy Of Kings
                                </label>
                                <label for="expansion_te" class="check">
                                    <input type="checkbox" data-toggle-expansion="TE" id="expansion_te" class="auto-enable" />Thunder's Edge
                                </label>
                                <label for="expansion_ds" class="check">
                                    <input type="checkbox" data-toggle-expansion="DS" id="expansion_ds" class="auto-enable" />Discordant Stars
                                </label>
                                <label for="expansion_dsplus" class="check">
                                    <input type="checkbox" data-toggle-expansion="DSPlus" id="expansion_dsplus" class="auto-enable" />Discordant Stars Plus
                                </label>
                                <span class="help">
                                    <a target="_blank" href="https://www.reddit.com/r/twilightimperium/comments/pvbbie/discordant_stars_24_homebrew_factions/">Discordant Stars</a> and Discordant Stars Plus are fan made expansion introduced by members of the Discord community.
                                </span>
                            </div>

                            <h4>Factions:</h4>
                            <span class="help">
                                Which faction sets should be included as possible draft options?
                            </span>
                            <div class="input">
                                <label for="factions_base" class="check">
                                    <input type="checkbox" class="draft-faction auto-enable" name="factionSets[BaseGame]" id="factions_base" checked /> Base Game Factions
                                </label>
                                <label for="factions_pok" class="check" data-expansion="PoK">
                                    <input type="checkbox" class="draft-faction auto-enable" name="factionSets[PoK]" id="factions_pok" checked /> Prophecy Of Kings
                                </label>
                                <label for="factions_keleres" class="check" data-expansion="PoK">
                                    <input type="checkbox" name="include_keleres" class="draft-faction" data-num="1" data-set="keleres" id="factions_keleres" /> Include The Council Keleres
                                </label>
                                <span class="help">
                                    The Council Keleres was introduced in <a target="_blank" href="https://images-cdn.fantasyflightgames.com/filer_public/35/e1/35e10f37-4b6d-4479-a117-4e2c571ddfa7/ti_codex_volume_3_vigil_v2_1-compressed.pdf">Codex III</a> and is included in Thunder's Edge.
                                </span>
                                <label for="factions_te" class="check" data-expansion="TE">
                                    <input type="checkbox" class="draft-faction auto-enable" name="factionSets[TE]" id="factions_te" /> Thunder's Edge
                                </label>
                                <label for="factions_ds" class="check" data-expansion="DS">
                                    <input type="checkbox" class="draft-faction auto-enable" name="factionSets[DS]" id="factions_ds" /> Discordant Stars
                                </label>
                                <label for="factions_dsplus" class="check" data-expansion="DSPlus">
                                    <input type="checkbox" class="draft-faction auto-enable" name="factionSets[DSPlus]" id="factions_dsplus" /> Discordant Stars Plus
                                </label>
                            </div>

                            <h4>Tiles:</h4>
                            <span class="help">
                                Which tile sets should be included to generate slices?
                            </span>
                            <div class="input">
                                <label for="tiles_base" class="check">
                                    <input type="checkbox" class="draft-tiles auto-enable" name="tileSets[BaseGame]" id="tiles_base" checked disabled /> Base Game Tiles
                                </label>
                                <label for="tiles_pok" class="check" data-expansion="PoK">
                                    <input type="checkbox" class="draft-tiles auto-enable" name="tileSets[PoK]" id="tiles_pok" checked /> Prophecy Of Kings
                                </label>
                                <label for="tiles_te" class="check" data-expansion="TE">
                                    <input type="checkbox" class="draft-tiles auto-enable" name="tileSets[TE]" id="tiles_te" /> Thunder's Edge
                                </label>
                                <label for="tiles_dsplus" class="check" data-expansion="DSPlus">
                                    <input type="checkbox" class="draft-tiles auto-enable" name="tileSets[DSPlus]" id="tiles_dsplus" /> Discordant Stars Plus
                                </label>
                            </div>

                            <div class="input">
                                <label for="game_name">
                                    Game Name
                                </label>
                                <input type="text" placeholder="Game Name" maxlength="100" name="game_name" id="game_name" />

                                <span class="help">
                                    Optional. To help you remember which draft is which, because after two or three drafts that gets confusing. If you leave this blank it will generate something random like "Operation Glorious Drama".
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="content-wrap">
                        <div class="header">
                            <h3>Slice Generation</h3>
                        </div>
                        <div class="content">
                            <h4>Wormholes</h4>
                            <div class="input">
                                <label for="wormholes" class="check">
                                    <input type="checkbox" name="wormholes" id="wormholes" /> Include at least 2 alpha and beta wormholes
                                </label>
                                <span class="help">So at least 4 in total, divided over the slices. A slice will never have two of the same wormholes.</span>
                            </div>
                            <div class="input">
                                <label for="max_wormhole" class="check">
                                    <input type="checkbox" name="max_wormhole" id="max_wormhole" /> Max. 1 wormhole per slice
                                </label>
                            </div>
                            <h4>Legendaries</h4>
                            <div class="input">
                                <label for="min_legendaries">
                                    Minimum amount of legendary planets
                                </label>
                                <input type="text" value="0" placeholder="Minimum amount of legendary planets" max="7" name="min_legendaries" id="min_legendaries" />
                                <span class="help">PoK and TE include <a target="_blank" href="https://twilight-imperium.fandom.com/wiki/Planets_and_Systems#Legendary_Planets">2 and 5 draftable legendary planets, respectively</a>. <br /> Discordant Stars has <a href="https://twilight-imperium.fandom.com/wiki/Uncharted_Space_Expansion_(UNOFFICIAL)#Legendary_Planets" target="_blank">5 more</a></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="content-wrap">

                        <div class="header">
                            <div>
                                <h3>Advanced Settings</h3>
                                <a href="#" class="btn" id="more">Show</a>
                            </div>
                            <span class="help">Take a peek under the hood.</span>
                        </div>


                        <div class="content" id="advanced">

                            <h4>Draft Order</h4>
                            <div class="input">

                                <label for="random_draft_order" class="check">
                                    <input type="checkbox" name="preset_draft_order" id="preset_draft_order" /> Use specified player order (don't randomise)
                                </label>
                            </div>
                            <h4>Slice generation</h4>
                            <p>
                                The “Optimal Value” of a planet is calculated by using the higher of its resource value and influence value as that value, and the other value as zero.
                                If both of the planet’s resource value and influence value are equal, half that value is used for both of its optimal values.
                                For example, Starpoint, a 3/1, is treated as 3/0, Corneeq, a 1/2, is treated as 0/2, and Rigel III, a 1/1, is treated as ½/½.
                            </p>

                            <div class="input">
                                <label for="min_inf">
                                    Minimum Optimal Influence
                                </label>

                                <input type="number" id="min_inf" step="0.5" min="0" name="min_inf" value="4" />
                            </div>
                            <div class="input">
                                <label for="min_res">
                                    Minimum Optimal Resources
                                </label>

                                <input type="number" id="min_res" required min="0" name="min_res" step="0.5" value="2.5" />
                            </div>
                            <div class="input">
                                <label for="min_total">
                                    Minimum Optimal Total
                                </label>

                                <input type="number" id="min_total" required min="0" name="min_total" step="0.5" value="9" />
                            </div>

                            <div class="input">
                                <label for="max_total">
                                    Maximum Optimal Total
                                </label>

                                <input type="number" id="max_total" required min="0" name="max_total" step="0.5" value="13" />
                            </div>

                            <div class="input">
                                <label for="custom_slices">
                                    Custom Slices
                                </label>

                                <textarea rows="10" name="custom_slices" id="custom_slices" placeholder="1, 2, 3, 4, 5<?= "\n" ?>6, 7, 8, 9, 10<?= "\n" ?>..."></textarea>

                                <span class="help">
                                        You can skip the slice-generation stuff by inputting your own slices. You can do this by listing the tiles in each slice, one per line, seperated by commas.<br />
                                        Note: The order within each line matters! Slices are laid out like this:<br /><br />
                                        <img class="slice-help" src="<?= url('img/slice-layout.png') ?>" /><br /> so the first slice listed will be to positioned top left of the home system, the second one top, third top right,...
                                    </span>
                            </div>

                            <h4>Seed</h4>
                            <div class="input">
                                <label for="seed">
                                    Random Seed (optional)
                                </label>

                                <input type="number" id="seed" name="seed" placeholder="Leave empty for random" />
                                <span class="help">
                                    Set an explicit seed for reproducible draft generation. If left empty, a random seed will be automatically generated. The seed will be displayed after generation, allowing any draft to be reproduced exactly.
                                </span>
                            </div>

                            <h4>Custom Factions</h4>
                            <div class="input">
                                <label>

                                        <span class="help">
                                            You can pre-select the factions that will be considered in the draft.<br /><br />
                                            If you don't check enough factions to fill up the draft (based on the number of factions above), the generator will add some random (unchecked) ones. <br />
                                            If you check more than enough (e.g: checking 10 factions when the draft only needs 8), a random selection will be made from the checked factions. <br /><br />
                                            This means that if you want to <strong>exclude</strong> certain factions from the draft, you need to check everything except the ones you wish to exclude, and we'll do the rest.<br /><br />
                                            <strong>Note: You can change the selectable factions by checking or unchecking the boxes up where it says "Draftable Factions".</strong>
                                        </span>

                                    <br />
                                    <span>
                                            <a href="#" id="select-all">Select All</a> / <a href="#" id="deselect-all">Deselect All</a>
                                        </span>
                                </label>

                                <div class="input-group factions">
                                    <?php require_once 'templates/factions.php'; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="input center content-wrap">
                    <p id="error">
                    </p>

                    <p>
                        <button type="submit" id="submit">Generate</button>
                    </p>
                </div>
            </form>
        </div>

        <?php require_once 'templates/faq.php'; ?>
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

<script>
    window.routes = {
        "generate": "<?= url('api/generate') ?>"
    }
</script>

<script src="<?= asset_url('js/vendor.js') ?>"></script>
<script src="<?= asset_url('js/main.js') ?>"></script>
</body>

</html>
