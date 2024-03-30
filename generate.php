<?php

use App\Draft;
use App\GeneratorConfig;
require_once 'boot.php';

if (get('regen') != null) {
    $draft = Draft::load(get('regen'));

    if (!$draft->isAdminPass(get('admin'))) return_error('You are not allowed to do this');
    if (!empty($draft->log())) return_error('Draft already in progress');

    $regen_slices = get('shuffle_slices', "false") == "true";
    $regen_factions = get('shuffle_factions', "false") == "true";
    $regen_order = get('shuffle_order', "false") == "true";

    $draft->regenerate($regen_slices, $regen_factions, $regen_order);

    return_data([
        'ok' => true
    ]);
} else {
    $config = new GeneratorConfig(true);
    $draft = Draft::createFromConfig($config);
    $draft->save();
    return_data([
        'id' => $draft->getId(),
        'admin' => $draft->getAdminPass()
    ]);
}
