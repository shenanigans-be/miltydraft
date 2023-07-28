let advanced_open = false;
$(document).ready(function() {
    init_player_count();

    pok_check();
    $('#pok').on('change', pok_check);
    $('.draft-faction').on('change', faction_check);

    $('#tabs nav a').on('click', function(e) {
        e.preventDefault();
        $('#tabs nav a, .tab').removeClass('active');
        $(this).addClass('active');
        $('.tab' + $(this).attr('href')).addClass('active');
    });

    $('#more').on('click', function(e) {
        e.preventDefault();
        advanced_open = !advanced_open
        $('#advanced').slideToggle();
        if(advanced_open) {
            $(this).html('hide');
        } else {
            $(this).html('show');
        }
    });


    if(window.location.hash != '' && $('.tab' + window.location.hash).length != 0) {
        $('#tabs nav a[href="' + window.location.hash + '"]').click();
    }

    $('#select-all').on('click', function(e) {
        e.preventDefault();
        $('.custom_faction:not(:disabled)').prop('checked', true);
    });
    $('#deselect-all').on('click', function(e) {
        e.preventDefault();
        $('.custom_faction').prop('checked', false);
    });

    $('#add-player').on('click', function(e) {
        e.preventDefault();
        $('#num_players').val(parseInt($('#num_players').val()) + 1);
        update_player_count();
    })

    $('#generate-form').on('submit', function(e) {
        e.preventDefault();
        $('#error').hide();

        loading();

        let formData = new FormData();
        let values = $('form').serializeArray();
        for(let i = 0; i < values.length; i++) {
            formData.append(values[i].name, values[i].value);
        }

        const request = new XMLHttpRequest();
        request.open("POST", routes.generate);
        request.onreadystatechange = function () {
            if (request.readyState != 4 || request.status != 200) return;

            let data = JSON.parse(request.responseText);

            if(data.error) {
                $('#error').show().html(data.error);
                loading(false);
            } else {
                localStorage.setItem('admin_' + data.id, data.admin);

                window.location.href = "d/" + data.id;
            }
            // alert("Success: " + r.responseText);
        };
        request.send(formData);

    });
});


function loading(loading = true) {
    if(loading) {
        $('body').addClass('loading');
    } else {
        $('body').removeClass('loading');
    }
}

function pok_check() {
    let $pokf = $('#pokf');
    let $keleres = $('#keleres');
    let $legendary_options = $('.legendary-option');
    let $DSTiles = $('#DSTiles');
    let $discordant = $('#discordant');
    let $discordantexp = $('#discordantexp');

    if($('#pok').is(':checked')) {

        // When POK is checked, allow POK dependant items to be selectable
        $pokf.prop('disabled', false);
        $pokf.parent().removeClass('disabled'); // I think these all share the same parent now, so additional lines might be moot.

        $keleres.prop('disabled', false);
        $keleres.parent().removeClass('disabled');

        $legendary_options.prop('disabled', false);
        $legendary_options.parent().removeClass('disabled');

        $DSTiles.prop('disabled', false);
        $DSTiles.parent().removeClass('disabled');

        $discordant.prop('disabled', false);
        $discordant.parent().removeClass('disabled');

        $discordantexp.prop('disabled', false);
        $discordantexp.parent().removeClass('disabled');
    } else {

        // When POK is not checked, disable options that depend on POK
        $pokf.prop('checked', false)
            .prop('disabled', true);
        $pokf.parent().addClass('disabled');

        $keleres.prop('checked', false)
            .prop('disabled', true);
        $keleres.parent().addClass('disabled');

        $legendary_options.prop('checked', false)
            .prop('disabled', true);
        $legendary_options.parent().addClass('disabled');

        $discordant.prop('checked', false)
            .prop('disabled', true);
        $discordant.parent().addClass('disabled');

        $discordantexp.prop('checked', false)
            .prop('disabled', true);
        $discordantexp.parent().addClass('disabled');
    }

    faction_check();
}


function faction_check() {
    let sets = [];
    let max = 0;


    $('.draft-faction').each(function (i, el) {
        if($(el).is(':checked')) {
            max += parseInt($(el).data('num'));
            $('.factions label[data-set="' + $(el).data('set') +'"]').removeClass('disabled');
            $('.factions label[data-set="' + $(el).data('set') +'"] input').prop('disabled', false);
        } else {
            $('.factions label[data-set="' + $(el).data('set') +'"]').addClass('disabled');
            $('.factions label[data-set="' + $(el).data('set') +'"] input').prop('disabled', true).prop('checked', false);
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

    let num_players = parseInt($('#num_players').val());

    num_players = Math.max(3, Math.min(8, num_players));
    $('#num_players').val(num_players);
    $('#add-player').toggle(num_players < 8);
    $('.player:gt(' + (num_players - 1) + ')').hide().find('input').val('');
}
