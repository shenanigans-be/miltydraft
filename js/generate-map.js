let HEX_SIZE = 100;
let HEX_WIDTH = HEX_SIZE * 2;
let HEX_HEIGHT = HEX_SIZE * Math.sqrt(3);

/**
 * A note on this stuff:
 *
 * Hex grids are beautiful but fucking complicated. Most of these functions are just lifted from https://www.redblobgames.com/grids/hexagons/
 * without which I'd still be breaking my head over this. For those following along at home: we're using Axial Coordinates and flat-topped hexes.
 *
 * If you ever think: "Oh hey I'll make my game with hexagonal tiles, they're better", you are correct, but you will regret being correct.
 *
 */

// map coordinates are built up like this: q, r, value
    // value is player index + tile index, so
    // 0-H is the 1st player's home system
    // M is mecatol rex
    // 3-4 is the 5th tile in the 4th player's slice
    // L-S-1 is the straight [S] hyperlane [L] rotated 1 times (= 60 degrees) (yeah, messy, I'm aware)

const HYPERLANES = {
    "S": "83A",
    "C": "85A",
    "T": "87A",
    "N": "84B",
    "K": "83B",
    "R": "87B",
    "L": "89B"
}

const MAP_3 = [
    [0, -3, "0-H"],
    [1, -3, "0-0"],
    [2, -3, "L-S-1"],
    [3, -3, "L-C-4"],

    [-1, -2, "0-2"],
    [0, -2, "0-1"],
    [1, -2, "L-T-0"],
    [2, -2, "0-3"],
    [3, -2, "L-S-2"],

    [-2, -1, "L-S-0"],
    [-1, -1, "L-T-2"],
    [0, -1, "0-4"],
    [1, -1, "L-C-4"],
    [2, -1, "L-T-4"],
    [3, -1, "1-2"],

    [-3, 0, "L-C-2"],
    [-2, 0, "2-3"],
    [-1, 0, "L-C-2"],
    [0, 0, "M"],
    [1, 0, "1-4"],
    [2, 0, "1-1"],
    [3, 0, "1-H"],

    [-3, 1, "L-S-2"],
    [-2, 1, "L-T-4"],
    [-1, 1, "2-4"],
    [0, 1, "L-C-0"],
    [1, 1, "L-T-2"],
    [2, 1, "1-0"],

    [-3, 2, "2-0"],
    [-2, 2, "2-1"],
    [-1, 2, "L-T-0"],
    [0, 2, "1-3"],
    [1, 2, "L-S-0"],

    [-3, 3, "2-H"],
    [-2, 3, "2-2"],
    [-1, 3, "L-S-1"],
    [0, 3, "L-C-0"],
];

const MAP_4 = [
    [0, -3, "0-H"],
    [1, -3, "0-0"],
    [2, -3, "1-2"],
    [3, -3, "1-H"],

    [-1, -2, "0-2"],
    [0, -2, "0-1"],
    [1, -2, "0-3"],
    [2, -2, "1-1"],
    [3, -2, "1-0"],

    [-2, -1, "L-S-0"],
    [-1, -1, "L-T-2"],
    [0, -1, "0-4"],
    [1, -1, "1-4"],
    [2, -1, "L-T-1"],
    [3, -1, "L-S-2"],

    [-3, 0, "L-C-2"],
    [-2, 0, "3-3"],
    [-1, 0, "L-C-2"],
    [0, 0, "M"],
    [1, 0, "L-C-5"],
    [2, 0, "1-3"],
    [3, 0, "L-C-5"],

    [-3, 1, "L-S-2"],
    [-2, 1, "L-T-4"],
    [-1, 1, "3-4"],
    [0, 1, "2-4"],
    [1, 1, "L-T-5"],
    [2, 1, "L-S-0"],

    [-3, 2, "3-0"],
    [-2, 2, "3-1"],
    [-1, 2, "2-3"],
    [0, 2, "2-1"],
    [1, 2, "2-2"],

    [-3, 3, "3-H"],
    [-2, 3, "3-2"],
    [-1, 3, "2-0"],
    [0, 3, "2-H"],
];

