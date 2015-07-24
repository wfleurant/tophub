<?php

return [

    /*
    | PDO Fetch Style
    */

    'fetch' => PDO::FETCH_CLASS,

    /*
    | Default Database Connection Name
    */

    // 'default' => env('DB_DEFAULT', 'mysql'),
    'default' => 'mongodb',

    /*
    | Database Connections
    */

    'connections' => [

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => env('NOSQL_HOST', '172.17.42.1'),
            'port'     => env('NOSQL_PORT', '27017'),
            'database' => env('NOSQL_DATABASE', 'tophub-db'),
            'username' => env('NOSQL_USERNAME', ''),
            'password' => env('NOSQL_PASSWORD', ''),
            'options'  => [ 'db' => 'admin' ],
        ],

        'testing' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ],

        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => env('DB_DATABASE', storage_path('database.sqlite')),
            'prefix'   => env('DB_PREFIX', ''),
        ],

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', 'localhost'),
            'port'      => env('DB_PORT', 3306),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => env('DB_PREFIX', ''),
            'timezone'  => env('DB_TIMEZONE','+00:00'),
            'strict'    => false,
        ],

        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', 5432),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => env('DB_PREFIX', ''),
            'schema'   => 'public',
        ],

        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'prefix'   => env('DB_PREFIX', ''),
        ],

    ],

    /*
    | Migration Repository Table
    */

    'migrations' => 'migrations',

    /*
    | Redis Databases
    */

    'redis' => [

        'cluster' => env('REDIS_CLUSTER', false),

        'default' => [
            'host'     => env('REDIS_HOST', '127.0.0.1'),
            'port'     => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 0),
            'password' => env('REDIS_PASSWORD', null)
        ],

    ],

];
