<?php

namespace App\Draft;

enum PickCategory: string {
    case FACTION = "faction";
    case SLICE = "slice";
    case POSITION = "position";
}