const MAP_5 = [
    [0, -3, "0-H"],
    [1, -3, "0-0"],
    [2, -3, "1-2"],
    [3, -3, "1-H"],

    [-1, -2, "0-2"],
    [0, -2, "0-1"],
    [1, -2, "0-3"],
    [2, -2, "1-1"],
    [3, -2, "1-0"],

    [-2, -1, "4-0"],
    [-1, -1, "4-3"],
    [0, -1, "0-4"],
    [1, -1, "1-4"],
    [2, -1, "1-3"],
    [3, -1, "2-2"],

    [-3, 0, "4-H"],
    [-2, 0, "4-1"],
    [-1, 0, "4-4"],
    [0, 0, "M"],
    [1, 0, "2-4"],
    [2, 0, "2-1"],
    [3, 0, "2-H"],

    [-3, 1, "4-2"],
    [-2, 1, "3-3"],
    [-1, 1, "3-4"],
    [0, 1, "L-C-0"],
    [1, 1, "L-T-2"],
    [2, 1, "2-0"],

    [-3, 2, "3-0"],
    [-2, 2, "3-1"],
    [-1, 2, "L-T-0"],
    [0, 2, "2-3"],
    [1, 2, "L-S-0"],

    [-3, 3, "3-H"],
    [-2, 3, "3-2"],
    [-1, 3, "L-S-1"],
    [0, 3, "L-C-0"],
];

const MAP_6 = [
    [0, -3, "0-H"],
    [1, -3, "0-0"],
    [2, -3, "1-2"],
    [3, -3, "1-H"],

    [-1, -2, "0-2"],
    [0, -2, "0-1"],
    [1, -2, "0-3"],
    [2, -2, "1-1"],
    [3, -2, "1-0"],

    [-2, -1, "5-0"],
    [-1, -1, "5-3"],
    [0, -1, "0-4"],
    [1, -1, "1-4"],
    [2, -1, "1-3"],
    [3, -1, "2-2"],

    [-3, 0, "5-H"],
    [-2, 0, "5-1"],
    [-1, 0, "5-4"],
    [0, 0, "M"],
    [1, 0, "2-4"],
    [2, 0, "2-1"],
    [3, 0, "2-H"],

    [-3, 1, "5-2"],
    [-2, 1, "4-3"],
    [-1, 1, "4-4"],
    [0, 1, "3-4"],
    [1, 1, "2-3"],
    [2, 1, "2-0"],

    [-3, 2, "4-0"],
    [-2, 2, "4-1"],
    [-1, 2, "3-3"],
    [0, 2, "3-1"],
    [1, 2, "3-2"],

    [-3, 3, "4-H"],
    [-2, 3, "4-2"],
    [-1, 3, "3-0"],
    [0, 3, "3-H"],
];

const MAP_7 = [
    [0, -4, "0-H"],
    [1, -4, "0-0"],

    [-1, -3, "0-2"],
    [0, -3, "0-1"],
    [1, -3, "L-K-0"],
    [2, -3, "1-2"],
    [3, -3, "1-H"],

    [-2, -2, "6-0"],
    [-1, -2, "6-3"],
    [0, -2, "0-4"],
    [1, -2, "0-3"],
    [2, -2, "1-1"],
    [3, -2, "1-0"],

    [-3, -1,  "6-H"],
    [-2, -1,  "6-1"],
    [-1, -1,  "6-4"],
    [0, -1,  "L-K-0"],
    [1, -1,  "1-4"],
    [2, -1,  "1-3"],
    [3, -1,  "2-2"],

    [-3, 0, "6-2"],
    [-2, 0, "5-3"],
    [-1, 0, "5-4"],
    [0, 0, "M"],
    [1, 0, "2-4"],
    [2, 0, "2-1"],
    [3, 0, "2-H"],

    [-4, 1, "5-0"],
    [-3, 1, "L-K-2"],
    [-2, 1, "5-1"],
    [-1, 1, "L-L-0"],
    [0, 1, "L-N-0"],
    [1, 1, "2-3"],
    [2, 1, "2-0"],

    [-4, 2, "5-H"],
    [-3, 2, "5-2"],
    [-2, 2, "4-3"],
    [-1, 2, "4-4"],
    [0, 2, "3-4"],
    [1, 2, "L-N-0"],

    [-3, 3, "4-0"],
    [-2, 3, "4-1"],
    [-1, 3, "3-3"],
    [0, 3, "3-1"],
    [1, 3, "3-2"],

    [-3, 4, "4-H"],
    [-2, 4, "4-2"],
    [-1, 4, "3-0"],
    [0, 4, "3-H"],
];

