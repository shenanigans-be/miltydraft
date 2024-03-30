<?php

require_once 'boot.php';

$draft = \App\Draft::load(get('draft'));

$is_admin = $draft->isAdminPass(get('admin'));

if (!$is_admin) return_error("Only the admin can undo");
if (!count($draft->log())) return_error("Nothing to undo");

$draft->undoLastAction();

return_data([
    'draft' => $draft,
    'success' => true
]);
