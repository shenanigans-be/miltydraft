<?php

require_once 'boot.php';

$id = get('id');
$index = get('index');
$player = get('player');
$category = get('category');
$value = get('value');

$draft = \App\Draft::load($id);

$is_admin = $draft->isAdminPass(get('admin'));
if ($draft == null) return_error('draft not found');
if ($player != $draft->currentPlayer() && !$is_admin) return_error('Not your turn!');

// Not enforcing this here yet because it would break for older drafts
// if (!$is_admin && !$draft->isPlayerSecret($player, get('secret'))) return_error('You are not allowed to do this!');

if ($index != count($draft->log())) {
    return_error('Draft data out of date, meaning: stuff has been picked or undone while this tab was open.');
}

$draft->pick($player, $category, $value);

return_data([
    'draft' => $draft,
    'success' => true
]);
