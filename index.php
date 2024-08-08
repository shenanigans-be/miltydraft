<?php require_once 'boot.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TI4 - Milty Draft</title>
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
                                            <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Amy") ?>" />
                                        </div>
                                        <div class="input player">
                                            <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Ben") ?>" />
                                        </div>
                                    </div>
                                    <div class="alliance_team team_b">
                                        <p class="team_label">Team 2</p>
                                        <div class="input player">
                                            <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Charlie") ?>" />
                                        </div>
                                        <div class="input player">
                                            <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Desmond") ?>" />
                                        </div>
                                    </div>
                                    <div class="alliance_team team_c">
                                        <p class="team_label">Team 3</p>
                                        <div class="input player">
                                            <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Esther") ?>" />
                                        </div>
                                        <div class="input player">
                                            <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Frank") ?>" />
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

                                    <input type="number" id="num_slices" name="num_slices" value="7" max="13" />
                                    <span class="help">
                                        Note: The slices are random and not necessarily balanced (more on that below), so increasing this number makes it more relaxed for players to choose.<br />
                                        Number of players + 1 is generally recommended. Can't have more than 9 without DS+ tiles or 13 with DS+, cause you run out of tiles.
                                    </span>
                                </div>

                                <div class="input">
                                    <label for="num_slices">
                                        Number of Factions
                                    </label>

                                    <input type="number" id="num_factions" name="num_factions" value="9" max="17" />
                                    <span class="help">
                                        Note: Less options means more competitive drafting.<br />
                                        Number of players + 3 is kind of recommended, but this is all personal preference.
                                    </span>
                                </div>

                                <div class="input">
                                    <label for="pok" class="check">
                                        <input type="checkbox" name="include_pok" id="pok" checked /> Use Prophecy Of Kings Expansion
                                    </label>
                                    <span class="help">
                                        Include the factions and tiles from the Prophecy of Kings expansion.<br /><br />
                                        <strong>IMPORTANT NOTE: If you don't include PoK you can only organise drafts up to 5 players (because you can only generate 5 valid slices with the base-game tiles)!</strong>
                                    </span>
                                </div>
                                <div class="input">
                                    <label for="include_ds_tiles" class="check">
                                        <input type="checkbox" name="include_ds_tiles" id="include_ds_tiles" /> Use Discordant Stars Plus tiles
                                    </label>
                                    <span class="help">
                                        Include the new tiles from the Unofficial Discordant Stars Plus expansion.
                                    </span>
                                </div>

                                <h4>Draftable Factions:</h4>
                                <div class="input">
                                    <label for="basef" class="check">
                                        <input type="checkbox" class="draft-faction" data-num="17" data-set="base" name="include_base_factions" id="basef" checked /> Include Base Game
                                    </label>
                                    <label for="pokf" class="check">
                                        <input type="checkbox" class="draft-faction" data-num="7" data-set="pok" name="include_pok_factions" id="pokf" checked /> Include Prophecy Of Kings
                                    </label>
                                    <label for="keleres" class="check">
                                        <input type="checkbox" name="include_keleres" class="draft-faction" data-num="1" data-set="keleres" id="keleres" /> Include The Council Keleres
                                    </label>
                                    <span class="help">
                                        The Council Keleres was introduced in <a target="_blank" href="https://images-cdn.fantasyflightgames.com/filer_public/35/e1/35e10f37-4b6d-4479-a117-4e2c571ddfa7/ti_codex_volume_3_vigil_v2_1-compressed.pdf">Codex III</a>.
                                        (PoK required). For simplicity's sake I'll leave it up to each group to decide how they want to handle things (including the very limited possibility of all 3 flavours also being picked). Just something to keep in mind.
                                    </span>
                                    <label for="discordant" class="check">
                                        <input type="checkbox" name="include_discordant" class="draft-faction" data-num="24" data-set="discordant" id="discordant" /> Include Discordant Stars
                                    </label>
                                    <span class="help">
                                        <a target="_blank" href="https://www.reddit.com/r/twilightimperium/comments/pvbbie/discordant_stars_24_homebrew_factions/">Discordant Stars</a> is a fan made faction pack introduced by members of the Discord community.
                                    </span>
                                    <label for="discordantexp" class="check">
                                        <input type="checkbox" name="include_discordantexp" class="draft-faction" data-num="10" data-set="discordantexp" id="discordantexp" /> Include Discordant Stars Plus
                                    </label>
                                    <span class="help">
                                        Ten additional factions were added to Discordant Stars as an expansion: Bentor, Nokar, Gledge, Lanefir, Kyro, Ghoti, Kolume, Cheiran, Kjalengard, and Edyn.
                                    </span>
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
                                <h3>Map Generation</h3>
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
                                    <label for="legendary_0" class="check">
                                        <input type="radio" name="legendary" value="0" id="legendary_0" checked /> Include any amount (including none) of legendary planets
                                    </label>
                                    <label for="legendary_1" class="check">
                                        <input class="legendary-option" type="radio" name="legendary" value="1" id="legendary_1" /> Include at least 1 legendary planets
                                    </label>
                                    <label for="legendary_2" class="check">
                                        <input class="legendary-option" type="radio" name="legendary" value="2" id="legendary_2" /> Include both legendary planets
                                    </label>

                                    <span class="help">The legendaries, in case you were wondering, are Primor and Hope's end.</span>
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
                                        <?php require_once 'factions.php'; ?>
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

            <?php require_once 'faq.php'; ?>
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
            "generate": "<?= url('generate.php') ?>"
        }
    </script>

    <script src="<?= url('js/vendor.js?v=' . $_ENV['VERSION']) ?>"></script>
    <script src="<?= url('js/main.js?v=' . $_ENV['VERSION']) ?>"></script>
</body>

</html>