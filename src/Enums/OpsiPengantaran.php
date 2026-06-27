<?php

declare(strict_types=1);

namespace App\Enums;

enum OpsiPengantaran: string
{
    case ANTAR_JEMPUT = 'ANTAR_JEMPUT';
    case ANTAR_SENDIRI = 'ANTAR_SENDIRI';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::ANTAR_JEMPUT->value => 'Antar-jemput',
            self::ANTAR_SENDIRI->value => 'Antar sendiri',
        ];
    }
}
