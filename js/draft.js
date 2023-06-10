$(document).ready(function() {

    $('#tabs nav a, .tabnav').on('click', function(e) {
        // e.preventDefault();
        let ref = $(this).attr('href');
        $('#tabs nav a, .tab').removeClass('active');
        $('#tabs nav a[href="' + ref + '"]').addClass('active');
        $('.tab' + ref).addClass('active');

        if($(this).attr('href') == '#map') {
            generate_map();
        }
    });

    $('.open-reference').on('click', function(e) {
        e.preventDefault();

        let base_url = $('#reference-popup img').data('base');
        $('#reference-popup img').attr('src', base_url + $(this).data('id') + '.jpg');
        $('#reference-popup').show();
    });


    $('.close-reference, #reference-popup img').on('click', function(e) {
        $('#reference-popup').hide();
    });

    $('.status .map').on('click', function(e) {
        $('#tabs nav a[href="#map"]').click();
    });

    $('#change-mapview').on('change', function(e) {
        $('.mapview.current').removeClass('current');
        $('.mapview#mapview-' + $(this).val()).addClass('current');
    });

    if(window.location.hash != '' && $('.tab' + window.location.hash).length != 0) {
        $('#tabs nav a[href="' + window.location.hash + '"]').click();
    }

    $('button.draft').on('click', function(e) {
        e.preventDefault();

        if(typeof(me) == 'undefined' && !IS_ADMIN) return;

        window.draft_pick = {
            'id': draft.id,
            'index': draft.draft.index,
            'player': (IS_ADMIN)? draft.draft.current : me.id,
            'category': $(this).data('category'),
            'value': $(this).data('value')
        };

        if(IS_ADMIN) {
            draft_pick.admin = localStorage.getItem('admin_' + draft.id);
        }

        $('#confirm-category').html(draft_pick.category);

        let show_value = draft_pick.value;

        if(draft_pick.category == "slice") {
            show_value = parseInt(draft_pick.value) + 1;
        } else if(draft_pick.category == "position") {
            show_value = (draft_pick.value == 0)? 'speaker' : ordinal(parseInt(draft_pick.value) + 1);
        }

        $('#confirm-value').html(show_value);

        $('#confirm-popup').show();
    })

    $('#confirm-cancel').on('click', function(e) {
        $('#confirm-popup').hide();
    });

    $('#confirm').on('click', function(e) {
        $('button.draft').hide();
        $('#confirm-popup').hide();

        loading();
        $.ajax({
            type: "POST",
            url: window.routes.pick,
            dataType: 'json',
            data: draft_pick,
            success: function(resp) {
                if(resp.error) {
                    $('#error-message').html(resp.error);
                    $('#error-popup').show();

                    loading(false);
                }

                if(resp.success) {
                    window.draft = resp.draft;
                    refresh();
                }
            }
        })
    });

    $('#close-error').on('click', function(e) {
        $('#error-popup').hide();
    })


    if(window.draft) {
        who_am_i();
        draft_status();



        if(!IS_ADMIN) {
            hide_regen();
        } else {
            $('#regenerate').on('click', function() {
                if(!$('#shuffle_factions').is(':checked') && !$('#shuffle_slices').is(':checked')) return;

                loading();
                $.ajax({
                    type: 'POST',
                    url: window.routes.regenerate,
                    dataType: 'json',
                    data: {
                        'regen': draft.id,
                        'admin': localStorage.getItem('admin_' + draft.id),
                        'shuffle_slices': $('#shuffle_slices').is(':checked'),
                        'shuffle_factions': $('#shuffle_factions').is(':checked'),
                    },
                    success: function(resp) {
                        console.log(resp);
                        if(resp.error) {
                            $('#error-message').html(resp.error);
                            $('#error-popup').show();
                            loading(false);
                        } else {
                            window.location.hash = '';
                            window.location.reload();
                        }

                    }
                });
            });
        }


        $('.claim').on('click', function(e) {
            $(this).hide();
            loading();
            $.ajax({
                type: 'POST',
                url: window.routes.claim,
                dataType: 'json',
                data: {
                    'draft': draft.id,
                    'player': $(this).data('id')
                },
                success: function(resp) {
                    if(resp.error) {
                        $('#error-message').html(resp.error);
                        $('#error-popup').show();
                        loading(false);
                    } else {
                        window.draft = resp.draft;
                        localStorage.setItem('draft_' + draft.id, resp.player);
                        refresh();
                    }

                }
            });
        });

        $('.unclaim').on('click', function(e) {
            $(this).hide();
            loading();
            $.ajax({
                type: 'POST',
                url: window.routes.claim,
                dataType: 'json',
                data: {
                    'draft': draft.id,
                    'player': $(this).data('id'),
                    'unclaim': 1
                },
                success: function(resp) {
                    if(resp.error) {
                        $('#error-message').html(resp.error);
                        $('#error-popup').show();
                        loading(false);
                    } else {
                        window.draft = resp.draft;
                        localStorage.removeItem('draft_' + draft.id);
                        refresh();
                    }
                }
            });
        })
    }
});

