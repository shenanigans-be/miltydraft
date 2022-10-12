let advanced_open = false;
$(document).ready(function() {
    init_player_count();

    pok_check();
    $('#pok').on('change', pok_check);

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

    $('#select-all').on('click', function(e) {
        e.preventDefault();
        $('.custom_faction').prop('checked', true);
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

    $('form').on('submit', function(e) {
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
    let $keleres = $('#keleres');
    let $specials = $('#specials');
    let $max_factions = $('#num_factions');

    if($('#pok').is(':checked')) {
        $max_factions.attr("max", 24);

        $keleres.prop('disabled', false);
        $keleres.parent().removeClass('disabled');

        $specials.prop('disabled', false);
        $specials.parent().removeClass('disabled');
    } else {
        $max_factions.attr("max", 17);

        $keleres.prop('checked', false)
            .prop('disabled', true);
        $keleres.parent().addClass('disabled');


        $specials.prop('checked', false)
            .prop('disabled', true);
        $specials.parent().addClass('disabled');
    }
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