const MAP_8 = [
    [0, -4, "0-H"],
    [1, -4, "0-0"],
    [2, -4, "1-2"],
    [3, -4, "1-H"],

    [-1, -3, "0-2"],
    [0, -3, "0-1"],
    [1, -3, "0-3"],
    [2, -3, "1-1"],
    [3, -3, "1-0"],

    [-2, -2, "7-0"],
    [-1, -2, "7-3"],
    [0, -2, "0-4"],
    [1, -2, "1-4"],
    [2, -2, "1-3"],
    [3, -2, "2-2"],
    [4, -2, "2-H"],

    [-3, -1, "7-H"],
    [-2, -1, "7-1"],
    [-1, -1, "7-4"],
    [0, -1, "L-T-1"],
    [1, -1, "L-L-3"],
    [2, -1, "2-1"],
    [3, -1, "L-K-2"],
    [4, -1, "2-0"],

    [-3, 0, "7-2"],
    [-2, 0, "6-3"],
    [-1, 0, "6-4"],
    [0, 0, "M"],
    [1, 0, "2-4"],
    [2, 0, "2-3"],
    [3, 0, "3-2"],

    [-4, 1, "6-0"],
    [-3, 1, "L-K-2"],
    [-2, 1, "6-1"],
    [-1, 1, "L-L-0"],
    [0, 1, "L-T-4"],
    [1, 1, "3-4"],
    [2, 1, "3-1"],
    [3, 1, "3-H"],

    [-4, 2, "6-H"],
    [-3, 2, "6-2"],
    [-2, 2, "5-3"],
    [-1, 2, "5-4"],
    [0, 2, "4-4"],
    [1, 2, "3-3"],
    [2, 2, "3-0"],

    [-3, 3, "5-0"],
    [-2, 3, "5-1"],
    [-1, 3, "4-3"],
    [0, 3, "4-1"],
    [1, 3, "4-2"],

    [-3, 4, "5-H"],
    [-2, 4, "5-2"],
    [-1, 4, "4-0"],
    [0, 4, "4-H"],
]

const MAPS = {
    3: MAP_3,
    4: MAP_4,
    5: MAP_5,
    6: MAP_6,
    7: MAP_7,
    8: MAP_8,
}

window.map_cached = false;
let all_tiles = [];

function generate_map() {
    if(map_cached) {
        return;
    }

    let map_template = MAPS[draft.config.players.length];

    let map = "";
    all_tiles = [];

    let TTS_spiral = []

    for(let i = 1; i <= 4; i++) {
        TTS_spiral = TTS_spiral.concat(axial_ring([0, 0], i));
    }

    let TTS_string = [];
    for(const t in map_template) {
        let result =  draw_tile(map_template[t]);
        map += result.html;


        for(const i in TTS_spiral) {
            if(TTS_spiral[i][0] == map_template[t][0] && TTS_spiral[i][1] == map_template[t][1]) {
                let tts = result.tile;
                if(result.hyperlane) tts += result.rotate;
                TTS_string[i] = tts;
            }
        }
    }

    let speaker_order = [];
    for(let pid in draft.draft.players) {
        let p = draft.draft.players[pid];
        if(p.position != null) {
            speaker_order[p.position] = p.name;
        }
    }

    let slices_html = '';
    for(let i = 0; i < draft.config.players.length; i++) {
        let s = speaker_order[i];

        if(typeof(s) == 'undefined') {
            s = 'Unknown';
        }

        let tpl = [
            [-1, 0, i + '-3'],
            [0, -1, i + '-4'],
            [-1, 1, i + '-0'],
            [0, 0, i + '-1'],
            [1, 0, i + '-2'],
            [0, 1, i + '-H'],
        ];

        let result = '';
        for(let u = 0; u < tpl.length; u++) {
            result += draw_tile(tpl[u]).html;
        }

        slices_html += '<div class="slice"><h3>' + (i + 1) + ': ' + s + '</h3><div class="map-offset"><div class="map">' + result + '</div></div></div>';
    }



    for(let u = 0; u < TTS_string.length; u++) {
        if(typeof(TTS_string[u]) == 'undefined' || TTS_string[u] == "EMPTY") TTS_string[u] = 0;
    }


    $('.map-container').attr('data-p', draft.config.players.length)
    $('#map-wrap').html(map);
    $('#mapslices-wrap').html(slices_html);
    $('#tile-gather').html(all_tiles.sort(function(a, b) {
        if(isNaN(a)) a = a.toString().substr(0, a.length - 1);
        if(isNaN(b)) b = b.toString().substr(0, b.length - 1);

        return a - b;
    }).join(', '));
    $('#tts-string').html(TTS_string.join(' '));

    map_cached = true;
}

