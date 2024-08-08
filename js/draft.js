$(document).ready(function () {

    $('#tabs nav a, .tabnav').on('click', function (e) {
        let ref = $(this).attr('href');
        $('#tabs nav a, .tab').removeClass('active');
        $('#tabs nav a[href="' + ref + '"]').addClass('active');
        $('.tab' + ref).addClass('active');

        if ($(this).attr('href') == '#map') {
            generate_map();
        }

        if ($(this).attr('href') == '#session') {
            session_status();
            $('#secret-form').on('submit', restoreSession);
        } else {
            $('#secret-form').off('click');
        }
    });


    $('.close-popup').on('click', function(e) {
        $(this).parents('.popup').removeClass('open');
    })

    $('.open-reference').on('click', function (e) {
        e.preventDefault();

        let base_url = $('#reference-popup img').data('base');
        $('#reference-popup img').attr('src', base_url + $(this).data('id') + '.jpg');
        $('#reference-popup').show();
    });

    document.addEventListener("visibilitychange", function (e) {
        if (document.visibilityState === "visible") {
            refreshData();
        }
    });


    $('.close-reference, #reference-popup img').on('click', function (e) {
        $('#reference-popup').hide();
    });

    $('.status .view-map').on('click', function (e) {
        $('#tabs nav a[href="#map"]').click();
    });

    $('#change-mapview').on('change', function (e) {
        $('.mapview.current').removeClass('current');
        $('.mapview#mapview-' + $(this).val()).addClass('current');
    });

    if (window.location.hash != '' && $('.tab' + window.location.hash).length != 0) {
        $('#tabs nav a[href="' + window.location.hash + '"]').click();
    }

    $('button.draft').on('click', function (e) {
        e.preventDefault();

        if (typeof (me) == 'undefined' && !IS_ADMIN) return;

        window.draft_pick = {
            'id': draft.id,
            'index': draft.draft.log.length,
            'player': (IS_ADMIN) ? draft.draft.current : me.id,
            'secret': localStorage.getItem('secret_' + draft.id),
            'category': $(this).data('category'),
            'value': $(this).data('value')
        };

        if (IS_ADMIN) {
            draft_pick.admin = localStorage.getItem('admin_' + draft.id);
        }

        $('#confirm-category').html(draft_pick.category);

        let show_value = draft_pick.value;

        if (draft_pick.category == "slice") {
            show_value = parseInt(draft_pick.value) + 1;
        } else if (draft_pick.category == "position") {
            show_value = (draft_pick.value == 0) ? 'speaker' : ordinal(parseInt(draft_pick.value) + 1);
        }

        $('#confirm-value').html(show_value);

        $('#confirm-popup').show();
    })

    $('#confirm-cancel').on('click', function (e) {
        $('#confirm-popup').hide();
    });

    $('#confirm').on('click', function (e) {
        $('button.draft').hide();
        $('#confirm-popup').hide();

        loading();
        $.ajax({
            type: "POST",
            url: window.routes.pick,
            dataType: 'json',
            data: draft_pick,
            success: function (resp) {
                if (resp.error) {
                    $('#error-message').html(resp.error);
                    $('#error-popup').show();

                    loading(false);
                }

                if (resp.success) {
                    window.draft = resp.draft;
                    refresh();
                }
            }
        })
    });

    $('#close-error').on('click', function (e) {
        $('#error-popup').hide();
    })

    if (window.draft) {
        who_am_i();
        draft_status();

        if (!IS_ADMIN) {
            hide_regen();
        } else {

            // if it's the first time we're opening this draft as the admin, show the passkey popup
            if(localStorage.getItem('admin_popup_' + draft.id) == null) {

                session_status();
                $('#session-popup').addClass('open');
                localStorage.setItem('admin_popup_' + draft.id, true);
            }

            $('#regenerate').on('click', function () {
                if (!$('#shuffle_factions').is(':checked') && !$('#shuffle_slices').is(':checked') && !$('#shuffle_order').is(':checked')) {
                    return;
                };

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
                        'shuffle_order': $('#shuffle_order').is(':checked'),
                    },
                    success: function (resp) {
                        if (resp.error) {
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

            $(".undo-last-action").on('click', function () {
                loading();
                $.ajax({
                    type: 'POST',
                    url: window.routes.undo,
                    dataType: 'json',
                    data: {
                        'draft': draft.id,
                        'admin': localStorage.getItem('admin_' + draft.id),
                    },
                    success: function (resp) {
                        if (resp.error) {
                            $('#error-message').html(resp.error);
                            $('#error-popup').show();
                            loading(false);
                        } else {
                            window.draft = resp.draft;
                            reset_draft();
                            refresh();
                        }
                    }
                });
            });
        }


        $('.claim').on('click', function (e) {
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
                success: function (resp) {
                    if (resp.error) {
                        $('#error-message').html(resp.error);
                        $('#error-popup').show();
                        loading(false);
                    } else {
                        $('#session-popup #user').show();
                        window.draft = resp.draft;
                        localStorage.setItem('draft_' + draft.id, resp.player);
                        $('#session-popup').addClass('open');
                        $('#popup-passkey').html(resp.secret);
                        localStorage.setItem('secret_' + draft.id, resp.secret);
                        refresh();
                    }

                }
            });
        });

        $('.unclaim').on('click', function (e) {
            $(this).hide();
            loading();
            $.ajax({
                type: 'POST',
                url: window.routes.claim,
                dataType: 'json',
                data: {
                    'draft': draft.id,
                    'player': localStorage.getItem('draft_' + draft.id),
                    'secret': localStorage.getItem('secret_' + draft.id),
                    'unclaim': 1
                },
                success: function (resp) {
                    if (resp.error) {
                        $('#error-message').html(resp.error);
                        $('#error-popup').show();
                        loading(false);
                    } else {
                        window.draft = resp.draft;
                        localStorage.removeItem('draft_' + draft.id);
                        localStorage.removeItem('secret_' + draft.id);
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
    session_status();
    loading(false);
}

function who_am_i() {
    var player_id = localStorage.getItem('draft_' + draft.id);
    var admin_id = localStorage.getItem('admin_' + draft.id);

    window.IS_ADMIN = !!admin_id;

    if (IS_ADMIN) $('#admin-msg').show();

    $('.unclaim').hide();

    // Check for faulty local storage
    if (player_id && draft.draft.players[player_id].claimed == false) {
        localStorage.removeItem('draft_' + draft.id);
        localStorage.removeItem('secret_' + draft.id);
        player_id = null;
    }

    if (player_id == null) {
        $('.you').hide();
        for (p_id in draft.draft.players) {
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
        if (typeof (p) != 'undefined') {
            window.me = p;
            $('.you[data-id="' + me.id + '"]').show();
            $('.unclaim[data-id="' + me.id + '"]').show();
        }
    }
}

function loading(loading = true) {
    if (loading) {
        $('body').addClass('loading');
    } else {
        $('body').removeClass('loading');
    }
}

function find_player(id) {
    return draft.draft.players[id];
}

function refreshData() {
    loading(true);

    $.ajax({
        type: "GET",
        url: window.routes.data,
        dataType: 'json',
        data: {
            'draft': draft.id,
        },
        success: function (resp) {
            if (resp.error) {
                $('#error-message').html(resp.error);
                $('#error-popup').show();
                loading(false);
            } else {
                window.draft = resp.draft;
                refresh();

                // if we're looking at the map, regen it
                if (window.location.hash == '#map') {
                    generate_map();
                }
            }
        }
    })
}

function restoreSession(e) {
    e.preventDefault();
    loading(true);
    let $inputEl = $(this).find('input');
    let secret = $inputEl.val();

    if (!secret) return;

    $.ajax({
        type: "POST",
        url: window.routes.restore,
        dataType: 'json',
        data: {
            'draft': draft.id,
            'secret': secret,
        },
        success: function (resp) {
            if (resp.error) {
                $('#error-message').html(resp.error);
                $('#error-popup').show();
                loading(false);
            } else {
                if (resp.admin) {
                    localStorage.setItem('admin_' + draft.id, resp.admin);
                } else {
                    localStorage.setItem('draft_' + draft.id, resp.player);
                    localStorage.setItem('secret_' + draft.id, resp.secret);
                }
                $inputEl.val('');
                refresh();
            }
        }
    })
}

function draft_status() {

    let current_player = find_player(draft.draft.current);

    let log = '';
    for (let i = 0; i < draft.draft.log.length; i++) {
        let log_item = draft.draft.log[i];
        let p = find_player(log_item.player);

        let show_value = log_item.value;

        if (log_item.category == "slice") {
            show_value = "slice " + (parseInt(log_item.value) + 1);
        } else if (log_item.category == "position") {
            show_value = (log_item.value == 0) ? 'speaker' : ordinal(parseInt(log_item.value) + 1) + " position";
        }

        $('#player-' + p.id + ' .chosen-' + log_item.category).html(show_value);
        let $btn = $('button[data-category="' + log_item.category + '"][data-value="' + log_item.value + '"]');
        $('.drafted-by[data-category="' + log_item.category + '"][data-value="' + log_item.value + '"]').html(p.name).show();
        $btn.prop('disabled', true);
        $btn.parents('.option').addClass('picked');

        log += '<p><strong>' + p.name + '</strong> picked <strong>' + show_value + '</strong></p>';
    }

    if (log != '') {
        if (IS_ADMIN) $(".undo-last-action").show();
        hide_regen();
    }

    $('#log-content').html(log);

    if (draft.done) {
        $('#turn').removeClass('show');
        $('#done').addClass('show');
    } else {
        $('#turn').addClass('show');
    }

    $('.player').removeClass('active');
    $('#player-' + current_player.id).addClass('active');

    if (IS_ADMIN) {
        $('button.draft').show();

        $('#current-name').html(current_player.name + "'s");
    } else {
        if (typeof (me) != 'undefined' && current_player.id == me.id) {
            // IT'S MY TURN!
            $('button.draft').show();
            $('#current-name').html('your');
        } else {
            $('button.draft').hide();
            $('#current-name').html(current_player.name + "'s");
        }
    }

    // filter the buttons
    if (current_player.position != null) {
        $('button.draft[data-category="position"]').hide();
    }
    if (current_player.faction != null) {
        $('button.draft[data-category="faction"]').hide();
    }
    if (current_player.slice != null) {
        $('button.draft[data-category="slice"]').hide();
    }

    // Forcing team positions logic
    if (current_player.position == null && draft.config.alliance && draft.config.alliance["alliance_teams_position"] != 'none') {
        var allowedValues = [...Array(draft.config.players.length).keys()];
        for (let i in draft.draft.players) {
            let p = draft.draft.players[i];
            let numberOfPlayers = +draft.config.players.length;
            let oppositePosition = +((+p.position + (numberOfPlayers / 2)) % numberOfPlayers);
            let neighborsPositions = [+((+p.position + 1) % numberOfPlayers), +((+p.position - 1 + numberOfPlayers) % numberOfPlayers)];
            if (p.id != current_player.id && p.position && p.team == current_player.team) {
                if (draft.config.alliance["alliance_teams_position"] == 'opposites') {
                    allowedValues = [oppositePosition];
                }
                if (draft.config.alliance["alliance_teams_position"] == 'neighbors') {
                    allowedValues = allowedValues.filter(e => neighborsPositions.includes(e));
                }
            }
            else if (p.id != current_player.id && p.position && p.team != current_player.team) {
                if (draft.config.alliance["alliance_teams_position"] == 'opposites') {
                    allowedValues = allowedValues.filter(e => e != oppositePosition);
                }
                if (draft.config.alliance["alliance_teams_position"] == 'neighbors') {
                    let partner = getPartner(p.id);
                    if (partner.position == null && getPlayerInPosition(neighborsPositions[0])) {
                        allowedValues = allowedValues.filter(e => e != neighborsPositions[1]);
                    }
                    if (partner.position == null && getPlayerInPosition(neighborsPositions[1])) {
                        allowedValues = allowedValues.filter(e => e != neighborsPositions[0]);
                    }
                }
            }
        }
        $('button.draft[data-category="position"]').hide();
        var selector = 'button.draft[data-category="position"][data-value="' + allowedValues.join('"],button.draft[data-category="position"][data-value="') + '"]';
        $(selector).show();
    }

    // Force teammates to pick within the same category back to back
    if (draft.config.alliance && draft.config.alliance["force_double_picks"]) {
        let partner = getPartner(current_player.id);
        if (current_player.faction == null && partner.faction != null) {
            $('button.draft[data-category="position"]').hide();
            $('button.draft[data-category="slice"]').hide();
        }
        if (current_player.slice == null && partner.slice != null) {
            $('button.draft[data-category="faction"]').hide();
            $('button.draft[data-category="position"]').hide();
        }
        if (current_player.position == null && partner.position != null) {
            $('button.draft[data-category="faction"]').hide();
            $('button.draft[data-category="slice"]').hide();
        }
    }
}

function session_status() {
    console.log('sess')
    const admin = localStorage.getItem('admin_' + draft.id);
    const secret = localStorage.getItem('secret_' + draft.id);

    if (!admin && !secret) {
        $('#current-session').hide();
        return;
    }

    $('#current-session').show();
    if (admin) {
        $('#session-popup #admin').show();
        $('#session-popup #admin #popup-admin-passkey').text(admin);
        $('#current-session-admin').show();
        $('#current-session-admin').find('strong').text(admin);
    } else {
        $('#current-session-admin').hide();
    }

    if (secret) {
        $('#current-session-player').show();
        $('#current-session-player').find('strong').text(secret);
    } else {
        $('#session-popup #user').hide();
        $('#current-session-player').hide();
    }

}

function hide_regen() {

    $('#tabs nav a[href="#regen"]').hide();
    $('.regen-help').hide();
}

function ordinal(number) {
    let ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
    if (((number % 100) >= 11) && ((number % 100) <= 13))
        return number + 'th';
    else
        return number + ends[number % 10];
}

function reset_draft() {
    // Reset displayed choices
    $(".chosen-slice, .chosen-faction, .chosen-position").html("?");
    $('.drafted-by').html("").hide();
    $('button.draft').prop('disabled', false);
    $('.option').removeClass('picked');
    $(".undo-last-action").hide();
    $('#done').removeClass('show');
}

function getPartner(playerId) {
    let player = draft.draft.players[playerId];
    for (let i in draft.draft.players) {
        let p = draft.draft.players[i];
        if (p.id != playerId && player.team == p.team) {
            return p;
        }
    }
    // Throw an error here? Shouldn't happen if the draft is set up correctly
}

function getPlayerInPosition(position) {
    for (let i in draft.draft.players) {
        let p = draft.draft.players[i];
        if (p.position == position) {
            return p;
        }
    }
    return null;
}
