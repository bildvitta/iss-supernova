<?php

namespace Bildvitta\IssSupernova\Traits;

use Illuminate\Support\Facades\DB;

trait UsesSupernovaDB
{
    public function __construct(array $attributes = [])
    {
        $this->configDbConnection();
        parent::__construct($attributes);
    }

    public static function __callStatic($method, $parameters)
    {
        self::configDbConnection();
        return parent::__callStatic($method, $parameters);
    }

    protected static function configDbConnection()
    {
        config([
            'database.connections.iss-supernova' => [
                'driver' => 'mysql',
                'host' => config('iss-supernova.db.host'),
                'port' => config('iss-supernova.db.port'),
                'database' => config('iss-supernova.db.database'),
                'username' => config('iss-supernova.db.username'),
                'password' => config('iss-supernova.db.password'),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => [],
            ]
        ]);
    }
}
