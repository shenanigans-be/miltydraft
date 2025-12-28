let advanced_open = false;
let alliance_mode = false;

const EDITIONS = {
    BASEGAME: {
        id: 'BaseGame',
        maxFactions: 17,
        maxSlices: 5,
        maxLegendaries: 0,
    },
    POK: {
        id: 'PoK',
        maxFactions: 7,
        maxSlices: 4,
        maxLegendaries: 2,
    },
    TE: {
        id: 'TE',
        maxFactions: 7,
        maxSlices: 3,
        maxLegendaries: 5
    },
    DS: {
        id: 'DS',
        maxFactions: 24,
        maxSlices: 0,
        maxLegendaries: 0
    },
    DSPLUS: {
        id: 'DSPlus',
        maxFactions: 10,
        maxSlices: 1,
        maxLegendaries: 5
    },
};

$(document).ready(function () {
    $('input[data-toggle-expansion]').each((_, el) => {
        toggleExpansion($(el));
    }).on('change', (e) => {
        toggleExpansion($(e.currentTarget))
    });

    // @todo initial check
    // @todo disable DS/DSPlus when Pok is disabled

    $('.draft-faction').on('change', faction_check);

    $('#tabs nav a').on('click', function (e) {
        e.preventDefault();
        $('#tabs nav a, .tab').removeClass('active');
        $(this).addClass('active');
        $('.tab' + $(this).attr('href')).addClass('active');
    });

    $('#more').on('click', function (e) {
        e.preventDefault();
        advanced_open = !advanced_open
        $('#advanced').slideToggle();
        if (advanced_open) {
            $(this).html('hide');
        } else {
            $(this).html('show');
        }
    });


    if (window.location.hash != '' && $('.tab' + window.location.hash).length != 0) {
        $('#tabs nav a[href="' + window.location.hash + '"]').click();
    }

    $('#select-all').on('click', function (e) {
        e.preventDefault();
        $('.custom_faction:not(:disabled)').prop('checked', true);
    });
    $('#deselect-all').on('click', function (e) {
        e.preventDefault();
        $('.custom_faction').prop('checked', false);
    });

    $('#add-player').on('click', function (e) {
        e.preventDefault();
        var step = $("#alliance_toggle").is(':checked') ? 2 : 1;
        $('#num_players').val(parseInt($('#num_players').val()) + step);
        update_player_count();
    })

    $('#generate-form').on('submit', function (e) {
        e.preventDefault();
        $('#error').hide();

        loading();

        let formData = new FormData();
        let values = $('form').serializeArray();
        for (let i = 0; i < values.length; i++) {
            formData.append(values[i].name, values[i].value);
        }

        // @todo json body
        const request = new XMLHttpRequest();
        request.open("POST", routes.generate);
        request.onreadystatechange = function () {
            if (request.readyState != 4) return;



            let data = JSON.parse(request.responseText);

            if (data.error) {
                $('#error').show().html(data.error);
                loading(false);
            } else if (request.status == 200) {
                localStorage.setItem('admin_' + data.id, data.admin);
                window.location.href = "d/" + data.id + '?fresh=1';
            }
            // alert("Success: " + r.responseText);
        };
        request.send(formData);

    });

    $('#enable_alliance_mode').on('click', function() {
        $('#alliance_toggle').prop('checked', true);
        update_alliance_mode();
    });
    $('#disable_alliance_mode').on('click', function() {
        $('#alliance_toggle').prop('checked', false);
        update_alliance_mode();
    });

    $("#alliance_toggle").on('change', update_alliance_mode);

    $("input[name='alliance_teams']").on('change', update_alliance_teams);

    update_alliance_mode();
    init_player_count();
});

function toggleExpansion($checkbox) {
    const expansion = $checkbox.data('toggle-expansion');
    $checkbox.is(':checked') ? enableExpansion(expansion) : disableExpansion(expansion);
}

function enableExpansion(expansion) {
    $('[data-expansion="' + expansion + '"]')
        .prop('disabled', false)
        .removeClass('disabled');
    $('.check[data-expansion="' + expansion + '"] input').each((_, el) => {
        $checkbox = $(el);
        $checkbox.prop('disabled', false)
        $checkbox.prop('checked', $checkbox.hasClass('auto-enable'))
    });

    if (expansion == 'PoK') {
        toggleDiscordantAvailability(true);
    }
}