function lookup(player_index, tile_index) {
    for(const i in draft.draft.players) {

        let p = draft.draft.players[i];

        if(p.position == player_index) {

            if(tile_index == "H") {

                let tile = "0";

                if(p.faction != null) {
                    let h = $('[data-faction="' + p.faction + '"]').data('homesystem');
                    if(typeof(h) != 'undefined') {
                        tile = h;
                    }
                }

                return [tile, p.name];
            }
            else if(p.slice != null) {
                // huzzah!
                return [draft.slices[p.slice].tiles[tile_index], p.name];
            }
        }
    }

    return ["EMPTY", ordinal(parseInt(player_index) + 1)];
}

function draw_tile(tile) {
    // let coords = hex_to_pixel(tile[0], tile[1]);
    let tilename = "EMPTY";
    let rotation = 0;
    let chunks = tile[2].split('-');
    let label = chunks[0] + "-" + chunks[1];
    let is_hyperlane = false;

    if(chunks.length == 3 && chunks[0] == "L") {
        // hyperlane
        tilename = HYPERLANES[chunks[1]];
        is_hyperlane = true;
        rotation = parseInt(chunks[2]);
        label = tilename;
    } else {
        if(chunks[0] == "M") {
            tilename = 18;
            label = tilename;
        } else {
            let result = lookup(chunks[0], chunks[1]);
            tilename = result[0];
            rotation = 0;
            label = result[0];

            if(label == "EMPTY") label = (parseInt(chunks[0]) + 1) + "-" + chunks[1];

            if(chunks[1] == "H") {
                if(tilename == "EMPTY") tilename = "0";
                label = result[1];
            }
        }
    }

    if(tilename != 'EMPTY' && tilename != 0 && !all_tiles.includes(tilename)) all_tiles.push(tilename);

    let tile_image =  tilename + '.png';
    if(tile_image.substring(0, 2) != 'DS') tile_image = 'ST_' + tile_image;
    tile_image = window.routes.tile_images + '/' + tile_image;

    let html = '<img src="' + tile_image + '" data-rotate="' + rotation + '" data-q="' + tile[0] +'" data-r="' + tile[1] + '" /><img class="zoom" src="' + tile_image + '" data-rotate="' + rotation + '" data-q="' + tile[0] +'" data-r="' + tile[1] + '" /><span data-q="' + tile[0] +'" data-r="' + tile[1] + '">' + label + '</span>';

    return {
        html: html,
        tile: tilename,
        rotate: rotation,
        hyperlane: is_hyperlane,
        q: chunks[0],
        r: chunks[1]
    };
}

function hex_to_pixel(q, r) {
    var x = size * (3/2 * q);
    var y = size * (Math.sqrt(3)/2 * q  +  Math.sqrt(3) * r);
    return [x, y];
}



var axial_direction_vectors = [
    [1, 0], [0, 1], [-1, 1], [-1, 0], [0, -1], [1, -1]
];

function axial_scale(hex, factor) {
    return [hex[0] * factor, hex[1] * factor];
}

function axial_ring(center, radius) {
    var results = [];
    var hex = axial_add(center, axial_scale(axial_direction(4), radius));
    for(let i = 0; i < 6; i++) {
        for(let j = 0; j < radius; j++) {
            results.push(hex);
            hex = axial_neighbor(hex, i);
        }
    }
    return results;
}

function axial_direction(direction) {
    return axial_direction_vectors[direction];
}

function axial_add(hex, vec) {
    return [hex[0] + vec[0], hex[1] + vec[1]];
}

function axial_neighbor(hex, direction) {
    return axial_add(hex, axial_direction(direction))
}

function axial_spiral(center, radius) {
    var results = [center];
    for(let i = 1; i <= radius; i++) {
        results = results.concat(axial_ring(center, i));
    }
    return results;
}
