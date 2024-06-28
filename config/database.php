<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        /*
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
*/
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('PGSQL_SEARCH_PATH', 'public'),
            'sslmode' => 'prefer',
            'options' => ([
                PDO::ATTR_PERSISTENT => env('PGSQL_ATTR_PERSISTENT', true),
            ]),
        ],
        'medical_db' => [
            'driver' => 'pgsql',
            'url' => env('MEDICAL_DB_URL'),
            'host' => env('MEDICAL_DB_HOST', '127.0.0.1'),
            'port' => env('MEDICAL_DB_PORT', '5432'),
            'database' => env('MEDICAL_DB_DATABASE', 'medical'),
            'username' => env('MEDICAL_DB_USERNAME', 'root'),
            'password' => env('MEDICAL_DB_PASSWORD', ''),
            'charset' => env('MEDICAL_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'sslmode' => 'prefer',
            'options' => ([
                PDO::ATTR_PERSISTENT => env('MEDICAL_DB_ATTR_PERSISTENT', true),
            ]),
        ],
        'chatwoot_db' => [
            'driver' => 'pgsql',
            'url' => env('CHATWOOT_DB_URL'),
            'host' => env('CHATWOOT_DB_HOST', '127.0.0.1'),
            'port' => env('CHATWOOT_DB_PORT', '5432'),
            'database' => env('CHATWOOT_DB_DATABASE', 'chatwoot'),
            'username' => env('CHATWOOT_DB_USERNAME', 'root'),
            'password' => env('CHATWOOT_DB_PASSWORD', ''),
            'charset' => env('CHATWOOT_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'sslmode' => 'prefer',
            'options' => [
                PDO::ATTR_PERSISTENT => env('CHATWOOT_DB_ATTR_PERSISTENT', true),
            ],
        ],

        'stripe_db' => [
            'driver' => 'pgsql',
            'url' => env('STRIPE_DB_URL'),
            'host' => env('STRIPE_DB_HOST', '127.0.0.1'),
            'port' => env('STRIPE_DB_PORT', '5432'),
            'database' => env('STRIPE_DB_DATABASE', 'stripe'),
            'username' => env('STRIPE_DB_USERNAME', 'root'),
            'password' => env('STRIPE_DB_PASSWORD', ''),
            'charset' => env('STRIPE_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'sslmode' => 'prefer',
            'options' => [
                PDO::ATTR_PERSISTENT => env('STRIPE_DB_ATTR_PERSISTENT', true),
            ],
        ],

        'cloudflare_db' => [
            'driver' => 'pgsql',
            'url' => env('CLOUDFLARE_DB_URL'),
            'host' => env('CLOUDFLARE_DB_HOST', '127.0.0.1'),
            'port' => env('CLOUDFLARE_DB_PORT', '5432'),
            'database' => env('CLOUDFLARE_DB_DATABASE', 'cloudflare'),
            'username' => env('CLOUDFLARE_DB_USERNAME', 'root'),
            'password' => env('CLOUDFLARE_DB_PASSWORD', ''),
            'charset' => env('CLOUDFLARE_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'sslmode' => 'prefer',
            'options' => [
                PDO::ATTR_PERSISTENT => env('CLOUDFLARE_DB_ATTR_PERSISTENT', true),
            ],
        ],

        'filament_db' => [
            'driver' => 'pgsql',
            'url' => env('FILAMENT_DB_URL'),
            'host' => env('FILAMENT_DB_HOST', '127.0.0.1'),
            'port' => env('FILAMENT_DB_PORT', '5432'),
            'database' => env('FILAMENT_DB_DATABASE', 'filament'),
            'username' => env('FILAMENT_DB_USERNAME', 'root'),
            'password' => env('FILAMENT_DB_PASSWORD', ''),
            'charset' => env('FILAMENT_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'sslmode' => 'prefer',
            'options' => [
                PDO::ATTR_PERSISTENT => env('FILAMENT_DB_ATTR_PERSISTENT', true),
            ],
        ],
        /*
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],
*/
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
