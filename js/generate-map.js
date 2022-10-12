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
    [1, -3, "1-0"],
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
        console.log('were good');
        return;
    }

    let map_template = MAPS[draft.config.players.length];
    console.log(map_template);

    let map = "";
    all_tiles = [];
    for(const t in map_template) {
        map += draw_tile(map_template[t]);
    }


    $('.map-container').attr('data-p', draft.config.players.length)
    $('#map-wrap').html(map);
    $('#tile-gather').html(all_tiles.sort().join(', '));
}

function lookup(player_index, tile_index) {
    // console.log(player_index, tile_index);
    // console.log( draft.draft.players);

    for(const i in draft.draft.players) {

        let p = draft.draft.players[i];
        // console.log(p);

        if(p.position == player_index) {
            if(tile_index == "H") {
                return ["0", p.name];
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

    if(chunks.length == 3 && chunks[0] == "L") {
        // hyperlane
        tilename = HYPERLANES[chunks[1]];
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
                tilename = "0";
                label = result[1];
            }
        }
    }

    if(tilename != 'EMPTY' && tilename != 0) all_tiles.push(tilename);

    return '<img src="' + window.routes.tile_images + '/ST_' + tilename + '.png' + '" data-rotate="' + rotation + '" data-q="' + tile[0] +'" data-r="' + tile[1] + '" /><img class="zoom" src="' + window.routes.tile_images + '/ST_' + tilename + '.png' + '" data-rotate="' + rotation + '" data-q="' + tile[0] +'" data-r="' + tile[1] + '" /><span data-q="' + tile[0] +'" data-r="' + tile[1] + '">' + label + '</span>'
}

function hex_to_pixel(q, r) {
    var x = size * (3/2 * q);
    var y = size * (Math.sqrt(3)/2 * q  +  Math.sqrt(3) * r);
    return [x, y];
}
