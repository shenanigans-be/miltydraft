<?php
    require_once 'boot.php';


    $draft = get_draft(get('draft'));
    $unclaim = get('unclaim') == 1;

    $p = &$draft['draft']['players'][get('player')];

    if($unclaim) {
        if(!$p['claimed']) {
            return_error('Already unclaimed');
        } else {
            $p['claimed'] = false;
            $result = save_draft($draft);
            return_data([
                'draft' => $draft,
                'player' => $p['id'],
                'success' => $result
            ]);
        }
    } else {
        if($p['claimed']) {
            return_error('Already claimed');
        } else {
            $p['claimed'] = true;
            $result = save_draft($draft);
            return_data([
                'draft' => $draft,
                'player' => $p['id'],
                'success' => $result
            ]);
        }
    }
?>
