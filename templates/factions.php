<?php

$factions = json_decode(file_get_contents('data/factions.json'), true);

function translateSet($set): string
{
    return match($set) {
        'base' => \App\TwilightImperium\Edition::BASE_GAME->value,
        'pok' => \App\TwilightImperium\Edition::PROPHECY_OF_KINGS->value,
        'te' => \App\TwilightImperium\Edition::THUNDERS_EDGE->value,
        'discordant' => \App\TwilightImperium\Edition::DISCORDANT_STARS->value,
        'discordantexp' => \App\TwilightImperium\Edition::DISCORDANT_STARS_PLUS->value,
        'keleres' => \App\TwilightImperium\Edition::THUNDERS_EDGE->value,
    };
}

foreach ($factions as $f) {
    $fact = '<label data-expansion="' . translateSet($f['set']) . '" class="check" for="custom_f_' . $f['id'] . '"><input class="custom_faction" value="' . $f['name'] . '" type="checkbox" id="custom_f_' . $f['id'] . '" name="custom_factions[]" />';
    $fact .= '<img src="' . url('img/factions/ti_' . $f['id'] . '.png') . '" /> ' . $f['name'] . '</label>';
    echo $fact;
}
