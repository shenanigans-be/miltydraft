<?php

require_once 'boot.php';

$id = get('id');
$index = get('index');
$player = get('player');
$category = get('category');
$value = get('value');

$draft = Draft::load($id);

$is_admin = $draft->isAdminPass(get('admin'));
if ($draft == null) return_error('draft not found');
if ($player != $draft->getCurrentPlayer() && !$is_admin) return_error('Not your turn!');

if ($index != count($draft->getLog())) {
    return_error('Draft data out of date, meaning: stuff has been picked or undone while this tab was open.');
}

$draft->pick($player, $category, $value);

return_data([
    'draft' => $draft,
    'success' => true
]);
