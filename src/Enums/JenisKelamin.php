<?php

declare(strict_types=1);

namespace App\Enums;

enum JenisKelamin: string
{
    case JANTAN = 'JANTAN';
    case BETINA = 'BETINA';

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            self::JANTAN->value => 'Jantan',
            self::BETINA->value => 'Betina',
        ];
    }
}
