<?php

require_once 'boot.php';

$draft = Draft::load(get('draft'));
$unclaim = get('unclaim') == 1;

$p = $draft->getPlayers()[get('player')];

if ($unclaim) {
    if (!$p['claimed']) {
        return_error('Already unclaimed');
    } else {
        $p['claimed'] = false;
        $result = $draft->save();
        return_data([
            'draft' => $draft,
            'player' => $p['id'],
            'success' => $result
        ]);
    }
} else {
    if ($p['claimed']) {
        return_error('Already claimed');
    } else {
        $p['claimed'] = true;
        $result = $draft->save();
        return_data([
            'draft' => $draft,
            'player' => $p['id'],
            'success' => $result
        ]);
    }
}
