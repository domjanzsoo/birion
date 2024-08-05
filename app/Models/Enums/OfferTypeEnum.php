<?php

namespace App\Models\Enums;

enum OfferTypeEnum: string {
    case SALE = 'sale';
    case RENT = 'rent';

    public static function toArray(): array
    {
        return array_column(OfferTypeEnum::cases(), 'value');
    } 
}