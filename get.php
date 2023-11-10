<?php
    require_once 'boot.php';

    $id = get('id');
    $draft = get_draft($id);

    return_data([
        'draft' => $draft,
        'success' => true
    ]);
?>
