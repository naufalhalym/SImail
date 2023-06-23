<?php

namespace App\Enums;

enum Role
{
    case ADMIN;
    case KETUA;
    case SEKRETARIS;
    case KEPALA;

    public function status(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::KETUA => 'Ketua P3MP',
            self::SEKRETARIS => 'Sekretaris',
            self::KEPALA => 'Ketua Bidang',
        };
    }
}
