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
                <form action="generate.php" method="post">
                    <div class="section">
                        <div class="content-wrap">
                            <div class="header">
                                <h3>Players</h3>
                                <p class="help">
                                    Choose the number of players and fill in their names. Draft order will be randomised.
                                </p>
                            </div>
                            <div class="content">
                                <div class="input">
                                    <label for="num_players">
                                        Number of players
                                    </label>
                                    <input type="number" name="num_players" id="num_players" value="6" min="3" max="8" required />
                                </div>

                                <div class="input player">
                                    <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Test") ?>" />
                                </div>
                                <div class="input player">
                                    <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Test") ?>" />
                                </div>
                                <div class="input player">
                                    <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Test") ?>" />
                                </div>
                                <div class="input player">
                                    <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Test") ?>" />
                                </div>
                                <div class="input player">
                                    <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Test") ?>" />
                                </div>
                                <div class="input player">
                                    <input type="text" placeholder="Player Name" name="player[]" value="<?= e(get('debug', false), "Test") ?>" />
                                </div>
                                <div class="input player">
                                    <input type="text" placeholder="Player Name" name="player[]" />
                                </div>
                                <div class="input player">
                                    <input type="text" placeholder="Player Name" name="player[]" />
                                </div>

                                <a class="btn small" href="#" id="add-player" title="Add Player">+</a>
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

                                <input type="number" id="num_slices" name="num_slices" value="7" max="9" />
                                <span class="help">
                                    Note: The slices are random and not necessarily balanced (more on that below), so increasing this number makes it more relaxed for players to choose.<br />
                                    Number of players + 1 is generally recommended. Can't have more than 9 cause you run out of tiles.
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
                                    <input type="checkbox" name="include_pok" id="pok" checked /> Include Prophecy Of Kings
                                </label>
                                <span class="help">Include the factions and tiles from the Prophecy of Kings expansion.</span>
                            </div>

                            <div class="input">
                                <label for="keleres" class="check">
                                    <input type="checkbox" name="include_keleres" id="keleres" /> Include The Council Keleres
                                </label>
                                <span class="help">
                                    The Council Keleres was introduced in <a href="https://images-cdn.fantasyflightgames.com/filer_public/35/e1/35e10f37-4b6d-4479-a117-4e2c571ddfa7/ti_codex_volume_3_vigil_v2_1-compressed.pdf">Codex III</a>.
                                    (PoK required)
                                </span>
                            </div>

                            <div class="input">
                                <label for="specials" class="check">
                                    <input type="checkbox" name="specials" id="specials" /> Map must include wormholes and Legendary Planets
                                </label>
                                <span class="help">Checking this box means that there will be at least 2 or 3 <span class="alpha">alpha</span> wormholes, 2 or 3 <span class="beta">beta</span> wormholes and 1 or 2 legendary planets, divided among the slices.</span>
                            </div>

                            <div class="input">
                                <label for="max_wormhole" class="check">
                                    <input type="checkbox" name="max_wormhole" id="max_wormhole" /> Max. 1 wormhole per slice
                                </label>
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
                                <span class="help">This is where the Slice-generation magic happens.</span>
                            </div>


                            <div class="content" id="advanced">
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

                                <div class="input">
                                    <label for="custom_tiles">
                                        Custom Factions<br />

                                        <span class="help">
                                            You can pre-select the factions that will be considered in the draft.<br /><br />
                                            If you don't check enough factions to fill up the draft (based on the number of factions above), the generator will add some random (unchecked) ones. <br />
                                            If you check more than enough (e.g: checking 10 factions when the draft only needs 8), a random selection will be made from the checked factions. <br /><br />
                                            This means that if you want to <strong>exclude</strong> certain factions from the draft, you need to check everything except the ones you wish to exclude, and we'll do the rest.<br /><br />
                                            <strong>Note: once you start fiddling with this, the above options to include/exclude PoK or Keleres will be ignored.</strong>
                                        </span>
                                    </label>

                                    <div class="input-group">
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

    <div id="loading">
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
