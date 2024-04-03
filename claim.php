<?php

require_once 'boot.php';

$draft = \App\Draft::load(get('draft'));
$unclaim = get('unclaim') == 1;
$playerId = get('player');

if ($unclaim) {
    $result = $draft->unclaim($playerId)->save();
} else {
    $result = $draft->claim($playerId)->save();
}

return_data([
    'draft' => $draft,
    'player' => $playerId,
    'success' => $result
]);
