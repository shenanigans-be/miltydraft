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

    })

    $('form').on('submit', function(e) {
        e.preventDefault();
        $('#error').hide()
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
            } else {
                localStorage.setItem('admin_' + data.id, data.admin);

                window.location.href = "draft/" + data.id;
            }
            // alert("Success: " + r.responseText);
        };
        request.send(formData);

    });
});

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
    $('.player:gt(' + ($('#num_players').val() - 1) + ')').hide();
}