function refresh() {
    window.map_cached = false;
    who_am_i();
    draft_status();
    loading(false);
}

function who_am_i() {
    var player_id = localStorage.getItem('draft_' + draft.id);
    var admin_id =  localStorage.getItem('admin_' + draft.id);

    window.IS_ADMIN = (admin_id == draft.admin_pass);

    if(IS_ADMIN) $('#admin-msg').show();

    $('.unclaim').hide();

    if(player_id == null) {
        $('.you').hide();
        for(p_id in draft.draft.players) {
            let p = draft.draft.players[p_id];

            if (p.claimed == false) {
                $('.claim[data-id="' + p.id + '"]').show();
            } else {
                $('.claim[data-id="' + p.id + '"]').hide();
            }
        }
    } else {
        $('.claim').hide();
        let p = draft.draft.players[player_id];
        if(typeof(p) != 'undefined') {
            window.me = p;
            $('.you[data-id="' + me.id + '"]').show();
            $('.unclaim[data-id="' + me.id + '"]').show();
        }
    }
}

function loading(loading = true) {
    if(loading) {
        $('body').addClass('loading');
    } else {
        $('body').removeClass('loading');
    }
}

function find_player(id) {
    for(let i in draft.draft.players) {
        if(draft.draft.players[i].id == id) {
            return draft.draft.players[i];
        }
    }
}

function draft_status() {

    let current_player = find_player(draft.draft.current);

    let log = '';
    for(let i = 0; i < draft.draft.log.length; i++) {
        let log_item = draft.draft.log[i];
        let p = find_player(log_item.player);

        let show_value = log_item.value;

        if(log_item.category == "slice") {
            show_value = "slice " + (parseInt(log_item.value) + 1);
        } else if(log_item.category == "position") {
            show_value = (log_item.value == 0)? 'speaker' : ordinal(parseInt(log_item.value) + 1) + " position";
        }

        $('#player-' + p.id + ' .chosen-' + log_item.category).html(show_value);
        let $btn = $('button[data-category="' + log_item.category + '"][data-value="' + log_item.value + '"]');
        $('.drafted-by[data-category="' + log_item.category + '"][data-value="' + log_item.value + '"]').html(p.name).show();
        $btn.prop('disabled', true);
        $btn.parents('.option').addClass('picked');

        log += '<p><strong>' + p.name + '</strong> picked <strong>' + show_value + '</strong></p>';
    }

    if(log != '') {

        hide_regen();
    }

    $('#log-content').html(log);

    if(draft.done) {
        $('#turn').removeClass('show');
        $('#done').addClass('show');
    } else {
        $('#turn').addClass('show');
    }

    $('.player').removeClass('active');
    $('#player-' + current_player.id).addClass('active');

    if(IS_ADMIN) {
        $('button.draft').show();

        $('#current-name').html(current_player.name + "'s");
    } else {
        if(typeof(me) != 'undefined' &&  current_player.id == me.id) {
            // IT'S MY TURN!
            $('button.draft').show();
            $('#current-name').html('your');
        } else {
            $('button.draft').hide();
            $('#current-name').html(current_player.name + "'s");
        }
    }

    // filter the buttons
    if(current_player.position != null) {
        $('button.draft[data-category="position"]').hide();
    }
    if(current_player.faction != null) {
        $('button.draft[data-category="faction"]').hide();
    }
    if(current_player.slice != null) {
        $('button.draft[data-category="slice"]').hide();
    }
}

function hide_regen() {

    $('#tabs nav a[href="#regen"]').hide();
    $('.regen-help').hide();
}

function ordinal(number) {
    let ends = ['th','st','nd','rd','th','th','th','th','th','th'];
    if (((number % 100) >= 11) && ((number%100) <= 13))
        return number + 'th';
    else
        return number + ends[number % 10];
}
