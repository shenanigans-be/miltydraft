<?php
    require_once 'boot.php';

    $id = get('id');
    $index = get('index');
    $player = get('player');
    $category = get('category');
    $value = get('value');

    $draft = get_draft($id);

    $is_admin = ($draft['admin_pass'] == get('admin'));
    if($draft == null) return_error('draft not found');
    if($player != $draft['draft']['current'] && !$is_admin) return_error('Not your turn!');

    if($index != $draft['draft']['index']) {
        return_error('Draft data out of date, meaning: stuff has been picked while this tab was open.');
    }

    foreach($draft['draft']['log'] as $logItem) {
        if($logItem['player'] == $player && $logItem['category'] == $category) {
            return_error('You already picked one of these.');
        }
        if($logItem['category'] == $category && $logItem['value'] == $value) {
            return_error('This was already picked.');
        }
    }

    $draft['draft']['log'][] = [
        'player' => $player,
        'category' => $category,
        'value' => $value
    ];

    $draft['draft']['players'][$player][$category] = $value;
    $draft['draft']['index']++;

    if($draft['draft']['index'] >= (count($draft['draft']['players']) * 3)) {
        $draft['done'] = true;
    } else {
        $draft['done'] = false;
    }

    if(!$draft['done']) {
        $ids = array_keys($draft['draft']['players']);
        $i = array_search($player, $ids);

        $i = ($draft['draft']['order_reversed'])? $i - 1 : $i + 1;

        if($i == -1 || $i >= count($ids)) {
            // we've reached the end of round
            $draft['draft']['order_reversed'] = !$draft['draft']['order_reversed'];

            if($draft['draft']['order_reversed']) {
                $i = count($ids) - 1;
            } else {
                $i = 0;
            }
        }

        $draft['draft']['current'] = $ids[$i];
    }


    save_draft($draft);

    return_data([
        'draft' => $draft,
        'success' => true
    ]);

?>
