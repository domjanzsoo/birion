<?php

namespace App\Models\Enums;

enum HeatingEnum: string {
    case GAS = 'gas';
    case WOOD = 'wood';
    case ELECTRIC = 'electric';

    public static function toArray(): array
    {
        return array_column(HeatingEnum::cases(), 'value');
    } 
}