function toggleDiscordantAvailability(pokIsEnabled) {

    if (pokIsEnabled) {
        $('[data-toggle-expansion="DS"]').prop('disabled', false).parent().removeClass('disabled');
        $('[data-toggle-expansion="DSPlus"]').prop('disabled', false).parent().removeClass('disabled');
    } else {
        $('[data-toggle-expansion="DS"]').prop('disabled', true).parent().addClass('disabled');
        $('[data-toggle-expansion="DSPlus"]').prop('disabled', true).parent().addClass('disabled');
        disableExpansion('DS');
        disableExpansion('DSPlus');
    }
}

function disableExpansion(expansion) {
    $('[data-expansion="' + expansion + '"]')
        .prop('disabled', true)
        .addClass('disabled');
    $('.check[data-expansion="' + expansion + '"] input').each((_, el) => {
        $checkbox = $(el);
        $checkbox.prop('disabled', true)
        $checkbox.prop('checked', false)
    });

    if (expansion == 'PoK') {
        toggleDiscordantAvailability(false);
    }
}

function update_alliance_mode() {
    alliance_mode = $('#alliance_toggle').is(':checked');

    if (alliance_mode) {
        var numPlayers = $("#num_players").val();
        if (numPlayers % 2 == 1) {
            $("#num_players").val(parseInt(numPlayers) + 1).trigger('change');
        }
        $("#num_players").attr("step", 2).attr("min", 4);
        $(".alliance_only input").prop("disabled", false);
        $(".alliance_only").show();
        $(".players_inputs").addClass("alliance_on");
        $('#enable_alliance_mode').hide();
        update_alliance_teams();
        init_player_count();
    }
    else {
        $("#num_players").attr("min", 3).attr("step", 1);
        $(".alliance_only input").prop("disabled", true);
        $(".alliance_only").hide();
        $(".players_inputs").removeClass("alliance_on");
        $('#enable_alliance_mode').show();
    }

}

function update_alliance_teams() {
    $(".players_inputs").toggleClass("alliance_preset_teams", $('input[name="alliance_teams"]:checked').val() == "preset");
}


function loading(loading = true) {
    if (loading) {
        $('body').addClass('loading');
    } else {
        $('body').removeClass('loading');
    }
}


function faction_check() {
    let sets = [];
    let max = 0;


    $('.draft-faction').each(function (i, el) {
        if ($(el).is(':checked')) {
            max += parseInt($(el).data('num'));
            $('.factions label[data-set="' + $(el).data('set') + '"]').removeClass('disabled');
            $('.factions label[data-set="' + $(el).data('set') + '"] input').prop('disabled', false);
        } else {
            $('.factions label[data-set="' + $(el).data('set') + '"]').addClass('disabled');
            $('.factions label[data-set="' + $(el).data('set') + '"] input').prop('disabled', true).prop('checked', false);
        }
    });


    $('#num_factions').attr('max', max);
}

function init_player_count() {
    $('#num_players').on('change', update_player_count);
    $('#num_players').on('keyup', update_player_count);
    update_player_count();
}

function update_player_count() {
    $('.player').show();


    let numPlayers = parseInt($('#num_players').val());
    $('#even_player_number_error').hide();

    console.log(alliance_mode);
    if(alliance_mode) {
        // validate
        if(numPlayers % 2 != 0) {
            $('#even_player_number_error').show();
            $('.alliance_team').hide();
        } else {
            $('.alliance_team').show();
            console.log((Math.ceil(numPlayers / 2) - 1));
            $('.alliance_team:gt(' + (Math.ceil(numPlayers / 2) - 1) + ')').hide();
        }
    }

    numPlayers = Math.max(3, Math.min(8, numPlayers));
    $('#num_players').val(numPlayers);
    $('#add-player').toggle(numPlayers < 8);
    $('.player:gt(' + (numPlayers - 1) + ')').hide().find('input').val('');
}
