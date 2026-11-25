<?php

namespace App\Services;

use Config\Database;
use Throwable;

/**
 * Helper ringan untuk mengecek kesiapan koneksi PostgreSQL dan skema tabel.
 */
class DbHelper
{
    private static ?bool $ready = null;

    public static function isReady(): bool
    {
        if (self::$ready !== null) {
            return self::$ready;
        }

        try {
            $db = Database::connect();
            if ($db->DBDriver !== 'Postgre') {
                self::$ready = false;
                return false;
            }

            $db->initialize();
            self::$ready = $db->tableExists('pengguna');
        } catch (Throwable $e) {
            self::$ready = false;
        }

        return self::$ready;
    }

    public static function resetCache(): void
    {
        self::$ready = null;
    }
}
