<?php

require_once 'boot.php';

$draft = \App\Draft::load(get('draft'));
$secret = get('secret');

if ($draft->isAdminPass($secret)) {
  return return_data([
      'admin' => $secret,
      'success' => true
  ]);
}

$playerId = $draft->getPlayerIdBySecret($secret);

if (!$playerId) return return_error('No session found with that passkey');

return_data([
    'player' => $playerId,
    'secret' => $secret,
    'success' => true
]);
