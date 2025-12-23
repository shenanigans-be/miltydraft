<?php

$draft = \App\DeprecatedDraft::load(get('draft'));

if ($draft == null) return_error('draft not found');

return_data([
    'draft' => $draft,
    'success' => true
]);
