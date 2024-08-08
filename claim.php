<?php

require_once 'boot.php';

$draft = \App\Draft::load(get('draft'));
$unclaim = get('unclaim') == 1;
$playerId = get('player');

if ($unclaim) {
    // Not enforcing this here yet because it would break for older drafts
    // if (!$draft->isPlayerSecret(playerId, get('secret'))) return_error('You are not allowed to do this!');
    $result = $draft->unclaim($playerId);
} else {
    $result = $draft->claim($playerId);
}

$data = [
    'draft' => $draft,
    'player' => $playerId,
    'success' => $result
];

if ($unclaim == false) {
    $data['secret'] = $draft->getPlayerSecret($playerId);
}

return_data($data);
