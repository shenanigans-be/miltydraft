<?php

require_once 'boot.php';

$draft = \App\Draft::load(get('draft'));

if ($draft == null) return_error('draft not found');

return_data([
    'draft' => $draft,
    'success' => true
]);
