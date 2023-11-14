<?php

require_once 'boot.php';

$draft = get_draft(get('draft'));

$is_admin = ($draft['admin_pass'] == get('admin'));

if (!$is_admin) return_error("Only the admin can undo");

$log = &$draft['draft']['log'];

if (!count($log)) return_error("Nothing to undo");

$last_log = array_pop($log);

$draft['draft']["players"][$last_log['player']][$last_log['category']] = null;
$draft['draft']['current'] = $last_log['player'];

if ($draft['draft']['index'] % count($draft['draft']["players"]) == 0) {
    $draft['draft']['order_reversed'] = !$draft['draft']['order_reversed'];
}

$draft['draft']['index']--;
$draft['done'] = false;

save_draft($draft);

return_data([
    'draft' => $draft,
    'success' => true
]);
